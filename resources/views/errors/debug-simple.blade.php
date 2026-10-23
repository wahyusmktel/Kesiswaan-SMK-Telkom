<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error: {{ class_basename($exception) }} | SISFO Debug</title>
    <style>
        :root {
            --bg: #0b0f19;
            --surface: #111827;
            --surface-card: #1e293b;
            --surface-hover: #273549;
            --border: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --danger-border: #dc2626;
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.12);
            --accent: #38bdf8;
            --accent-bg: rgba(56, 189, 248, 0.12);
            --success: #10b981;
            --code-bg: #070b14;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            padding-bottom: 60px;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 24px 20px;
        }

        /* Top Bar */
        .top-nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--accent);
        }

        .brand svg { width: 20px; height: 20px; fill: currentColor; }

        .sys-badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 9999px;
            font-weight: 600;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background: var(--surface-card);
            border: 1px solid var(--border);
            color: var(--text-muted);
        }

        .badge.danger {
            background: var(--danger-bg);
            border-color: var(--danger-border);
            color: #fca5a5;
        }

        .badge.warning {
            background: var(--warning-bg);
            border-color: #d97706;
            color: #fcd34d;
        }

        .badge.accent {
            background: var(--accent-bg);
            border-color: #0284c7;
            color: #7dd3fc;
        }

        /* Main Hero Card */
        .hero {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
            margin-bottom: 24px;
        }

        .hero-head {
            background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);
            padding: 20px 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-title {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
            word-break: break-all;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            text-decoration: none;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-solid {
            background: #ffffff;
            color: #991b1b;
            font-weight: 700;
        }

        .btn-solid:hover {
            background: #f1f5f9;
            color: #7f1d1d;
        }

        .btn-card {
            background: var(--surface-card);
            border-color: var(--border);
            color: var(--text-main);
            font-size: 12px;
            padding: 4px 10px;
        }

        .btn-card:hover {
            background: var(--surface-hover);
            border-color: var(--accent);
            color: var(--accent);
        }

        /* Body & Sections */
        .hero-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .code-box {
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 14px;
            color: #fca5a5;
            overflow-x: auto;
            position: relative;
            line-height: 1.6;
            word-break: break-word;
        }

        .code-box.neutral {
            color: var(--text-main);
        }

        .file-location {
            background: var(--surface-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 13px;
        }

        .file-path {
            color: #38bdf8;
            font-weight: 600;
            word-break: break-all;
        }

        .file-line {
            background: #0284c7;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
            white-space: nowrap;
        }

        /* Grid for details */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 16px;
        }

        .info-card {
            background: var(--surface-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
        }

        .info-card-header {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .req-url {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 13px;
            word-break: break-all;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .method-badge {
            background: #2563eb;
            color: #ffffff;
            font-weight: 800;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 4px;
        }

        /* Stack Trace Panel */
        .trace-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 24px;
        }

        .trace-head {
            background: var(--surface-card);
            border-bottom: 1px solid var(--border);
            padding: 16px 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .trace-title {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trace-filters {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trace-list {
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 600px;
            overflow-y: auto;
        }

        .trace-item {
            background: var(--surface-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12.5px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            transition: border-color 0.15s ease;
        }

        .trace-item:hover {
            border-color: var(--accent);
        }

        .trace-item.is-app {
            border-left: 3px solid #38bdf8;
            background: rgba(30, 41, 59, 0.9);
        }

        .trace-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
            overflow: hidden;
        }

        .trace-func {
            color: #f1f5f9;
            font-weight: 600;
            word-break: break-all;
        }

        .trace-file {
            color: var(--text-muted);
            font-size: 11.5px;
            word-break: break-all;
        }

        .trace-index {
            color: #64748b;
            font-weight: 700;
            min-width: 28px;
        }

        .tag-app {
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .tag-vendor {
            background: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Toast */
        #toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #10b981;
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 18px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
            opacity: 0;
            pointer-events: none;
            transform: translateY(10px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    @php
        $exceptionClass = get_class($exception);
        $exceptionBasename = class_basename($exception);
        $message = $exception->getMessage() ?: 'No exception message provided.';
        $file = $exception->getFile();
        $line = $exception->getLine();
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $traces = collect($exception->getTrace());
    @endphp

    <div class="container">
        <!-- Top Navigation & Environment Header -->
        <div class="top-nav">
            <div class="brand">
                <svg viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
                SISFO Error Inspector
            </div>
            <div class="sys-badges">
                <span class="badge danger">HTTP {{ $statusCode }}</span>
                <span class="badge warning">PHP {{ PHP_VERSION }}</span>
                <span class="badge accent">Laravel {{ app()->version() }}</span>
                <span class="badge">{{ config('app.env') }}</span>
                <span class="badge">{{ now()->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        <!-- Main Hero Card -->
        <div class="hero">
            <div class="hero-head">
                <div class="hero-title">
                    <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $exceptionClass }}</span>
                </div>
                <div class="hero-actions">
                    <button type="button" class="btn btn-solid" onclick="copyErrorSummary()">
                        📋 Salin Ringkasan Error
                    </button>
                    <button type="button" class="btn" onclick="copyFullTrace()">
                        📑 Salin Full Trace
                    </button>
                    <a href="javascript:history.back()" class="btn">
                        ⬅️ Kembali
                    </a>
                    <button type="button" class="btn" onclick="location.reload()">
                        🔄 Muat Ulang
                    </button>
                </div>
            </div>

            <div class="hero-body">
                <!-- Message Box -->
                <div>
                    <div class="section-header">
                        <span class="section-title">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            Pesan Kesalahan (Exception Message)
                        </span>
                        <button type="button" class="btn btn-card" onclick="copyText('message-box')">Salin Pesan</button>
                    </div>
                    <div class="code-box" id="message-box">{{ $message }}</div>
                </div>

                <!-- Location Box -->
                <div>
                    <div class="section-header">
                        <span class="section-title">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Lokasi Berkas & Baris Kode (Location)
                        </span>
                        <button type="button" class="btn btn-card" onclick="copyText('location-path')">Salin Path</button>
                    </div>
                    <div class="file-location" id="location-box">
                        <span class="file-path" id="location-path">{{ $file }}:{{ $line }}</span>
                        <span class="file-line">Baris {{ $line }}</span>
                    </div>
                </div>

                <!-- Request Context Details Grid -->
                <div class="details-grid">
                    <div class="info-card">
                        <div class="info-card-header">
                            <span>Request Details</span>
                            <button type="button" class="btn btn-card" onclick="copyText('request-url')">Salin URL</button>
                        </div>
                        <div class="req-url">
                            <span class="method-badge">{{ request()->method() }}</span>
                            <span id="request-url">{{ request()->fullUrl() }}</span>
                        </div>
                        @if(request()->query())
                            <div style="margin-top: 10px; font-size: 11px; color: var(--text-muted);">
                                <strong>Query Parameters:</strong>
                                <pre style="margin: 4px 0 0 0; background: var(--code-bg); padding: 8px; border-radius: 6px; overflow: auto; color: #7dd3fc;">{{ json_encode(request()->query(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <span>User & Client Context</span>
                        </div>
                        <div style="font-size: 13px; display: flex; flex-direction: column; gap: 6px;">
                            <div>
                                <span style="color: var(--text-muted);">User:</span>
                                <strong>{{ auth()->check() ? auth()->user()->name . ' (' . (auth()->user()->roles->pluck('name')->implode(', ') ?: 'No Role') . ')' : 'Guest / Tidak Login' }}</strong>
                            </div>
                            <div>
                                <span style="color: var(--text-muted);">Client IP:</span>
                                <code>{{ request()->ip() }}</code>
                            </div>
                            <div>
                                <span style="color: var(--text-muted);">User-Agent:</span>
                                <span style="font-size: 11px; color: #cbd5e1; word-break: break-all;">{{ request()->userAgent() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stack Trace Section -->
        <div class="trace-card">
            <div class="trace-head">
                <h3 class="trace-title">
                    <svg style="width: 18px; height: 18px; color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Stack Trace ({{ $traces->count() }} Frames)
                </h3>
                <div class="trace-filters">
                    <button type="button" class="btn btn-card" id="btn-filter-app" onclick="toggleAppFilter()">
                        Hanya Kode Aplikasi (App)
                    </button>
                    <button type="button" class="btn btn-card" onclick="copyFullTrace()">
                        Salin Semua Trace
                    </button>
                </div>
            </div>

            <div class="trace-list" id="trace-container">
                @foreach($traces as $index => $frame)
                    @php
                        $frameFile = $frame['file'] ?? '[internal]';
                        $frameLine = $frame['line'] ?? '-';
                        $class = $frame['class'] ?? '';
                        $type = $frame['type'] ?? '';
                        $func = $frame['function'] ?? '';
                        $isApp = !str_contains($frameFile, 'vendor/') && !str_contains($frameFile, '[internal]');
                    @endphp
                    <div class="trace-item {{ $isApp ? 'is-app' : 'is-vendor' }}">
                        <span class="trace-index">#{{ $index }}</span>
                        <div class="trace-content" style="flex: 1;">
                            <div class="trace-func">
                                {{ $class }}{{ $type }}{{ $func }}()
                                @if($isApp)
                                    <span class="tag-app">APP CODE</span>
                                @else
                                    <span class="tag-vendor">VENDOR</span>
                                @endif
                            </div>
                            <div class="trace-file">{{ $frameFile }}:{{ $frameLine }}</div>
                        </div>
                        <button type="button" class="btn btn-card" style="padding: 2px 7px; font-size: 11px;" onclick="copyString('{{ addslashes($frameFile . ':' . $frameLine . ' ' . $class . $type . $func . '()') }}')">
                            Salin
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast">
        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span id="toast-msg">Teks berhasil disalin ke clipboard!</span>
    </div>

    <script>
        let onlyApp = false;

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').innerText = msg || 'Teks berhasil disalin ke clipboard!';
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }

        function copyString(str, msg) {
            navigator.clipboard.writeText(str).then(() => {
                showToast(msg || 'Disalin ke clipboard!');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        function copyText(elementId, msg) {
            const el = document.getElementById(elementId);
            if (el) {
                copyString(el.innerText, msg);
            }
        }

        function copyErrorSummary() {
            const summary = [
                '🚨 *SISFO APPLICATION ERROR REPORT*',
                '──────────────────────────────',
                '*Exception*: {{ $exceptionClass }}',
                '*Message*: {{ str_replace(["\r", "\n"], " ", addslashes($message)) }}',
                '*Location*: {{ $file }}:{{ $line }}',
                '*Request*: {{ request()->method() }} {{ request()->fullUrl() }}',
                '*User*: {{ auth()->check() ? auth()->user()->name : "Guest" }}',
                '*Time*: {{ now()->toDateTimeString() }}',
                '──────────────────────────────',
                '*Top Trace*:',
                @foreach($traces->take(8) as $idx => $f)
                '#{{ $idx }} {{ $f["file"] ?? "[internal]" }}:{{ $f["line"] ?? "-" }} {{ ($f["class"] ?? "") . ($f["type"] ?? "") . ($f["function"] ?? "") }}()',
                @endforeach
            ].join('\n');

            copyString(summary, 'Ringkasan error siap ditempel!');
        }

        function copyFullTrace() {
            const traceText = [
                'Full Stack Trace for {{ $exceptionClass }}:',
                @foreach($traces as $idx => $f)
                '#{{ $idx }} {{ $f["file"] ?? "[internal]" }}:{{ $f["line"] ?? "-" }} {{ ($f["class"] ?? "") . ($f["type"] ?? "") . ($f["function"] ?? "") }}()',
                @endforeach
            ].join('\n');

            copyString(traceText, 'Full Stack Trace berhasil disalin!');
        }

        function toggleAppFilter() {
            onlyApp = !onlyApp;
            const btn = document.getElementById('btn-filter-app');
            const items = document.querySelectorAll('.trace-item.is-vendor');
            
            if (onlyApp) {
                btn.style.borderColor = 'var(--accent)';
                btn.style.color = 'var(--accent)';
                btn.innerText = 'Tampilkan Semua Trace';
                items.forEach(el => el.style.display = 'none');
            } else {
                btn.style.borderColor = '';
                btn.style.color = '';
                btn.innerText = 'Hanya Kode Aplikasi (App)';
                items.forEach(el => el.style.display = 'flex');
            }
        }
    </script>
</body>
</html>
