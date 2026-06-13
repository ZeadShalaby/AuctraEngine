@props([
    'title' => 'Auctra',
    'src' => asset('storage/images/logo.png'),
    'href' => route('landing-pages.index'),
    'style' => 'color: black; font-weight: 600;'
])

<style>
    .logo-img {
        width: 60px;
        height: auto;
    }
</style>

<a href="{{ $href }}" class="navbar-brand m-0 d-xl-flex d-none align-items-center gap-2">
    <img class="logo-img" src="{{ $src }}">
    
    <h4 class="logo-title m-0" style="{{ $style }}">
        {{ $title }}
    </h4>
</a>