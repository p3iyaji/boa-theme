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
@if ($customCss !== '')
    <style id="boa-theme-custom">{!! $customCss !!}</style>
@endif
@if ($bodyClass !== '')
    <script>
        document.documentElement.classList.add(...@json(explode(' ', $bodyClass)));
    </script>
@endif
@if ($customJavascript !== '')
    <script id="boa-theme-custom-js">{!! $customJavascript !!}</script>
@endif
