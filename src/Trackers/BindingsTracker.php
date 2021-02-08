<?php

namespace JKocik\Laravel\Profiler\Trackers;

use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Container\BindingResolutionException;

class BindingsTracker extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $bindings = $this->abstracts()->map(function ($abstract) {
            try {
                $resolved = $this->resolved($abstract);
            } catch (InvalidArgumentException $e) {
            } catch (BindingResolutionException $e) {}

            return [
                'abstract' => $abstract,
                'resolved' => $resolved ?? null,
            ];
        });

        $this->data->put('bindings', $bindings);
    }

    /**
     * @return Collection
     */
    protected function abstracts(): Collection
    {
        return Collection::make(
            array_keys($this->app->getBindings())
        );
    }

    /**
     * @param string $abstract
     * @return string
     * @throws InvalidArgumentException
     */
    protected function resolved(string $abstract): string
    {
        if (! $this->app->resolved($abstract)) {
            throw new InvalidArgumentException();
        }

        $concrete = $this->app->make($abstract);

        if (is_object($concrete)) {
            return get_class($concrete);
        }

        return gettype($concrete);
    }
}
