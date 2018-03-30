<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$framework = new \JKocik\Laravel\Profiler\Tests\Support\Framework();

require_once bootstrapFramework($framework);

bootstrapFrameworkEnv($framework);

/**
 * @param \JKocik\Laravel\Profiler\Tests\Support\Framework $framework
 * @return string
 */
function bootstrapFramework(\JKocik\Laravel\Profiler\Tests\Support\Framework $framework): string
{
    $frameworkAutoloadFile = __DIR__ . '/../../frameworks/' . $framework->dir() . '/vendor/autoload.php';

    if (! file_exists($frameworkAutoloadFile)) {
        shell_exec('composer create-project --no-dev ' . $framework->composerPackage() . ' ./frameworks/' . $framework->dir());
    }

    return $frameworkAutoloadFile;
}

/**
 * @param \JKocik\Laravel\Profiler\Tests\Support\Framework $framework
 * @return void
 */
function bootstrapFrameworkEnv(\JKocik\Laravel\Profiler\Tests\Support\Framework $framework): void
{
    $frameworkPath = __DIR__ . '/../../frameworks/' . $framework->dir();

    if (! file_exists($frameworkPath . '/.env')) {
        copy($frameworkPath . '/.env.example', $frameworkPath . '/.env');
    }
}
