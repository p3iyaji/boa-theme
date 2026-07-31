<?php

declare(strict_types=1);

namespace Boa\Theme\Http\Middleware;

use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizeThemeSettings
{
    public function __construct(
        private readonly ThemeManager $manager,
        private readonly ThemeAuthorizer $authorizer,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->manager->panelEnabled()) {
            abort(404);
        }

        if (! $this->authorizer->canManage($request->user())) {
            abort(403, 'You are not authorised to manage theme settings.');
        }

        return $next($request);
    }
}
