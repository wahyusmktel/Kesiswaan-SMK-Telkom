module.exports = {
    apps: [
        {
            name: 'sisfo-whatsapp-gateway',
            script: 'index.js',
            cwd: __dirname,
            instances: 1,
            exec_mode: 'fork',
            autorestart: true,
            watch: false,
            max_memory_restart: '500M',
            restart_delay: 5000,
            kill_timeout: 10000,
            time: true,
            env: {
                NODE_ENV: 'production',
                HOST: '127.0.0.1',
                PORT: 3001,
                LOG_LEVEL: 'info',
                LARAVEL_BASE_URL: process.env.LARAVEL_BASE_URL
                    || 'https://sisfo.smktelkom-lpg.id',
                WHATSAPP_GATEWAY_API_KEY: process.env.WHATSAPP_GATEWAY_API_KEY,
            },
        },
    ],
};
