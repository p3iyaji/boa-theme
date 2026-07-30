<div {{ $attributes->class([
    'flex min-w-0 items-center',
    'flex-col gap-3 text-center' => $orientation === 'vertical',
    'gap-3' => $orientation !== 'vertical',
]) }}>
    <x-boa-theme::mark :size="$size" />
    <div @class([
        'min-w-0',
        'text-left' => $orientation !== 'vertical',
    ])>
        <span @class([
            'font-display font-semibold tracking-wide text-brand-100',
            'text-base' => $size === 'sm',
            'text-lg' => $size === 'md',
            'text-3xl' => $size === 'lg',
            'text-4xl' => $size === 'xl',
        ])>{{ $brandName }}</span>
        @if ($showTagline && $brandTagline !== '')
            <p class="text-[0.62rem] font-medium uppercase tracking-[0.18em] text-brand-300/90">{{ $brandTagline }}</p>
        @endif
    </div>
</div>
