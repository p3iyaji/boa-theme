# Build a Laravel Theme Settings Panel for an Existing Theme Package

## Role

Act as a senior Laravel package developer, Laravel architect, frontend engineer, and QA engineer.

I have an existing Laravel theme implemented as a reusable Composer package. I want you to analyse the package and implement a visible theme settings panel that allows application administrators or authorised users to customise the theme without editing configuration files or source code.

Do not assume how the package currently works. First inspect the existing package structure, service providers, configuration files, views, assets, routes, middleware, database usage, publishing logic, and frontend stack.

## Main Objective

Add a production-ready theme settings panel to the package.

The settings panel should allow authorised users to configure the appearance and behaviour of the theme through a graphical interface.

The implementation must remain reusable as a Laravel package and must not tightly couple the package to one host application.

## First Step: Analyse the Existing Package

Before making changes, inspect and document:

- The package namespace
- Composer package name
- Supported Laravel version
- PHP version
- Package service provider
- Existing configuration files
- Existing Blade layouts and components
- Existing CSS and JavaScript tooling
- Whether the package uses Blade, Livewire, Vue, React, Inertia, Filament, Tailwind CSS, Bootstrap, or another frontend framework
- Existing routes
- Existing middleware
- Existing authentication assumptions
- Existing authorisation or permission system
- Existing database migrations and models
- Existing package asset publishing
- Existing tests
- How the active theme is currently selected
- How theme variables are currently applied

After the analysis, provide a concise implementation plan before modifying files.

## Required Features

Create a visible theme settings page with the following configurable sections.

### 1. General Theme Settings

Include:

- Theme name or display label
- Light mode, dark mode, or system preference
- Default colour scheme
- Enable or disable rounded components
- Enable or disable shadows
- Enable or disable animations
- Compact or comfortable layout density
- Default content width
- Custom CSS class for the page body, where appropriate

### 2. Brand Settings

Include:

- Application or brand name
- Logo upload
- Dark-mode logo upload
- Favicon upload
- Primary brand colour
- Secondary brand colour
- Accent colour
- Link colour
- Success colour
- Warning colour
- Danger colour
- Information colour

Uploaded files must be validated, stored securely, and removable or replaceable.

### 3. Typography Settings

Include:

- Primary font family
- Heading font family
- Base font size
- Heading font weight
- Body font weight
- Line height
- Letter spacing

Use a controlled list of supported fonts unless the package already supports custom fonts.

Do not allow arbitrary unsafe CSS values without validation.

### 4. Layout Settings

Include:

- Sidebar enabled or disabled
- Sidebar position: left or right
- Sidebar width
- Sidebar collapsed by default
- Sticky sidebar
- Sticky header
- Header height
- Footer enabled or disabled
- Footer text
- Full-width or boxed layout
- Navigation style
- Breadcrumbs enabled or disabled

Only include settings that make sense for the current theme architecture. Clearly document any omitted settings.

### 5. Component Settings

Where supported by the existing theme, include:

- Button style
- Button border radius
- Card style
- Card border radius
- Form control style
- Table style
- Alert style
- Modal style
- Navigation item style

### 6. Custom Code Settings

Optionally include:

- Custom CSS
- Custom JavaScript
- Custom content for the document head

These options must be protected by a separate permission and clearly marked as advanced.

Do not render unsafe content for ordinary users.

Only trusted administrators should be able to use these fields.

Implement validation, sanitisation, and a configuration option that allows the host application to disable custom code completely.

### 7. Preview

Provide a live or near-live theme preview.

The preview should display representative UI components such as:

- Header
- Sidebar
- Buttons
- Cards
- Form inputs
- Alerts
- Tables
- Typography
- Navigation elements

Where practical, update the preview immediately when a setting changes.

Do not save changes automatically unless the existing package architecture already supports autosaving.

### 8. Reset and Restore

Include:

- Reset a single section
- Reset all settings to package defaults
- Confirmation before resetting
- Restore the last saved values after cancelling unsaved changes
- Optional export and import of theme settings as JSON

Imported settings must be validated before saving.

## Settings Storage Architecture

Implement a clean storage abstraction.

Create a contract such as:

```php
interface ThemeSettingsRepository
{
    public function all(): array;

    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    public function setMany(array $settings): void;

    public function forget(string $key): void;

    public function reset(): void;
}
```

Provide at least one production-ready storage driver.

Preferred default:

- Database-backed settings

Optional additional drivers:

- Configuration-only defaults
- Cache-backed reads
- File or JSON storage, if appropriate

The package configuration should allow the host application to select the settings driver.

Example:

```php
return [
    'settings' => [
        'driver' => env('THEME_SETTINGS_DRIVER', 'database'),
        'cache' => true,
        'cache_key' => 'theme-package.settings',
        'cache_ttl' => 3600,
    ],
];
```

Do not call `env()` outside configuration files.

## Database Design

Create a package migration for theme settings.

A recommended schema is:

```text
id
key
value
type
group
is_public
created_at
updated_at
```

Alternatively, use a JSON settings document if that is a better fit for the package.

The implementation should:

- Prevent duplicate setting keys
- Cast values correctly
- Support booleans, strings, integers, arrays, colours, and JSON
- Avoid storing uploaded files directly in the database
- Cache settings when appropriate
- Clear the relevant cache after updates
- Support package migration publishing

If the application is multi-tenant, inspect the project and make the settings architecture tenant-aware. Do not add tenancy assumptions when the application is not multi-tenant.

## Package Configuration

Add a publishable configuration file similar to:

```php
return [
    'enabled' => true,

    'route' => [
        'prefix' => 'admin/theme',
        'name' => 'theme-settings.',
        'middleware' => ['web', 'auth'],
    ],

    'authorization' => [
        'gate' => 'manage-theme-settings',
        'permission' => 'manage theme settings',
    ],

    'storage' => [
        'disk' => env('THEME_ASSET_DISK', 'public'),
        'directory' => 'theme-assets',
    ],

    'features' => [
        'live_preview' => true,
        'custom_css' => false,
        'custom_javascript' => false,
        'import_export' => true,
    ],
];
```

Ensure all configuration values have sensible defaults.

## Service Provider Integration

Update the package service provider to:

- Merge package configuration
- Load package views
- Load package routes
- Load package migrations
- Register the settings repository contract
- Register Blade components where applicable
- Register Livewire components where applicable
- Register package commands
- Register or document the authorisation gate
- Publish configuration
- Publish migrations
- Publish views
- Publish assets
- Publish translations
- Publish all resources using meaningful publish tags

Example publish tags:

```text
theme-package-config
theme-package-migrations
theme-package-views
theme-package-assets
theme-package-translations
```

Do not force users to publish views or assets unless customisation requires it.

## Authorisation

The settings panel must not be accessible to every authenticated user.

Use Laravel gates, policies, permissions, or a configurable authorisation callback.

The package should work even when Spatie Laravel Permission is not installed.

A suitable approach is:

1. Check a configured gate.
2. If a configured permission system is available, check the permission.
3. Allow the host application to provide a callback.
4. Deny access by default when no authorisation mechanism has been configured.

Add middleware protection to every settings route.

Return proper `403` responses for unauthorised users.

Do not rely only on hiding the navigation link.

## User Interface

Use the frontend technology already present in the package.

Preferred decision order:

1. Reuse the package's current UI stack.
2. If the package already uses Livewire, build the panel with Livewire.
3. If it uses Filament, create a Filament settings page or plugin page.
4. If it uses Inertia, use the existing Inertia frontend.
5. Otherwise, use Blade with lightweight JavaScript.

Do not introduce a large new frontend dependency unless it is necessary.

The page should include:

- A clear page title
- Tabbed or section-based navigation
- Save button
- Cancel or discard changes button
- Reset section button
- Reset all button
- Validation messages
- Success and error notifications
- Loading states
- Unsaved changes warning
- Responsive mobile layout
- Accessible labels
- Keyboard navigation
- Proper colour contrast

## Theme Rendering

Create a central theme settings service such as:

```php
ThemeManager
```

It should be responsible for:

- Reading saved settings
- Combining saved settings with package defaults
- Resolving the active colour mode
- Generating safe CSS variables
- Returning logo and favicon URLs
- Exposing settings to Blade views or frontend components
- Clearing cached settings
- Resetting values
- Importing and exporting settings

Expose theme settings through Blade where appropriate:

```php
app(ThemeManager::class)->get('colors.primary');
```

Also consider a Blade directive, helper, or view composer:

```php
@themeSetting('colors.primary')
```

Do not create global helper functions unless they are safely namespaced or intentionally loaded.

## CSS Variables

Prefer CSS custom properties instead of generating large dynamic stylesheets.

Example:

```css
:root {
  --theme-primary: #2563eb;
  --theme-secondary: #475569;
  --theme-accent: #7c3aed;
  --theme-success: #16a34a;
  --theme-warning: #d97706;
  --theme-danger: #dc2626;
  --theme-font-family: Inter, sans-serif;
  --theme-base-font-size: 16px;
  --theme-border-radius: 0.5rem;
}
```

Generate CSS variables from validated settings.

Escape all output correctly.

Do not render raw unvalidated values inside a style element.

Support dark-mode variables separately where appropriate.

## Forms and Validation

Create dedicated Laravel Form Request classes or component validation rules.

Validate:

- Colours as valid hexadecimal, RGB, HSL, or a deliberately restricted format
- Font sizes within safe minimum and maximum values
- Width and height values within safe ranges
- Enumerated options using Laravel `Rule::in`
- Boolean values
- Uploaded image MIME types
- Uploaded image dimensions
- Uploaded image sizes
- JSON import structure
- Custom CSS or JavaScript access permissions

Do not mass-assign unvalidated request data.

## File Uploads

For logos and favicons:

- Use the configured filesystem disk
- Store files in a configurable package directory
- Generate unique filenames
- Validate images
- Remove replaced files when safe
- Do not delete package default assets
- Work with both local and cloud storage
- Use Laravel Storage URLs
- Handle private disks appropriately
- Add tests for upload replacement and deletion

## Navigation Link

Provide a documented way to display the settings panel link in the host application.

Where possible, automatically register a navigation item through the package's existing navigation system.

Otherwise provide a Blade component such as:

```blade
<x-theme-package::settings-link />
```

The link should only be rendered when:

- The settings panel is enabled
- The user is authorised
- The route exists

Suggested label:

```text
Theme Settings
```

Suggested icon:

```text
paint brush, palette, or appearance icon
```

## Routes

Use configurable named routes.

Example:

```php
Route::middleware(config('theme-package.route.middleware'))
    ->prefix(config('theme-package.route.prefix'))
    ->name(config('theme-package.route.name'))
    ->group(function () {
        Route::get('/', ThemeSettingsController::class)->name('index');
        Route::put('/', [ThemeSettingsController::class, 'update'])->name('update');
        Route::post('/reset', [ThemeSettingsController::class, 'reset'])->name('reset');
        Route::get('/export', [ThemeSettingsController::class, 'export'])->name('export');
        Route::post('/import', [ThemeSettingsController::class, 'import'])->name('import');
    });
```

Avoid route name and URI collisions with the host application.

## Events

Dispatch package events where useful:

```text
ThemeSettingsUpdating
ThemeSettingsUpdated
ThemeSettingsReset
ThemeAssetUploaded
ThemeSettingsImported
```

Allow the host application to listen to these events.

Do not dispatch events containing unsafe or unnecessarily sensitive data.

## Console Commands

Add useful commands such as:

```bash
php artisan theme:settings:install
php artisan theme:settings:reset
php artisan theme:settings:export
php artisan theme:settings:clear-cache
```

The install command should assist with:

- Publishing configuration
- Publishing migrations
- Running migrations only after confirmation
- Publishing assets when required
- Displaying the settings panel URL
- Explaining authorisation setup

Do not automatically overwrite existing published files.

## Testing Requirements

Add comprehensive automated tests.

Include:

### Unit tests

- Default settings are returned correctly
- Saved values override defaults
- Invalid setting values are rejected
- Values are cast correctly
- Cache is cleared after updates
- CSS variables are generated safely
- Settings can be reset
- Import validation works
- Export output is valid JSON

### Feature tests

- Unauthenticated users are redirected or denied
- Unauthorised users receive `403`
- Authorised users can open the settings panel
- Authorised users can update settings
- Validation errors are displayed
- Logo upload works
- Replacing a logo removes the previous managed file
- Reset section works
- Reset all works
- Import and export work
- Package routes use the configured prefix
- Disabled features cannot be accessed
- Custom code fields require elevated authorisation

### Package compatibility tests

Where the package already uses Orchestra Testbench, extend the existing Testbench setup.

Test against the Laravel versions supported by `composer.json`.

Do not claim the feature is complete until the test suite passes.

## Documentation

Update the package README with:

- Installation
- Publishing configuration
- Publishing and running migrations
- Required storage link setup
- Route configuration
- Middleware configuration
- Gate or permission configuration
- How to display the settings navigation link
- How to retrieve theme settings
- How to override default settings
- How to publish views
- How to publish assets
- How to customise available settings
- How to disable dangerous features
- How to use import and export
- Multi-tenant usage, only if supported
- Troubleshooting

Include code examples.

## Backward Compatibility

The existing theme package must continue to work after this change.

Requirements:

- Existing applications should use package defaults when no database settings exist
- Existing configuration values must not be silently discarded
- Existing Blade components and layouts must continue to render
- Avoid breaking public classes, methods, routes, config keys, or view names
- Document any unavoidable breaking changes
- Add an upgrade guide if necessary

## Security Requirements

Ensure:

- Every settings route is authorised
- CSRF protection remains enabled
- Uploaded files are validated
- Unsafe file extensions are rejected
- Settings are escaped before rendering
- CSS values are restricted and validated
- Custom JavaScript is disabled by default
- Custom HTML is disabled by default
- Import files cannot set unsupported keys
- Mass assignment is prevented
- Path traversal is prevented
- Uploaded filenames are generated by the application
- Database queries are parameterised through Laravel
- The panel does not expose environment variables or secrets
- Package configuration is not editable through the UI unless explicitly safe

## Expected File Structure

Adapt this structure to the existing package:

```text
src/
├── Commands/
│   ├── InstallThemeSettingsCommand.php
│   ├── ResetThemeSettingsCommand.php
│   └── ClearThemeSettingsCacheCommand.php
├── Contracts/
│   └── ThemeSettingsRepository.php
├── Events/
│   ├── ThemeSettingsUpdated.php
│   └── ThemeSettingsReset.php
├── Http/
│   ├── Controllers/
│   │   └── ThemeSettingsController.php
│   └── Requests/
│       ├── UpdateThemeSettingsRequest.php
│       └── ImportThemeSettingsRequest.php
├── Models/
│   └── ThemeSetting.php
├── Repositories/
│   └── DatabaseThemeSettingsRepository.php
├── Services/
│   └── ThemeManager.php
├── Support/
│   ├── SettingDefinition.php
│   └── ThemeCssVariables.php
├── View/
│   └── Components/
│       └── SettingsLink.php
└── ThemeServiceProvider.php

config/
└── theme-package.php

database/
└── migrations/
    └── create_theme_settings_table.php.stub

resources/
├── views/
│   ├── settings/
│   │   └── index.blade.php
│   └── components/
│       └── settings-link.blade.php
├── js/
├── css/
└── lang/

routes/
└── web.php

tests/
├── Feature/
└── Unit/
```

Do not create unnecessary classes. Adapt the design to the package's existing conventions.

## Implementation Process

Follow this order:

1. Inspect the complete package.
2. Report the current architecture.
3. Identify relevant existing functionality.
4. Describe the proposed implementation.
5. List files that will be created or modified.
6. Implement the settings storage layer.
7. Implement configuration and service-provider registration.
8. Implement routes and authorisation.
9. Implement the settings UI.
10. Connect saved settings to the theme rendering system.
11. Implement uploads.
12. Implement reset, import, and export.
13. Add tests.
14. Run formatting and static analysis.
15. Run the complete test suite.
16. Update documentation.
17. Perform a final production-readiness review.

## Required Quality Checks

Run the relevant commands available in the project, such as:

```bash
composer validate
composer test
vendor/bin/phpunit
vendor/bin/pest
vendor/bin/phpstan analyse
vendor/bin/pint --test
npm run build
npm run lint
```

Use only commands supported by the existing project.

Fix failures introduced by your changes.

Do not modify unrelated code merely to make old unrelated tests pass without explaining the issue.

## Final Response Format

When finished, provide:

1. Existing package architecture summary
2. Implementation summary
3. Files created
4. Files modified
5. Database changes
6. New configuration options
7. New routes
8. Authorisation setup
9. How settings are applied to the theme
10. Installation and upgrade instructions
11. Commands executed
12. Test results
13. Remaining limitations
14. Security considerations
15. Manual verification checklist

## Definition of Done

The work is complete only when:

- The settings page is visible to authorised users
- Unauthorised users cannot access it
- Settings can be saved successfully
- Saved settings visibly affect the theme
- Defaults are used when no saved settings exist
- Logo and favicon uploads work securely
- Settings can be reset
- Validation prevents invalid values
- Cache invalidation works
- Package routes are configurable
- Package resources can be published
- Existing package functionality remains operational
- Automated tests cover the main workflows
- The test suite passes
- Documentation explains installation and usage
- No secrets or unsafe unvalidated values are exposed
- The implementation remains reusable across Laravel applications

Begin by analysing the package. Do not start coding until you have inspected the existing architecture and produced the implementation plan.
