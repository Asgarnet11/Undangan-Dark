@props([
    'name',
    'class' => '',
])
@php
    $safeName = preg_replace('/[^a-z0-9-]/', '', (string) $name);
    $path = public_path("themes/minimalist-botanical/svg/{$safeName}.svg");
    $svg = '';

    if ($safeName !== '' && is_file($path)) {
        $svg = trim((string) file_get_contents($path));
        if ($class !== '') {
            $escapedClass = e($class);
            if (preg_match('/<svg\b[^>]*\bclass=/i', $svg)) {
                $svg = (string) preg_replace('/(<svg\b[^>]*\bclass=["\'])([^"\']*)/i', '$1$2 ' . $escapedClass, $svg, 1);
            } else {
                $svg = (string) preg_replace('/<svg\b/i', '<svg class="' . $escapedClass . '"', $svg, 1);
            }
        }
    }
@endphp
{!! $svg !!}
