<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex">
        @include('admin.layouts.sidebar')
        
        <main class="flex-grow-1">
            @include('admin.layouts.header')
            <div class="container-fluid py-4">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
