<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    {!! $content !!}

    @if(! empty($customMessage))
        <hr>
        {!! $customMessage !!}
    @endif
</body>
</html>
