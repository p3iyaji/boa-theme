<div class="boa-preview" data-boa-preview>
    <h3>Live preview</h3>
    <div class="boa-preview-frame">
        <div class="boa-preview-header">
            <strong data-boa-preview-name>{{ $theme->name() }}</strong>
            <span>{{ $theme->tagline() }}</span>
        </div>
        <div class="boa-preview-body">
            <aside class="boa-preview-side" aria-label="Sample navigation">
                <a href="#">Dashboard</a>
                <a href="#">Library</a>
                <a href="#">Settings</a>
            </aside>
            <div style="display:grid;gap:0.85rem;">
                <div class="boa-swatch-row" aria-label="Colour swatches">
                    @foreach (['brand', 'accent', 'success', 'warning', 'danger', 'info'] as $role)
                        <span class="boa-swatch" style="background: var(--boa-{{ $role }}-600, var(--boa-{{ $role }}));" title="{{ $role }}"></span>
                    @endforeach
                </div>
                <div>
                    <button type="button" class="boa-sample-btn">Primary action</button>
                </div>
                <div class="boa-sample-card">
                    <strong style="font-family: var(--boa-font-display);">Sample card</strong>
                    <p style="margin: 0.35rem 0 0; color: var(--boa-canvas-700);">Representative body copy using theme typography tokens.</p>
                </div>
                <div class="boa-sample-alert">Success alert — settings look good.</div>
                <div class="boa-field">
                    <label for="preview_input">Sample input</label>
                    <input id="preview_input" type="text" value="Readable form control" readonly>
                </div>
                <table class="boa-sample-table">
                    <thead>
                        <tr><th>Item</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Theme tokens</td><td>Active</td></tr>
                        <tr><td>Preview</td><td>Near-live</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
