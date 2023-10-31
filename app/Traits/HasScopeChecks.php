<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasScopeChecks
{
    protected function isCheckScopeMethod(string $method): bool
    {
        return Str::startsWith($method, 'is') || Str::startsWith($method, 'has');
    }

    protected function getOriginalScopeMethodName($checkMethodName): string
    {
        return preg_replace('/^(is|has)(.*)$/m', '$2', $checkMethodName);
    }

    protected function forwardCallTo($object, $method, $parameters)
    {
        if ($this->isCheckScopeMethod($method)) {
            $originalScopeMethodName = $this->getOriginalScopeMethodName($method);

            if (method_exists($this, "scope{$originalScopeMethodName}")) {
                $builder = $this->newQuery()->where($this->getKeyName(), $this->getKey());

                $builder = call_user_func_array([$builder, $originalScopeMethodName], $parameters);

                return $builder->exists();
            }
        }

        return parent::forwardCallTo($object, $method, $parameters);
    }
}
