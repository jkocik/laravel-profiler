<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\Tracker;

abstract class BaseTracker implements Tracker
{
    /**
     * @var Collection
     */
    protected $meta;

    /**
     * @var Collection
     */
    protected $data;

    /**
     * Tracker constructor.
     */
    public function __construct()
    {
        $this->meta = new Collection();
        $this->data = new Collection();
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return $this->meta;
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return $this->data;
    }
}
