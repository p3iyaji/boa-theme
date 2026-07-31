<section class="boa-section" data-boa-panel="import" role="tabpanel">
    <h2>Import</h2>
    <p class="hint">Upload a previously exported JSON settings file. Only known keys are accepted.</p>

    <div class="boa-field">
        <label for="import_file">Settings JSON</label>
        <input id="import_file" type="file" name="file" form="boa-theme-import-form" accept=".json,application/json" required>
    </div>

    <div class="boa-actions" style="margin-top: 1.25rem;">
        <button type="submit" form="boa-theme-import-form" class="boa-btn boa-btn-primary" data-boa-confirm="Import settings and overwrite matching saved values?">Import settings</button>
    </div>
</section>
