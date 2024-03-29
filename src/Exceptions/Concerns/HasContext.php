<?php
namespace Jhu\Wse\LaravelShibboleth\Exceptions\Concerns;

trait HasContext
{
    public ?array $context = null;

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function context(): ?array
    {
        return $this->context;
    }
}
