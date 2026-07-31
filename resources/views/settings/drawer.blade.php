{{-- Right-side theme settings drawer --}}
<div
    id="boa-theme-drawer-root"
    class="boa-drawer-root"
    data-boa-drawer
    hidden
    aria-hidden="true"
>
    <div class="boa-drawer-backdrop" data-boa-drawer-close tabindex="-1"></div>

    <aside
        class="boa-drawer-panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="boa-theme-drawer-title"
        data-boa-drawer-panel
    >
        <header class="boa-drawer-header">
            <div>
                <h2 id="boa-theme-drawer-title">Theme Settings</h2>
                <p>Customise appearance. Changes apply to the live app after save.</p>
            </div>
            <button type="button" class="boa-drawer-close" data-boa-drawer-close aria-label="Close theme settings">&times;</button>
        </header>

        <div class="boa-drawer-status" data-boa-drawer-status hidden></div>

        <nav class="boa-drawer-tabs" role="tablist" aria-label="Settings sections">
            <button type="button" role="tab" data-boa-tab="general" aria-selected="true">General</button>
            <button type="button" role="tab" data-boa-tab="brand" aria-selected="false">Brand</button>
            <button type="button" role="tab" data-boa-tab="typography" aria-selected="false">Type</button>
            <button type="button" role="tab" data-boa-tab="components" aria-selected="false">UI</button>
            @if ($canCustomCode && (! empty($features['custom_css']) || ! empty($features['custom_javascript']) || ! empty($features['custom_head'])))
                <button type="button" role="tab" data-boa-tab="custom" aria-selected="false">Custom</button>
            @endif
            @if (! empty($features['import_export']))
                <button type="button" role="tab" data-boa-tab="import" aria-selected="false">Import</button>
            @endif
        </nav>

        <form
            id="boa-theme-settings-form"
            class="boa-drawer-body"
            method="post"
            action="{{ route($routePrefix.'update') }}"
            enctype="multipart/form-data"
            data-boa-settings-form
        >
            @csrf
            @method('PUT')

            <div class="boa-drawer-scroll">
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

                @if (! empty($features['live_preview']))
                    @include('boa-theme::settings.preview')
                @endif
            </div>

            <footer class="boa-drawer-footer">
                <button type="button" class="boa-btn" data-boa-drawer-close>Done</button>
                <div class="boa-drawer-footer-actions">
                    <button
                        type="button"
                        class="boa-btn boa-btn-danger"
                        data-boa-reset-all
                        data-boa-confirm="Reset all theme settings to package defaults?"
                    >Reset all</button>
                    <button type="submit" class="boa-btn boa-btn-primary" data-boa-save>Save</button>
                </div>
            </footer>
        </form>
    </aside>
</div>

@if (! empty($features['import_export']))
    <form id="boa-theme-import-form" method="post" action="{{ route($routePrefix.'import') }}" enctype="multipart/form-data" hidden>
        @csrf
    </form>
@endif

@foreach (['general', 'brand', 'typography', 'components', 'custom'] as $resetGroup)
    <form id="boa-reset-{{ $resetGroup }}" method="post" action="{{ route($routePrefix.'reset') }}" hidden>
        @csrf
        <input type="hidden" name="group" value="{{ $resetGroup }}">
    </form>
@endforeach

<form id="boa-reset-all" method="post" action="{{ route($routePrefix.'reset') }}" hidden>
    @csrf
</form>

<style>
    .boa-drawer-root[hidden] { display: none !important; }
    .boa-drawer-root {
        position: fixed; inset: 0; z-index: 2147483000;
        font-family: var(--boa-font-sans, ui-sans-serif, system-ui, sans-serif);
        color: var(--boa-canvas-950, #1c1917);
    }
    .boa-drawer-backdrop {
        position: absolute; inset: 0;
        background: rgb(15 23 42 / 0.45);
        opacity: 0; transition: opacity 200ms ease;
    }
    .boa-drawer-root.is-open .boa-drawer-backdrop { opacity: 1; }
    .boa-drawer-panel {
        position: absolute; top: 0; right: 0; height: 100%;
        width: min(420px, 100vw);
        background: #fff;
        box-shadow: -12px 0 40px rgb(0 0 0 / 0.18);
        display: flex; flex-direction: column;
        transform: translateX(100%);
        transition: transform 220ms ease;
    }
    .boa-drawer-root.is-open .boa-drawer-panel { transform: translateX(0); }
    .boa-drawer-header {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
        padding: 1.1rem 1.15rem 0.85rem;
        border-bottom: 1px solid color-mix(in srgb, var(--boa-canvas-300, #d6d3d1) 70%, transparent);
    }
    .boa-drawer-header h2 { margin: 0; font-family: var(--boa-font-display, Georgia, serif); font-size: 1.25rem; color: var(--boa-brand-900, #134e4a); }
    .boa-drawer-header p { margin: 0.3rem 0 0; font-size: 0.85rem; color: var(--boa-canvas-600, #57534e); }
    .boa-drawer-close {
        appearance: none; border: 0; background: transparent; font-size: 1.6rem; line-height: 1;
        cursor: pointer; color: var(--boa-canvas-600, #57534e); padding: 0.15rem 0.35rem;
    }
    .boa-drawer-status {
        margin: 0.75rem 1.15rem 0; padding: 0.65rem 0.8rem; border-radius: 0.6rem; font-size: 0.88rem;
        border: 1px solid transparent;
    }
    .boa-drawer-status.is-success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
    .boa-drawer-status.is-error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
    .boa-drawer-tabs {
        display: flex; gap: 0.25rem; overflow-x: auto; padding: 0.75rem 1rem 0;
        border-bottom: 1px solid color-mix(in srgb, var(--boa-canvas-300, #d6d3d1) 70%, transparent);
    }
    .boa-drawer-tabs button {
        appearance: none; border: 0; background: transparent; cursor: pointer; font: inherit; font-weight: 600;
        font-size: 0.85rem; color: var(--boa-canvas-600, #57534e); padding: 0.45rem 0.7rem; border-radius: 0.5rem 0.5rem 0 0;
        white-space: nowrap;
    }
    .boa-drawer-tabs button[aria-selected="true"] {
        color: var(--boa-brand-900, #134e4a); background: color-mix(in srgb, var(--boa-brand-50, #f0fdfa) 80%, white);
    }
    .boa-drawer-body { display: flex; flex-direction: column; flex: 1; min-height: 0; margin: 0; }
    .boa-drawer-scroll { flex: 1; overflow: auto; padding: 1rem 1.15rem 1.25rem; }
    .boa-drawer-footer {
        display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;
        padding: 0.85rem 1.15rem; border-top: 1px solid color-mix(in srgb, var(--boa-canvas-300, #d6d3d1) 70%, transparent);
        background: #fff;
    }
    .boa-drawer-footer-actions { display: flex; gap: 0.5rem; }
    .boa-btn {
        appearance: none; border: 1px solid #d6d3d1; background: #fff; color: inherit;
        border-radius: var(--boa-button-radius, 0.65rem); padding: 0.5rem 0.85rem; font: inherit; font-weight: 600; cursor: pointer;
    }
    .boa-btn-primary { background: var(--boa-brand-800, #115e59); border-color: var(--boa-brand-800, #115e59); color: #fff; }
    .boa-btn-danger { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
    .boa-section { display: none; }
    .boa-section.is-active { display: block; }
    .boa-section h2 { margin: 0 0 0.25rem; font-size: 1rem; }
    .boa-section .hint { margin: 0 0 1rem; color: #78716c; font-size: 0.85rem; }
    .boa-grid { display: grid; gap: 0.85rem; }
    .boa-grid-2, .boa-grid-3 { grid-template-columns: 1fr; }
    .boa-field { display: flex; flex-direction: column; gap: 0.3rem; }
    .boa-field label { font-weight: 600; font-size: 0.86rem; }
    .boa-field .help { color: #78716c; font-size: 0.78rem; }
    .boa-field input[type="text"], .boa-field input[type="number"], .boa-field input[type="color"],
    .boa-field select, .boa-field textarea, .boa-field input[type="file"] {
        width: 100%; border: 1px solid #d6d3d1; border-radius: 0.55rem; padding: 0.5rem 0.65rem; font: inherit; background: #fff;
    }
    .boa-field textarea { min-height: 6rem; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.8rem; }
    .boa-field input[type="color"] { padding: 0.15rem; height: 2.4rem; }
    .boa-check { display: flex; align-items: center; gap: 0.45rem; font-weight: 600; font-size: 0.88rem; }
    .boa-color-pair { display: grid; grid-template-columns: 2.6rem 1fr; gap: 0.45rem; align-items: center; }
    .boa-advanced { border: 1px solid #fcd34d; background: #fffbeb; padding: 0.7rem; border-radius: 0.55rem; margin-bottom: 0.85rem; font-size: 0.85rem; }
    .boa-preview { margin-top: 1rem; border: 1px dashed #d6d3d1; border-radius: 0.75rem; padding: 0.85rem; background: #fafaf9; }
    .boa-preview h3 { margin: 0 0 0.65rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: #78716c; }
    .boa-preview-frame { border: 1px solid #e7e5e4; border-radius: var(--boa-card-radius, 0.75rem); overflow: hidden; background: #fff; }
    .boa-preview-header { display: flex; justify-content: space-between; gap: 0.5rem; padding: 0.7rem 0.85rem; background: var(--boa-brand-950, #042f2e); color: var(--boa-brand-50, #f0fdfa); font-size: 0.85rem; }
    .boa-preview-body { padding: 0.85rem; display: grid; gap: 0.65rem; }
    .boa-swatch-row { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .boa-swatch { width: 1.6rem; height: 1.6rem; border-radius: 999px; border: 1px solid rgb(0 0 0 / 0.08); }
    .boa-sample-btn { display: inline-flex; padding: 0.45rem 0.75rem; border: 0; border-radius: var(--boa-button-radius, 0.55rem); background: var(--boa-accent-500, #f59e0b); color: #111; font-weight: 700; }
    .boa-sample-card { border: 1px solid #e7e5e4; border-radius: var(--boa-card-radius, 0.75rem); padding: 0.7rem; }
    .boa-sample-alert { padding: 0.55rem 0.7rem; border-radius: 0.5rem; background: var(--boa-success-50, #ecfdf5); color: var(--boa-success-900, #064e3b); border: 1px solid var(--boa-success-200, #a7f3d0); font-size: 0.85rem; }
    .boa-sample-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .boa-sample-table th, .boa-sample-table td { border-bottom: 1px solid #e7e5e4; padding: 0.35rem; text-align: left; }
    .boa-asset-preview { max-height: 2.5rem; max-width: 7rem; object-fit: contain; }
    .boa-actions { display: flex; flex-wrap: wrap; gap: 0.45rem; }
</style>

<script>
(() => {
    const root = document.querySelector('[data-boa-drawer]');
    if (!root || root.dataset.boaBound === '1') return;
    root.dataset.boaBound = '1';

    const form = root.querySelector('[data-boa-settings-form]');
    const status = root.querySelector('[data-boa-drawer-status]');
    const tabs = Array.from(root.querySelectorAll('[data-boa-tab]'));
    const panels = Array.from(root.querySelectorAll('[data-boa-panel]'));
    const csrf = form?.querySelector('input[name="_token"]')?.value;

    const showStatus = (message, type = 'success') => {
        if (!status) return;
        status.hidden = false;
        status.textContent = message;
        status.classList.remove('is-success', 'is-error');
        status.classList.add(type === 'error' ? 'is-error' : 'is-success');
    };

    const activate = (id) => {
        tabs.forEach((tab) => tab.setAttribute('aria-selected', String(tab.dataset.boaTab === id)));
        panels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.boaPanel === id));
    };

    const open = () => {
        root.hidden = false;
        root.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => root.classList.add('is-open'));
        document.documentElement.style.overflow = 'hidden';
    };

    const close = () => {
        root.classList.remove('is-open');
        document.documentElement.style.overflow = '';
        window.setTimeout(() => {
            root.hidden = true;
            root.setAttribute('aria-hidden', 'true');
        }, 220);
    };

    const applyCssPayload = async () => {
        const res = await fetch(@json(route($routePrefix.'preview')), {
            headers: { 'Accept': 'text/css', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        const css = await res.text();
        let vars = document.getElementById('boa-theme-vars');
        if (!vars) {
            vars = document.createElement('style');
            vars.id = 'boa-theme-vars';
            document.head.appendChild(vars);
        }
        // preview endpoint returns variables only; refresh vars then keep bridge
        const parts = css.split(/\n\n(?=\/\*|html|body|\.bg-|\.font-)/);
        // Prefer dedicated css endpoint shape: variables block is enough for live colour updates
        vars.textContent = css.includes('--boa-') ? css.replace(/\n\nhtml[\s\S]*$/, '').trim() || css : css;

        // Also hit a JSON-friendly refresh if available via update response
    };

    const refreshThemeFromPayload = (payload) => {
        if (!payload) return;
        if (payload.css_variables) {
            let vars = document.getElementById('boa-theme-vars');
            if (!vars) {
                vars = document.createElement('style');
                vars.id = 'boa-theme-vars';
                document.head.appendChild(vars);
            }
            vars.textContent = payload.css_variables;
        }
        if (payload.css_bridge) {
            let bridge = document.getElementById('boa-theme-bridge');
            if (!bridge) {
                bridge = document.createElement('style');
                bridge.id = 'boa-theme-bridge';
                document.head.appendChild(bridge);
            }
            bridge.textContent = payload.css_bridge;
        }
        const rootEl = document.documentElement;
        rootEl.classList.remove('boa-theme-dark', 'dark');
        if (payload.color_mode === 'dark') {
            rootEl.classList.add('boa-theme-dark', 'dark');
        } else if (payload.color_mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            rootEl.classList.add('boa-theme-dark', 'dark');
        }
        if (payload.name) {
            root.querySelectorAll('[data-boa-preview-name]').forEach((el) => { el.textContent = payload.name; });
        }
    };

    const postForm = async (targetForm) => {
        const data = new FormData(targetForm);
        const res = await fetch(targetForm.action, {
            method: 'POST',
            body: data,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        const json = await res.json().catch(() => ({}));

        if (!res.ok) {
            const firstError = json.errors ? Object.values(json.errors)[0]?.[0] : null;
            throw new Error(firstError || json.message || 'Could not save theme settings.');
        }

        refreshThemeFromPayload(json);
        return json;
    };

    tabs.forEach((tab) => tab.addEventListener('click', () => activate(tab.dataset.boaTab)));
    activate(tabs[0]?.dataset.boaTab || 'general');

    document.querySelectorAll('[data-boa-theme-open]').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            open();
        });
    });

    root.querySelectorAll('[data-boa-drawer-close]').forEach((el) => {
        el.addEventListener('click', close);
    });

    document.addEventListener('boa-theme:open-settings', open);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && root.classList.contains('is-open')) close();
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const saveBtn = form.querySelector('[data-boa-save]');
        if (saveBtn) saveBtn.disabled = true;
        try {
            const json = await postForm(form);
            showStatus(json.message || 'Theme settings saved.');
            // Keep drawer open briefly so the live page behind shows the new theme, then close.
            window.setTimeout(close, 650);
        } catch (error) {
            showStatus(error.message || 'Save failed.', 'error');
        } finally {
            if (saveBtn) saveBtn.disabled = false;
        }
    });

    root.querySelector('[data-boa-reset-all]')?.addEventListener('click', async () => {
        const message = root.querySelector('[data-boa-reset-all]').getAttribute('data-boa-confirm');
        if (message && !window.confirm(message)) return;
        try {
            const json = await postForm(document.getElementById('boa-reset-all'));
            showStatus(json.message || 'Theme settings reset.');
            refreshThemeFromPayload(json);
            window.setTimeout(() => window.location.reload(), 400);
        } catch (error) {
            showStatus(error.message || 'Reset failed.', 'error');
        }
    });

    document.querySelectorAll('button[form^="boa-reset-"]').forEach((btn) => {
        btn.addEventListener('click', async (event) => {
            const confirmMsg = btn.getAttribute('data-boa-confirm');
            if (confirmMsg && !window.confirm(confirmMsg)) {
                event.preventDefault();
                return;
            }
            event.preventDefault();
            const formId = btn.getAttribute('form');
            const resetForm = document.getElementById(formId);
            if (!resetForm) return;
            try {
                const json = await postForm(resetForm);
                showStatus(json.message || 'Section reset.');
                window.setTimeout(() => window.location.reload(), 400);
            } catch (error) {
                showStatus(error.message || 'Reset failed.', 'error');
            }
        });
    });

    // Near-live colour preview inside the drawer
    form?.addEventListener('input', () => {
        const brand = form.querySelector('[name="brand[colors][brand]"]')?.value;
        const accent = form.querySelector('[name="brand[colors][accent]"]')?.value;
        const name = form.querySelector('[name="brand[name]"]')?.value;
        const preview = root.querySelector('[data-boa-preview]');
        if (!preview) return;
        if (brand) preview.style.setProperty('--boa-brand-950', brand);
        if (accent) {
            preview.style.setProperty('--boa-accent-500', accent);
            preview.style.setProperty('--boa-link', accent);
        }
        if (name) preview.querySelector('[data-boa-preview-name]')?.replaceChildren(document.createTextNode(name));
    });

    // Auto-open when visiting the dedicated settings URL
    if (@json(! empty($standalone))) {
        open();
    }
})();
</script>
