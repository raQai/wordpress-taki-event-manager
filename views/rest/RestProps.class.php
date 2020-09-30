<?php

namespace BIWS\TaKiEventManager\views\rest;

class RestProps
{
    private string $namespace;

    private string $route;

    private array $args;

    private bool $override;

    public function __construct(
        string $namespace,
        string $route,
        array $args = array(),
        bool $override = false
    ) {
        $this->namespace = $this->sanitize($namespace);
        $this->route = $this->sanitize($route);
        $this->args = $args;
        $this->override = $override;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getEndpoint(): string
    {
        return "{$this->namespace}/{$this->route}";
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function isOverride(): bool
    {
        return $this->override;
    }

    private function sanitize(string $value): string
    {
        return trim(str_replace('\\', '/', $value), '/');
    }
}
