@if ($fontsUrl)
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontsUrl }}" rel="stylesheet">
@endif
@if ($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}">
@endif
@if ($customHead)
    {!! $customHead !!}
@endif
<style id="boa-theme-vars">{!! $css !!}</style>
@if ($bridgeCss !== '')
    <style id="boa-theme-bridge">{!! $bridgeCss !!}</style>
@endif
@if ($customCss !== '')
    <style id="boa-theme-custom">{!! $customCss !!}</style>
@endif
<script>
    (function () {
        var root = document.documentElement;
        var mode = @json($colorMode);
        root.classList.remove('boa-theme-dark', 'dark');
        if (mode === 'dark') {
            root.classList.add('boa-theme-dark', 'dark');
        } else if (mode === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            root.classList.add('boa-theme-dark', 'dark');
        }
        @if ($bodyClass !== '')
            @json(explode(' ', $bodyClass)).forEach(function (c) { if (c) root.classList.add(c); });
        @endif
    })();
</script>
@if ($customJavascript !== '')
    <script id="boa-theme-custom-js">{!! $customJavascript !!}</script>
@endif
