<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class DummyEventB
{
    use SerializesModels;

    /**
     * @var Model
     */
    public $user;

    /**
     * @var Collection
     */
    public $usersA;

    /**
     * @var array
     */
    public $usersB;

    /**
     * @var array
     */
    public $dummyClasses;

    /**
     * @var array
     */
    public $dataA;

    /**
     * @var string
     */
    public $dataB;

    /**
     * DummyEventB constructor.
     * @param Model $user
     * @param Collection $usersA
     * @param array $usersB
     * @param array $dummyClasses
     * @param array $dataA
     * @param string $dataB
     */
    public function __construct(
        Model $user,
        Collection $usersA,
        array $usersB,
        array $dummyClasses,
        array $dataA,
        string $dataB
    ) {
        $this->user = $user;
        $this->usersA = $usersA;
        $this->usersB = $usersB;
        $this->dummyClasses = $dummyClasses;
        $this->dataA = $dataA;
        $this->dataB = $dataB;
    }
}
