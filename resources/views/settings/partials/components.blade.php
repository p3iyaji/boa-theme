<section class="boa-section" data-boa-panel="components" role="tabpanel">
    <h2>Components</h2>
    <p class="hint">Token-level component styling exposed as CSS variables for the host app.</p>

    <div class="boa-grid boa-grid-3">
        <div class="boa-field">
            <label for="components_button_radius">Button radius</label>
            <select id="components_button_radius" name="components[button_radius]">
                @foreach (['none', 'sm', 'md', 'lg', 'xl', 'full'] as $value)
                    <option value="{{ $value }}" @selected(old('components.button_radius', $settings['components.button_radius'] ?? 'md') === $value)>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="boa-field">
            <label for="components_card_radius">Card radius</label>
            <select id="components_card_radius" name="components[card_radius]">
                @foreach (['none', 'sm', 'md', 'lg', 'xl'] as $value)
                    <option value="{{ $value }}" @selected(old('components.card_radius', $settings['components.card_radius'] ?? 'lg') === $value)>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="boa-field">
            <label for="components_form_style">Form control style</label>
            <select id="components_form_style" name="components[form_style]">
                @foreach (['outline' => 'Outline', 'filled' => 'Filled', 'underline' => 'Underline'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('components.form_style', $settings['components.form_style'] ?? 'outline') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="boa-actions" style="margin-top: 1.25rem;">
        <button type="submit" form="boa-reset-components" class="boa-btn" data-boa-confirm="Reset the Components section to defaults?">Reset section</button>
    </div>
</section>
