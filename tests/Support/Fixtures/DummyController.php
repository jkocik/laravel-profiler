<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use App\Http\Controllers\Controller;

class DummyController extends Controller
{
    /**
     * @param DummyFormRequest $request
     * @return mixed
     */
    public function dummyAction(DummyFormRequest $request)
    {
        return $request->get('id');
    }
}
