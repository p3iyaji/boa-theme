<x-boa-theme::settings-layout title="Theme Settings">
    <header class="boa-header">
        <div>
            <h1>Theme Settings</h1>
            <p>Customise brand colours, typography, and appearance. Changes apply after you save.</p>
        </div>
        <div class="boa-actions">
            <span class="boa-btn boa-btn-ghost boa-unsaved" data-boa-unsaved aria-live="polite">Unsaved changes</span>
            @if (! empty($features['import_export']))
                <a class="boa-btn" href="{{ route($routePrefix.'export') }}">Export JSON</a>
            @endif
            <form method="post" action="{{ route($routePrefix.'reset') }}" class="inline">
                @csrf
                <button type="submit" class="boa-btn boa-btn-danger" data-boa-confirm="Reset all theme settings to package defaults? This cannot be undone.">Reset all</button>
            </form>
        </div>
    </header>

    @if (session('boa-theme.status'))
        <div class="boa-alert boa-alert-success" role="status">{{ session('boa-theme.status') }}</div>
    @endif

    @if ($errors->any())
        <div class="boa-alert boa-alert-error" role="alert">
            <strong>Could not save settings.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="boa-theme-settings-form" method="post" action="{{ route($routePrefix.'update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="boa-layout">
            <nav class="boa-nav" role="tablist" aria-label="Settings sections">
                <button type="button" role="tab" data-boa-tab="general" aria-selected="true">General</button>
                <button type="button" role="tab" data-boa-tab="brand" aria-selected="false">Brand</button>
                <button type="button" role="tab" data-boa-tab="typography" aria-selected="false">Typography</button>
                <button type="button" role="tab" data-boa-tab="components" aria-selected="false">Components</button>
                @if ($canCustomCode && (! empty($features['custom_css']) || ! empty($features['custom_javascript']) || ! empty($features['custom_head'])))
                    <button type="button" role="tab" data-boa-tab="custom" aria-selected="false">Custom code</button>
                @endif
                @if (! empty($features['import_export']))
                    <button type="button" role="tab" data-boa-tab="import" aria-selected="false">Import</button>
                @endif
            </nav>

            <div>
                <div class="boa-panel">
                    @include('boa-theme::settings.partials.general')
                    @include('boa-theme::settings.partials.brand')
                    @include('boa-theme::settings.partials.typography')
                    @include('boa-theme::settings.partials.components')
                    @if ($canCustomCode)
                        @include('boa-theme::settings.partials.custom')
                    @endif
                    @if (! empty($features['import_export']))
                        @include('boa-theme::settings.partials.import')
                    @endif

                    <div class="boa-actions" style="margin-top: 1.5rem;">
                        <button type="submit" class="boa-btn boa-btn-primary">Save changes</button>
                        <a class="boa-btn" href="{{ route($routePrefix.'index') }}" data-boa-confirm="Discard unsaved changes and reload saved values?">Cancel</a>
                    </div>
                </div>

                @if (! empty($features['live_preview']))
                    @include('boa-theme::settings.preview')
                @endif

                <p class="boa-footer-note">
                    Layout options such as sidebar position and sticky headers are omitted — this package is a design-token / branding theme, not an application shell.
                </p>
            </div>
        </div>
    </form>

    @if (! empty($features['import_export']))
        <form id="boa-theme-import-form" method="post" action="{{ route($routePrefix.'import') }}" enctype="multipart/form-data" style="display:none;">
            @csrf
        </form>
    @endif

    @foreach (['general', 'brand', 'typography', 'components', 'custom'] as $resetGroup)
        <form id="boa-reset-{{ $resetGroup }}" method="post" action="{{ route($routePrefix.'reset') }}" style="display:none;">
            @csrf
            <input type="hidden" name="group" value="{{ $resetGroup }}">
        </form>
    @endforeach
</x-boa-theme::settings-layout>
