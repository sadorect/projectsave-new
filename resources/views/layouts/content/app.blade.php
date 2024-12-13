<!DOCTYPE html>
<html>
<head>
    <!-- Same head content as admin layout -->
</head>
<body>
    <div class="d-flex">
        @include('layouts.content.sidebar')
        <main class="flex-grow-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
