<?php

require_once __DIR__ . '/../../vendor/autoload.php';

require_once bootstrapFramework();

bootstrapFrameworkEnv();

/**
 * @return string
 */
function bootstrapFramework(): string
{
    $frameworkAutoload = __DIR__ . '/../../frameworks/laravel-55/vendor/autoload.php';

    if (! file_exists($frameworkAutoload)) {
        shell_exec("composer create-project --no-dev laravel/laravel:5.5.* ./frameworks/laravel-55");
    }

    return $frameworkAutoload;
}

/**
 * @return void
 */
function bootstrapFrameworkEnv(): void
{
    $frameworkPath = __DIR__ . '/../../frameworks/laravel-55';

    if (! file_exists($frameworkPath . '/.env')) {
        copy($frameworkPath . '/.env.example', $frameworkPath . '/.env');
    }
}
