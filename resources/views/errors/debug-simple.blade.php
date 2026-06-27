<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Error</title>
    <style>
        body { margin: 0; background: #111827; color: #f9fafb; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .wrap { max-width: 1180px; margin: 0 auto; padding: 32px 20px; }
        .panel { background: #1f2937; border: 1px solid #374151; border-radius: 14px; overflow: hidden; }
        .head { background: #b91c1c; padding: 18px 22px; }
        .head h1 { margin: 0; font-size: 20px; }
        .body { padding: 22px; }
        .muted { color: #d1d5db; }
        .code { background: #030712; border: 1px solid #374151; border-radius: 10px; padding: 14px; overflow: auto; white-space: pre-wrap; }
        .row { margin-top: 14px; }
        .label { color: #93c5fd; font-weight: 700; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="panel">
            <div class="head">
                <h1>{{ get_class($exception) }}</h1>
            </div>
            <div class="body">
                <div class="row">
                    <div class="label">Message</div>
                    <div class="code">{{ $exception->getMessage() ?: 'No exception message.' }}</div>
                </div>
                <div class="row">
                    <div class="label">Location</div>
                    <div class="muted">{{ $exception->getFile() }}:{{ $exception->getLine() }}</div>
                </div>
                <div class="row">
                    <div class="label">Request</div>
                    <div class="muted">{{ request()->method() }} {{ request()->fullUrl() }}</div>
                </div>
                <div class="row">
                    <div class="label">Trace</div>
                    <div class="code">{{ collect($exception->getTrace())->take(18)->map(function ($frame, $index) {
                        $file = $frame['file'] ?? '[internal]';
                        $line = $frame['line'] ?? '-';
                        $class = $frame['class'] ?? '';
                        $type = $frame['type'] ?? '';
                        $function = $frame['function'] ?? '';

                        return '#' . $index . ' ' . $file . ':' . $line . ' ' . $class . $type . $function . '()';
                    })->implode("\n") }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
