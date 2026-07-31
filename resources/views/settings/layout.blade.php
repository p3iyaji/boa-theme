<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Theme Settings' }} — {{ config('app.name', 'BOA') }}</title>
    <x-boa-theme::styles />
    <style>
        :root {
            --boa-panel-bg: color-mix(in srgb, var(--boa-canvas-50) 92%, white);
            --boa-panel-surface: #fff;
            --boa-panel-ink: var(--boa-brand-950);
            --boa-panel-muted: var(--boa-canvas-600);
            --boa-panel-border: color-mix(in srgb, var(--boa-canvas-300) 70%, transparent);
            --boa-panel-accent: var(--boa-accent-600);
            --boa-panel-radius: var(--boa-radius-lg);
        }
        * { box-sizing: border-box; }
        body.boa-theme-settings {
            margin: 0;
            min-height: 100vh;
            font-family: var(--boa-font-sans);
            font-size: var(--boa-font-size, 16px);
            line-height: var(--boa-line-height, 1.5);
            color: var(--boa-panel-ink);
            background:
                radial-gradient(1200px 600px at 10% -10%, color-mix(in srgb, var(--boa-accent-200) 45%, transparent), transparent 60%),
                radial-gradient(900px 500px at 100% 0%, color-mix(in srgb, var(--boa-brand-200) 40%, transparent), transparent 55%),
                var(--boa-panel-bg);
        }
        .boa-shell { max-width: 1200px; margin: 0 auto; padding: 1.5rem 1.25rem 3rem; }
        .boa-header { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; justify-content: space-between; margin-bottom: 1.5rem; }
        .boa-header h1 { margin: 0; font-family: var(--boa-font-display); font-weight: var(--boa-font-heading-weight, 700); font-size: clamp(1.75rem, 3vw, 2.25rem); letter-spacing: 0.02em; color: var(--boa-brand-900); }
        .boa-header p { margin: 0.35rem 0 0; color: var(--boa-panel-muted); max-width: 40rem; }
        .boa-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .boa-btn {
            appearance: none; border: 1px solid var(--boa-panel-border); background: var(--boa-panel-surface);
            color: var(--boa-panel-ink); border-radius: var(--boa-button-radius, var(--boa-radius-md));
            padding: 0.55rem 0.95rem; font: inherit; font-weight: 600; cursor: pointer;
            transition: background var(--boa-motion, 200ms), border-color var(--boa-motion, 200ms), transform var(--boa-motion, 200ms);
        }
        .boa-btn:hover { border-color: var(--boa-brand-400); }
        .boa-btn:focus-visible { outline: 2px solid var(--boa-accent-500); outline-offset: 2px; }
        .boa-btn-primary { background: var(--boa-brand-800); border-color: var(--boa-brand-800); color: var(--boa-brand-50); }
        .boa-btn-primary:hover { background: var(--boa-brand-900); }
        .boa-btn-danger { background: var(--boa-danger-50); border-color: var(--boa-danger-300); color: var(--boa-danger-800); }
        .boa-btn-ghost { background: transparent; }
        .boa-alert { padding: 0.85rem 1rem; border-radius: var(--boa-radius-md); margin-bottom: 1rem; border: 1px solid transparent; }
        .boa-alert-success { background: color-mix(in srgb, var(--boa-success-100) 80%, white); border-color: var(--boa-success-300); color: var(--boa-success-900); }
        .boa-alert-error { background: color-mix(in srgb, var(--boa-danger-100) 80%, white); border-color: var(--boa-danger-300); color: var(--boa-danger-900); }
        .boa-layout { display: grid; gap: 1.25rem; grid-template-columns: 1fr; }
        @media (min-width: 960px) {
            .boa-layout { grid-template-columns: 220px minmax(0, 1fr); align-items: start; }
        }
        .boa-nav { display: flex; gap: 0.35rem; overflow-x: auto; padding-bottom: 0.25rem; }
        @media (min-width: 960px) {
            .boa-nav { flex-direction: column; position: sticky; top: 1rem; }
        }
        .boa-nav button {
            appearance: none; border: 0; background: transparent; text-align: left; cursor: pointer;
            font: inherit; font-weight: 600; color: var(--boa-panel-muted); padding: 0.65rem 0.85rem;
            border-radius: var(--boa-radius-md); white-space: nowrap;
        }
        .boa-nav button[aria-selected="true"] { background: var(--boa-panel-surface); color: var(--boa-brand-900); box-shadow: var(--boa-shadow); }
        .boa-panel {
            background: color-mix(in srgb, var(--boa-panel-surface) 92%, transparent);
            border: 1px solid var(--boa-panel-border);
            border-radius: var(--boa-panel-radius);
            box-shadow: var(--boa-shadow);
            padding: 1.25rem;
        }
        .boa-section { display: none; }
        .boa-section.is-active { display: block; }
        .boa-section h2 { margin: 0 0 0.35rem; font-size: 1.15rem; font-family: var(--boa-font-display); color: var(--boa-brand-900); }
        .boa-section .hint { margin: 0 0 1.25rem; color: var(--boa-panel-muted); font-size: 0.95rem; }
        .boa-grid { display: grid; gap: 1rem; grid-template-columns: 1fr; }
        @media (min-width: 720px) { .boa-grid-2 { grid-template-columns: 1fr 1fr; } .boa-grid-3 { grid-template-columns: 1fr 1fr 1fr; } }
        .boa-field { display: flex; flex-direction: column; gap: 0.35rem; }
        .boa-field label { font-weight: 600; font-size: 0.92rem; }
        .boa-field .help { color: var(--boa-panel-muted); font-size: 0.82rem; }
        .boa-field input[type="text"],
        .boa-field input[type="number"],
        .boa-field input[type="color"],
        .boa-field select,
        .boa-field textarea {
            width: 100%; border: 1px solid var(--boa-panel-border); border-radius: var(--boa-radius-md);
            padding: 0.6rem 0.75rem; font: inherit; background: #fff; color: inherit;
        }
        .boa-field textarea { min-height: 8rem; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.85rem; }
        .boa-field input[type="color"] { padding: 0.2rem; height: 2.6rem; }
        .boa-check { display: flex; align-items: center; gap: 0.55rem; font-weight: 600; }
        .boa-check input { width: 1.05rem; height: 1.05rem; }
        .boa-preview {
            margin-top: 1.25rem; border: 1px dashed var(--boa-panel-border); border-radius: var(--boa-radius-lg);
            padding: 1rem; background: color-mix(in srgb, var(--boa-canvas-50) 70%, white);
        }
        .boa-preview h3 { margin: 0 0 0.75rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--boa-panel-muted); }
        .boa-preview-frame { border-radius: var(--boa-card-radius, var(--boa-radius-lg)); overflow: hidden; border: 1px solid var(--boa-panel-border); background: #fff; }
        .boa-preview-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.85rem 1rem; background: var(--boa-brand-950); color: var(--boa-brand-50); }
        .boa-preview-body { display: grid; gap: 1rem; padding: 1rem; }
        @media (min-width: 720px) { .boa-preview-body { grid-template-columns: 180px 1fr; } }
        .boa-preview-side { background: var(--boa-canvas-100); border-radius: var(--boa-radius-md); padding: 0.85rem; }
        .boa-preview-side a { display: block; color: var(--boa-link, var(--boa-accent-700)); text-decoration: none; padding: 0.35rem 0; font-weight: 600; }
        .boa-swatch-row { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .boa-swatch { width: 2rem; height: 2rem; border-radius: 999px; border: 1px solid rgb(0 0 0 / 0.08); }
        .boa-sample-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.55rem 0.9rem; border-radius: var(--boa-button-radius, var(--boa-radius-md)); background: var(--boa-accent-500); color: var(--boa-on-accent, #111); border: 0; font-weight: 700; box-shadow: var(--boa-shadow); }
        .boa-sample-card { border: 1px solid var(--boa-panel-border); border-radius: var(--boa-card-radius, var(--boa-radius-lg)); padding: 0.85rem; background: #fff; box-shadow: var(--boa-shadow); }
        .boa-sample-alert { padding: 0.65rem 0.8rem; border-radius: var(--boa-radius-md); background: var(--boa-success-50); color: var(--boa-success-900); border: 1px solid var(--boa-success-200); }
        .boa-sample-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .boa-sample-table th, .boa-sample-table td { border-bottom: 1px solid var(--boa-panel-border); padding: 0.45rem 0.35rem; text-align: left; }
        .boa-color-pair { display: grid; grid-template-columns: 3rem 1fr; gap: 0.5rem; align-items: center; }
        .boa-footer-note { margin-top: 1rem; font-size: 0.85rem; color: var(--boa-panel-muted); }
        .boa-advanced { border: 1px solid var(--boa-warning-300, #fcd34d); background: color-mix(in srgb, var(--boa-warning-50, #fffbeb) 80%, white); padding: 0.85rem; border-radius: var(--boa-radius-md); margin-bottom: 1rem; }
        .boa-unsaved { display: none; }
        .boa-unsaved.is-visible { display: inline-flex; }
        .boa-asset-preview { max-height: 3rem; max-width: 8rem; object-fit: contain; }
    </style>
</head>
<body class="boa-theme-settings">
    <div class="boa-shell">
        {{ $slot }}
    </div>
    <script>
        (() => {
            const form = document.getElementById('boa-theme-settings-form');
            if (!form) return;

            const tabs = Array.from(document.querySelectorAll('[data-boa-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-boa-panel]'));
            const unsaved = document.querySelector('[data-boa-unsaved]');
            let dirty = false;

            const activate = (id) => {
                tabs.forEach((tab) => tab.setAttribute('aria-selected', String(tab.dataset.boaTab === id)));
                panels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.boaPanel === id));
                try { localStorage.setItem('boa-theme-settings-tab', id); } catch (_) {}
            };

            tabs.forEach((tab) => tab.addEventListener('click', () => activate(tab.dataset.boaTab)));
            const savedTab = (() => { try { return localStorage.getItem('boa-theme-settings-tab'); } catch (_) { return null; } })();
            activate(savedTab && panels.some((p) => p.dataset.boaPanel === savedTab) ? savedTab : (tabs[0]?.dataset.boaTab || 'general'));

            const markDirty = () => {
                dirty = true;
                unsaved?.classList.add('is-visible');
            };

            form.addEventListener('input', markDirty);
            form.addEventListener('change', markDirty);
            form.addEventListener('submit', () => { dirty = false; });

            window.addEventListener('beforeunload', (event) => {
                if (!dirty) return;
                event.preventDefault();
                event.returnValue = '';
            });

            document.querySelectorAll('[data-boa-confirm]').forEach((el) => {
                el.addEventListener('click', (event) => {
                    const message = el.getAttribute('data-boa-confirm') || 'Are you sure?';
                    if (!window.confirm(message)) {
                        event.preventDefault();
                    } else {
                        dirty = false;
                    }
                });
            });

            // Near-live preview: update CSS variables from color/text inputs where practical.
            const previewRoot = document.querySelector('[data-boa-preview]');
            const syncPreview = () => {
                if (!previewRoot) return;
                const brand = form.querySelector('[name="brand[colors][brand]"]')?.value;
                const accent = form.querySelector('[name="brand[colors][accent]"]')?.value;
                const name = form.querySelector('[name="brand[name]"]')?.value;
                if (brand) previewRoot.style.setProperty('--boa-brand-950', brand);
                if (accent) {
                    previewRoot.style.setProperty('--boa-accent-500', accent);
                    previewRoot.style.setProperty('--boa-link', accent);
                }
                const title = previewRoot.querySelector('[data-boa-preview-name]');
                if (title && name) title.textContent = name;
            };
            form.addEventListener('input', syncPreview);
            syncPreview();
        })();
    </script>
</body>
</html>
