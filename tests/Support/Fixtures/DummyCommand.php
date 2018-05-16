<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use Illuminate\Console\Command;

class DummyCommand extends Command
{
    /**
     * @var int
     */
    protected $testExitCode;

    /**
     * DummyCommand constructor.
     * @param int $testExitCode
     */
    public function __construct(int $testExitCode)
    {
        parent::__construct();

        $this->testExitCode = $testExitCode;
    }

    /**
     * @var string
     */
    protected $signature = 'dummy-command {user?} {--number=}';

    /**
     * @var string
     */
    protected $description = 'Display dummy message';

    /**
     * @return void
     */
    public function handle()
    {
        $this->comment('Dummy Command Message');

        return $this->testExitCode;
    }
}
