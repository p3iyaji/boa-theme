<section class="boa-section" data-boa-panel="brand" role="tabpanel">
    <h2>Brand</h2>
    <p class="hint">Name, logos, and semantic colour seeds. Each seed expands into a full palette.</p>

    <div class="boa-grid boa-grid-2">
        <div class="boa-field">
            <label for="brand_name">Brand name</label>
            <input id="brand_name" type="text" name="brand[name]" value="{{ old('brand.name', $settings['brand.name'] ?? '') }}">
        </div>
        <div class="boa-field">
            <label for="brand_tagline">Tagline</label>
            <input id="brand_tagline" type="text" name="brand[tagline]" value="{{ old('brand.tagline', $settings['brand.tagline'] ?? '') }}">
        </div>
    </div>

    @if (! empty($features['uploads']))
        <div class="boa-grid boa-grid-3" style="margin-top: 1rem;">
            <div class="boa-field">
                <label for="logo">Logo</label>
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Current logo" class="boa-asset-preview">
                    <label class="boa-check"><input type="checkbox" name="remove_logo" value="1"> Remove</label>
                @endif
                <input id="logo" type="file" name="logo" accept="image/*">
            </div>
            <div class="boa-field">
                <label for="logo_dark">Dark-mode logo</label>
                @if ($logoDarkUrl)
                    <img src="{{ $logoDarkUrl }}" alt="Current dark logo" class="boa-asset-preview">
                    <label class="boa-check"><input type="checkbox" name="remove_logo_dark" value="1"> Remove</label>
                @endif
                <input id="logo_dark" type="file" name="logo_dark" accept="image/*">
            </div>
            <div class="boa-field">
                <label for="favicon">Favicon</label>
                @if ($faviconUrl)
                    <img src="{{ $faviconUrl }}" alt="Current favicon" class="boa-asset-preview">
                    <label class="boa-check"><input type="checkbox" name="remove_favicon" value="1"> Remove</label>
                @endif
                <input id="favicon" type="file" name="favicon" accept="image/*,.ico">
            </div>
        </div>
    @endif

    <div class="boa-grid boa-grid-2" style="margin-top: 1rem;">
        @foreach ([
            'brand' => 'Primary (brand)',
            'accent' => 'Accent',
            'canvas' => 'Canvas / secondary',
            'link' => 'Link',
            'success' => 'Success',
            'warning' => 'Warning',
            'danger' => 'Danger',
            'info' => 'Information',
        ] as $key => $label)
            @php
                $current = old("brand.colors.{$key}", $settings["brand.colors.{$key}"] ?? $theme->color($key === 'link' ? 'accent' : $key, 600));
                if (! is_string($current) || $current === '' || ! str_starts_with($current, '#')) {
                    $current = $theme->color($key === 'link' ? 'accent' : $key, 600);
                }
            @endphp
            <div class="boa-field">
                <label for="color_{{ $key }}">{{ $label }}</label>
                <div class="boa-color-pair">
                    <input id="color_{{ $key }}_picker" type="color" value="{{ strlen($current) === 4 ? $current : $current }}" oninput="this.nextElementSibling.value = this.value">
                    <input id="color_{{ $key }}" type="text" name="brand[colors][{{ $key }}]" value="{{ $current }}" pattern="#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})" oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value)) this.previousElementSibling.value=this.value">
                </div>
            </div>
        @endforeach
    </div>

    <div class="boa-actions" style="margin-top: 1.25rem;">
        <button type="submit" form="boa-reset-brand" class="boa-btn" data-boa-confirm="Reset the Brand section to defaults?">Reset section</button>
    </div>
</section>
