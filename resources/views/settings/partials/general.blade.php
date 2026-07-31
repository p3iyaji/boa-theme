<section class="boa-section is-active" data-boa-panel="general" role="tabpanel">
    <h2>General</h2>
    <p class="hint">Colour mode, density, and global appearance toggles.</p>

    <div class="boa-grid boa-grid-2">
        <div class="boa-field">
            <label for="general_display_label">Display label</label>
            <input id="general_display_label" type="text" name="general[display_label]" value="{{ old('general.display_label', $settings['general.display_label'] ?? '') }}">
        </div>

        <div class="boa-field">
            <label for="general_color_mode">Colour mode</label>
            <select id="general_color_mode" name="general[color_mode]">
                @foreach (['system' => 'System preference', 'light' => 'Light', 'dark' => 'Dark'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('general.color_mode', $settings['general.color_mode'] ?? 'system') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="boa-field">
            <label for="general_preset">Colour scheme preset</label>
            <select id="general_preset" name="general[preset]">
                @foreach ($presets as $preset)
                    <option value="{{ $preset }}" @selected(old('general.preset', $settings['general.preset'] ?? '') === $preset)>{{ $preset }}</option>
                @endforeach
            </select>
        </div>

        <div class="boa-field">
            <label for="general_density">Layout density</label>
            <select id="general_density" name="general[density]">
                @foreach (['comfortable' => 'Comfortable', 'compact' => 'Compact'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('general.density', $settings['general.density'] ?? 'comfortable') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="boa-field">
            <label for="general_content_width">Content width</label>
            <select id="general_content_width" name="general[content_width]">
                @foreach (['full' => 'Full width', 'boxed' => 'Boxed'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('general.content_width', $settings['general.content_width'] ?? 'full') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="boa-field">
            <label for="general_body_class">Body CSS class</label>
            <input id="general_body_class" type="text" name="general[body_class]" value="{{ old('general.body_class', $settings['general.body_class'] ?? '') }}" pattern="[a-zA-Z0-9_\-\s]*" placeholder="optional-class">
            <span class="help">Safe class tokens only (letters, numbers, dash, underscore).</span>
        </div>
    </div>

    <div class="boa-grid boa-grid-3" style="margin-top: 1rem;">
        <label class="boa-check">
            <input type="hidden" name="general[rounded]" value="0">
            <input type="checkbox" name="general[rounded]" value="1" @checked(old('general.rounded', $settings['general.rounded'] ?? true))>
            Rounded components
        </label>
        <label class="boa-check">
            <input type="hidden" name="general[shadows]" value="0">
            <input type="checkbox" name="general[shadows]" value="1" @checked(old('general.shadows', $settings['general.shadows'] ?? true))>
            Shadows
        </label>
        <label class="boa-check">
            <input type="hidden" name="general[animations]" value="0">
            <input type="checkbox" name="general[animations]" value="1" @checked(old('general.animations', $settings['general.animations'] ?? true))>
            Animations
        </label>
    </div>

    <div class="boa-actions" style="margin-top: 1.25rem;">
        <button type="submit" form="boa-reset-general" class="boa-btn" data-boa-confirm="Reset the General section to defaults?">Reset section</button>
    </div>
</section>
