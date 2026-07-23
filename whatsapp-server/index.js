const express = require('express');
const axios = require('axios');
const path = require('path');
const fs = require('fs');
const pino = require('pino');
const QRCode = require('qrcode');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
    Browsers,
    delay,
} = require('@whiskeysockets/baileys');

const app = express();
const port = Number(process.env.PORT || 3001);
const host = process.env.HOST || '127.0.0.1';
const apiKey = process.env.WHATSAPP_GATEWAY_API_KEY || '';
const laravelBaseUrl = (process.env.LARAVEL_BASE_URL || 'http://127.0.0.1')
    .replace(/\/+$/, '');
const authRoot = path.join(__dirname, 'sessions-auth');
const logger = pino({ level: process.env.LOG_LEVEL || 'info' });

const activeSessions = new Map();
const sessionQrs = new Map();
const connectingSessions = new Map();
const reconnectAttempts = new Map();
const fallbackWaVersion = [2, 3000, 1032141294];

app.use(express.json({ limit: '1mb' }));

function requireApiKey(req, res, next) {
    if (!apiKey) {
        return next();
    }

    const token = req.get('authorization')?.replace(/^Bearer\s+/i, '');
    if (token !== apiKey) {
        return res.status(401).json({
            success: false,
            message: 'API key WhatsApp Gateway tidak valid.',
        });
    }

    return next();
}

app.use('/api', requireApiKey);

function sanitizeSessionId(sessionId) {
    const normalized = String(sessionId || '').trim();
    if (!/^[a-zA-Z0-9_-]{3,100}$/.test(normalized)) {
        throw new Error('Session ID tidak valid.');
    }
    return normalized;
}

function getAuthPath(sessionId) {
    return path.join(authRoot, sanitizeSessionId(sessionId));
}

function extractDisconnectCode(error) {
    return error?.output?.statusCode
        || error?.data?.statusCode
        || error?.statusCode;
}

async function notifyLaravel(sessionId, payload) {
    const webhookUrl = `${laravelBaseUrl}/api/whatsapp/webhook/${encodeURIComponent(sessionId)}`;

    try {
        await axios.post(webhookUrl, payload, {
            timeout: 10000,
            headers: {
                'Content-Type': 'application/json',
                ...(apiKey ? { Authorization: `Bearer ${apiKey}` } : {}),
            },
        });
    } catch (error) {
        logger.error(
            { sessionId, error: error.message },
            'Gagal mengirim webhook status ke Laravel',
        );
    }
}

async function resolveWaVersion() {
    try {
        const result = await fetchLatestBaileysVersion();
        logger.info(
            { version: result.version.join('.'), isLatest: result.isLatest },
            'Menggunakan versi WhatsApp Web terbaru',
        );
        return result.version;
    } catch (error) {
        logger.warn(
            { error: error.message, version: fallbackWaVersion.join('.') },
            'Gagal mengambil versi WhatsApp Web, menggunakan fallback',
        );
        return fallbackWaVersion;
    }
}

async function createSession(sessionId) {
    sessionId = sanitizeSessionId(sessionId);

    if (activeSessions.has(sessionId)) {
        return activeSessions.get(sessionId);
    }
    if (connectingSessions.has(sessionId)) {
        return connectingSessions.get(sessionId);
    }

    const connecting = (async () => {
        const authPath = getAuthPath(sessionId);
        const { state, saveCreds } = await useMultiFileAuthState(authPath);
        const version = await resolveWaVersion();
        const socket = makeWASocket({
            version,
            auth: state,
            browser: Browsers.ubuntu('Chrome'),
            printQRInTerminal: false,
            logger: pino({ level: 'silent' }),
            markOnlineOnConnect: false,
            syncFullHistory: false,
            qrTimeout: 60000,
            connectTimeoutMs: 60000,
        });

        activeSessions.set(sessionId, socket);
        socket.ev.on('creds.update', saveCreds);
        socket.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                reconnectAttempts.delete(sessionId);
                const qrDataUrl = await QRCode.toDataURL(qr, {
                    width: 320,
                    margin: 2,
                    errorCorrectionLevel: 'M',
                });
                sessionQrs.set(sessionId, qrDataUrl);
                await notifyLaravel(sessionId, {
                    status: 'qr_ready',
                    qr_code_data: qrDataUrl,
                });
            }

            if (connection === 'open') {
                reconnectAttempts.delete(sessionId);
                sessionQrs.delete(sessionId);
                const jid = socket.user?.id || '';
                const phoneNumber = jid.split(':')[0].split('@')[0] || null;
                logger.info({ sessionId, phoneNumber }, 'Sesi WhatsApp terhubung');
                await notifyLaravel(sessionId, {
                    status: 'connected',
                    qr_code_data: null,
                    phone_number: phoneNumber,
                });
            }

            if (connection === 'close') {
                activeSessions.delete(sessionId);
                sessionQrs.delete(sessionId);

                const code = extractDisconnectCode(lastDisconnect?.error);
                const loggedOut = code === DisconnectReason.loggedOut;
                const attempts = (reconnectAttempts.get(sessionId) || 0) + 1;
                reconnectAttempts.set(sessionId, attempts);
                logger.warn(
                    { sessionId, code, loggedOut, attempts },
                    'Koneksi WhatsApp tertutup',
                );

                if (loggedOut) {
                    reconnectAttempts.delete(sessionId);
                    fs.rmSync(authPath, { recursive: true, force: true });
                    await notifyLaravel(sessionId, {
                        status: 'disconnected',
                        qr_code_data: null,
                        error_message: 'Sesi keluar dari WhatsApp. Hubungkan ulang perangkat.',
                    });
                    return;
                }

                if (code === 405 && attempts >= 3) {
                    reconnectAttempts.delete(sessionId);
                    if (!state.creds.registered) {
                        fs.rmSync(authPath, { recursive: true, force: true });
                    }
                    await notifyLaravel(sessionId, {
                        status: 'disconnected',
                        qr_code_data: null,
                        error_message: 'WhatsApp menolak proses pairing (kode 405). Perbarui engine lalu coba hubungkan ulang.',
                    });
                    return;
                }

                await delay(3000);
                createSession(sessionId).catch((error) => {
                    logger.error(
                        { sessionId, error: error.message },
                        'Gagal menghubungkan ulang sesi WhatsApp',
                    );
                });
            }
        });

        return socket;
    })();

    connectingSessions.set(sessionId, connecting);
    try {
        return await connecting;
    } finally {
        connectingSessions.delete(sessionId);
    }
}

app.get('/health', (req, res) => {
    res.json({
        success: true,
        service: 'sisfo-whatsapp-gateway',
        active_sessions: activeSessions.size,
    });
});

app.post('/api/connect', async (req, res) => {
    try {
        const session = sanitizeSessionId(req.body.session);
        reconnectAttempts.delete(session);
        await createSession(session);
        return res.json({
            success: true,
            message: 'Inisialisasi sesi WhatsApp dimulai.',
            status: sessionQrs.has(session) ? 'qr_ready' : 'connecting',
        });
    } catch (error) {
        logger.error({ error: error.message }, 'Gagal membuat sesi WhatsApp');
        return res.status(500).json({ success: false, message: error.message });
    }
});

app.post('/api/disconnect', async (req, res) => {
    try {
        const session = sanitizeSessionId(req.body.session);
        const socket = activeSessions.get(session);
        if (socket) {
            await socket.logout();
        }
        activeSessions.delete(session);
        sessionQrs.delete(session);
        fs.rmSync(getAuthPath(session), { recursive: true, force: true });
        await notifyLaravel(session, {
            status: 'disconnected',
            qr_code_data: null,
        });

        return res.json({
            success: true,
            message: `Sesi ${session} berhasil diputuskan.`,
        });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});

app.post('/api/send', async (req, res) => {
    try {
        const session = sanitizeSessionId(req.body.session);
        const message = String(req.body.message || '').trim();
        let target = String(req.body.to || '').replace(/\D/g, '');

        if (!target || !message) {
            return res.status(422).json({
                success: false,
                message: 'Nomor tujuan dan pesan wajib diisi.',
            });
        }
        if (target.startsWith('0')) {
            target = `62${target.slice(1)}`;
        }

        let socket = activeSessions.get(session);
        if (!socket && fs.existsSync(getAuthPath(session))) {
            socket = await createSession(session);
            await delay(1500);
        }
        if (!socket?.user) {
            return res.status(409).json({
                success: false,
                message: 'Sesi WhatsApp belum terhubung. Scan QR terlebih dahulu.',
            });
        }

        const jid = `${target}@s.whatsapp.net`;
        const result = await socket.sendMessage(jid, { text: message });
        return res.json({
            success: true,
            message: 'Pesan berhasil dikirim.',
            data: { message_id: result?.key?.id || null },
        });
    } catch (error) {
        logger.error({ error: error.message }, 'Gagal mengirim pesan WhatsApp');
        return res.status(500).json({ success: false, message: error.message });
    }
});

app.get('/api/status', (req, res) => {
    try {
        const session = sanitizeSessionId(req.query.session);
        const socket = activeSessions.get(session);
        let status = 'disconnected';

        if (socket?.user) {
            status = 'connected';
        } else if (sessionQrs.has(session)) {
            status = 'qr_ready';
        } else if (socket || connectingSessions.has(session)) {
            status = 'connecting';
        }

        return res.json({
            success: true,
            session,
            status,
            qr_code_data: sessionQrs.get(session) || null,
        });
    } catch (error) {
        return res.status(422).json({ success: false, message: error.message });
    }
});

async function restoreSessions() {
    fs.mkdirSync(authRoot, { recursive: true });
    const sessionIds = fs.readdirSync(authRoot, { withFileTypes: true })
        .filter((entry) => entry.isDirectory())
        .map((entry) => entry.name);

    for (const sessionId of sessionIds) {
        createSession(sessionId).catch((error) => {
            logger.error(
                { sessionId, error: error.message },
                'Gagal memulihkan sesi WhatsApp',
            );
        });
    }
}

app.listen(port, host, async () => {
    logger.info({ host, port }, 'WhatsApp Gateway siap');
    await restoreSessions();
});
