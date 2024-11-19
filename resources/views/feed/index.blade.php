<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0">
    <channel>
        <title>ProjectSave International Blog</title>
        <link>{{ url('/') }}</link>
        <description>Latest updates and teachings from ProjectSave International Ministry</description>
        <language>en-us</language>
        @foreach($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('posts.show', $post) }}</link>
                <description>{{ $post->excerpt }}</description>
                <pubDate>{{ $post->created_at->toRssString() }}</pubDate>
                <guid>{{ route('posts.show', $post) }}</guid>
            </item>
        @endforeach
    </channel>
</rss>
