@props(['name'])
<svg {{ $attributes->merge(['class' => 'icon']) }} aria-hidden="true"><use href="#icon-{{ $name }}" /></svg>
