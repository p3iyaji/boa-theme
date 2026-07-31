<section class="boa-section" data-boa-panel="typography" role="tabpanel">
    <h2>Typography</h2>
    <p class="hint">Choose from the controlled font list. Arbitrary CSS font stacks are not accepted here.</p>

    <div class="boa-grid boa-grid-2">
        <div class="boa-field">
            <label for="typography_sans">Primary font</label>
            <select id="typography_sans" name="typography[sans]">
                @foreach ($fonts as $font)
                    <option value="{{ $font }}" @selected(old('typography.sans', $settings['typography.sans'] ?? '') === $font)>{{ $font }}</option>
                @endforeach
            </select>
        </div>
        <div class="boa-field">
            <label for="typography_display">Heading font</label>
            <select id="typography_display" name="typography[display]">
                @foreach ($fonts as $font)
                    <option value="{{ $font }}" @selected(old('typography.display', $settings['typography.display'] ?? '') === $font)>{{ $font }}</option>
                @endforeach
            </select>
        </div>
        <div class="boa-field">
            <label for="typography_base_size">Base font size</label>
            <select id="typography_base_size" name="typography[base_size]">
                @foreach (['14px', '15px', '16px', '17px', '18px', '20px'] as $size)
                    <option value="{{ $size }}" @selected(old('typography.base_size', $settings['typography.base_size'] ?? '16px') === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <div class="boa-field">
            <label for="typography_heading_weight">Heading weight</label>
            <select id="typography_heading_weight" name="typography[heading_weight]">
                @foreach (['400', '500', '600', '700', '800'] as $weight)
                    <option value="{{ $weight }}" @selected(old('typography.heading_weight', $settings['typography.heading_weight'] ?? '700') === $weight)>{{ $weight }}</option>
                @endforeach
            </select>
        </div>
        <div class="boa-field">
            <label for="typography_body_weight">Body weight</label>
            <select id="typography_body_weight" name="typography[body_weight]">
                @foreach (['300', '400', '500', '600'] as $weight)
                    <option value="{{ $weight }}" @selected(old('typography.body_weight', $settings['typography.body_weight'] ?? '400') === $weight)>{{ $weight }}</option>
                @endforeach
            </select>
        </div>
        <div class="boa-field">
            <label for="typography_line_height">Line height</label>
            <input id="typography_line_height" type="number" step="0.05" min="1" max="2.5" name="typography[line_height]" value="{{ old('typography.line_height', $settings['typography.line_height'] ?? '1.5') }}">
        </div>
        <div class="boa-field">
            <label for="typography_letter_spacing">Letter spacing</label>
            <input id="typography_letter_spacing" type="text" name="typography[letter_spacing]" value="{{ old('typography.letter_spacing', $settings['typography.letter_spacing'] ?? '0') }}" placeholder="0 or 0.02em">
        </div>
    </div>

    <div class="boa-actions" style="margin-top: 1.25rem;">
        <button type="submit" form="boa-reset-typography" class="boa-btn" data-boa-confirm="Reset the Typography section to defaults?">Reset section</button>
    </div>
</section>
