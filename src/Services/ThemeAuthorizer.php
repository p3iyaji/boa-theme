<?php

declare(strict_types=1);

namespace Boa\Theme\Services;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

final class ThemeAuthorizer
{
    public function __construct(
        private readonly ConfigRepository $config,
        private readonly Gate $gate,
    ) {}

    public function canManage(?Authenticatable $user): bool
    {
        if ($user === null) {
            return false;
        }

        $callback = $this->config->get('boa-theme.settings.authorization.callback');

        if (is_callable($callback)) {
            return (bool) $callback($user);
        }

        if (is_string($callback) && $callback !== '' && app()->bound($callback)) {
            $resolved = app($callback);

            if (is_callable($resolved)) {
                return (bool) $resolved($user);
            }
        }

        $gateName = $this->config->get('boa-theme.settings.authorization.gate');

        if (is_string($gateName) && $gateName !== '' && $this->gate->has($gateName)) {
            return $this->gate->forUser($user)->allows($gateName);
        }

        $permission = $this->config->get('boa-theme.settings.authorization.permission');

        if (is_string($permission) && $permission !== '' && method_exists($user, 'can')) {
            /** @phpstan-ignore-next-line */
            if ($user->can($permission)) {
                return true;
            }
        }

        // Deny by default when no authorisation mechanism is configured.
        return false;
    }

    public function canManageCustomCode(?Authenticatable $user): bool
    {
        if (! $this->canManage($user)) {
            return false;
        }

        $gateName = $this->config->get('boa-theme.settings.authorization.custom_code_gate');

        if (is_string($gateName) && $gateName !== '' && $this->gate->has($gateName)) {
            return $this->gate->forUser($user)->allows($gateName);
        }

        // If custom-code gate is not defined, fall back to manage permission
        // only when custom features are explicitly enabled in config.
        return (bool) $this->config->get('boa-theme.settings.features.custom_css', false)
            || (bool) $this->config->get('boa-theme.settings.features.custom_javascript', false)
            || (bool) $this->config->get('boa-theme.settings.features.custom_head', false);
    }
}
