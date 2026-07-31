<section class="boa-section" data-boa-panel="custom" role="tabpanel">
    <h2>Custom code</h2>
    <div class="boa-advanced">
        <strong>Advanced — trusted administrators only.</strong>
        Custom CSS, JavaScript, and head HTML can affect every visitor. These fields are disabled unless explicitly enabled in package config.
    </div>

    @if (! empty($features['custom_css']))
        <div class="boa-field" style="margin-bottom: 1rem;">
            <label for="custom_css">Custom CSS</label>
            <textarea id="custom_css" name="custom[css]">{{ old('custom.css', $settings['custom.css'] ?? '') }}</textarea>
        </div>
    @endif

    @if (! empty($features['custom_javascript']))
        <div class="boa-field" style="margin-bottom: 1rem;">
            <label for="custom_javascript">Custom JavaScript</label>
            <textarea id="custom_javascript" name="custom[javascript]">{{ old('custom.javascript', $settings['custom.javascript'] ?? '') }}</textarea>
        </div>
    @endif

    @if (! empty($features['custom_head']))
        <div class="boa-field">
            <label for="custom_head">Custom &lt;head&gt; content</label>
            <textarea id="custom_head" name="custom[head]">{{ old('custom.head', $settings['custom.head'] ?? '') }}</textarea>
        </div>
    @endif

    <div class="boa-actions" style="margin-top: 1.25rem;">
        <button type="submit" form="boa-reset-custom" class="boa-btn" data-boa-confirm="Reset custom code fields?">Reset section</button>
    </div>
</section>
