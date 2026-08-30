<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #18181b;">
    <div style="max-width: 600px; margin: 0 auto; padding: 32px 24px;">
        <div style="background-color: #ffffff; border-radius: 12px; padding: 32px; line-height: 1.6;">
            {!! $bodyHtml !!}
        </div>
        <p style="margin-top: 24px; text-align: center; font-size: 12px; color: #71717a;">
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
