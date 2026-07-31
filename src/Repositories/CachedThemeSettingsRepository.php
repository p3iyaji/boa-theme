<?php

declare(strict_types=1);

namespace Boa\Theme\Repositories;

use Boa\Theme\Contracts\ThemeSettingsRepository;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final class CachedThemeSettingsRepository implements ThemeSettingsRepository
{
    public function __construct(
        private readonly ThemeSettingsRepository $repository,
        private readonly CacheRepository $cache,
        private readonly string $cacheKey,
        private readonly int $ttl,
    ) {}

    public function all(): array
    {
        return $this->cache->remember(
            $this->cacheKey,
            $this->ttl,
            fn (): array => $this->repository->all(),
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->repository->set($key, $value);
        $this->forgetCache();
    }

    public function setMany(array $settings): void
    {
        $this->repository->setMany($settings);
        $this->forgetCache();
    }

    public function forget(string $key): void
    {
        $this->repository->forget($key);
        $this->forgetCache();
    }

    public function reset(): void
    {
        $this->repository->reset();
        $this->forgetCache();
    }

    public function forgetCache(): void
    {
        $this->cache->forget($this->cacheKey);
    }
}
