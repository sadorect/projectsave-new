<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $post->title }}</title>
</head>
<body style="margin:0;background:#f8fafc;color:#0f172a;font-family:Arial,sans-serif;">
    <div style="max-width:720px;margin:0 auto;padding:32px 20px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;">
            <div style="padding:28px 28px 8px;">
                <p style="margin:0 0 12px;color:#b45309;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">New devotional</p>
                <h1 style="margin:0 0 12px;font-size:30px;line-height:1.2;">{{ $post->title }}</h1>
                <p style="margin:0 0 18px;color:#64748b;font-size:14px;">
                    {{ optional($post->published_at)->format('F d, Y') }}@if($post->author) · {{ $post->author }}@endif
                </p>
                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="display:block;width:100%;height:auto;border-radius:14px;margin:0 0 22px;">
                @endif
                @if($post->subtitle)
                    <p style="margin:0 0 18px;font-size:18px;line-height:1.6;color:#1e293b;">{{ $post->subtitle }}</p>
                @endif
                @if($post->bible_text)
                    <div style="margin:0 0 20px;padding:16px 18px;background:#fff7ed;border-left:4px solid #ea580c;border-radius:12px;">
                        {!! $post->bible_text !!}
                    </div>
                @endif
                <div style="font-size:16px;line-height:1.8;color:#0f172a;">
                    {!! $post->details !!}
                </div>
                @if($post->action_point)
                    <div style="margin-top:24px;padding:16px 18px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                        <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#b91c1c;">Action point</p>
                        <div style="font-size:15px;line-height:1.7;color:#1f2937;">
                            {!! $post->action_point !!}
                        </div>
                    </div>
                @endif
                <div style="margin:28px 0 6px;">
                    <a href="{{ route('posts.show', $post->slug) }}" style="display:inline-block;padding:12px 18px;background:#b91c1c;border-radius:999px;color:#ffffff;text-decoration:none;font-weight:700;">
                        Read on the website
                    </a>
                </div>
            </div>
            <div style="padding:18px 28px 28px;color:#64748b;font-size:13px;border-top:1px solid #e2e8f0;">
                <p style="margin:0 0 8px;">You are receiving this because you subscribed to devotional updates from Projectsave International.</p>
                <p style="margin:0;">
                    <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}" style="color:#64748b;">Unsubscribe</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
