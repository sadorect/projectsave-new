@props([
    'pageTitle' => 'Student Workspace',
    'subtitle' => null,
    'title' => null,
])

@php
    $documentTitle = $title ?? ($pageTitle . ' - ' . config('app.name', 'Projectsave International'));
@endphp

<x-layouts.lms
    :title="$documentTitle"
    :page-title="$pageTitle"
    :subtitle="$subtitle"
    eyebrow="Student Workspace"
    :show-sidebar="true"
>
    {{ $slot }}
</x-layouts.lms>
