## Laravel Profiler

[![Build Status](https://travis-ci.org/jkocik/laravel-profiler.svg?branch=master)](https://travis-ci.org/jkocik/laravel-profiler)
[![Coverage Status](https://coveralls.io/repos/github/jkocik/laravel-profiler/badge.svg?branch=master)](https://coveralls.io/github/jkocik/laravel-profiler?branch=master)

The aim of this project is to track console and web Laravel framework execution and give developers
better understanding what is going on under the hood. Laravel Profiler is designed for Laravel Framework.

![Laravel Profiler](https://drive.google.com/uc?export=view&id=12TSAEW1butnLfmFxO3Pw4GhF96B8PUSk)

### Supported versions

| Laravel Framework version you have | Laravel Profiler version you should use |
| ---------------------------------- | --------------------------------------- |
| 5.2.x - 5.8.x | 1.x |
| 6.x - 8.x | 2.x |

### How does it work?

Profiler delivers data about Laravel framework execution:
- when **tests are run** (PHPUnit, Laravel Dusk)
- when Laravel is executed via **console** (artisan)
- when Laravel is executed via **browser request**
- when Laravel is executed via web request not expecting HTML response (**API**)
- on any other **action that terminates** Laravel framework.

Profiler does not add any routes to your application and does not modify the content of the response.

Profiler is divided into 3 parts:
- Profiler Package - PHP package for Laravel (this repository)
- Profiler Client - Single Page Application to review data delivered by Profiler Package
- Profiler Server - bridge between Profiler Package and Profiler Client.

Profiler Client and Profiler Server both live in [laravel-profiler-client](https://github.com/jkocik/laravel-profiler-client) repository

#### Data flow

Profiler Package tracks Laravel execution and sends collected data to Profiler Server using HTTP.
Profiler Server passes data to Profiler Client using WebSockets.

#### Trackers

Data tracked, collected and delivered to Profiler Client are:
- auth
- redis
- route
- views
- events
- session
- exceptions
- server status
- database queries
- performance metrics
- request (web) / input (console)
- response (web) / output (console)
- application (Laravel status, config, loaded service providers, container bindings, framework paths)

Profiler and its trackers do their job after request / artisan command is finished.
That keeps your framework execution time and peak of memory usage as close to real values (without Profiler impact)
as possible.

## Installation and configuration

### Step 1: Install Profiler Package

Requirements: PHP 7.2+

It is recommended to install Profiler Package only for development

```shell
composer require jkocik/laravel-profiler --dev
```

### Step 2: Publish configuration file

Run command

```shell
php artisan vendor:publish --provider="JKocik\Laravel\Profiler\ServiceProvider"
```

... and check config/profiler.php file for Profiler settings.

### Step 3: Install Profiler Server and Profiler Client

It is recommended to install Profiler Server and Profiler Client only for development

```shell
npm install laravel-profiler-client --save-dev
```

### Step 4: Run Profiler Server and Profiler Client

_Windows users: If you have any issue with running Profiler Server or Profiler Client
check Installation options / issues section below._

Run command

```shell
php artisan profiler:server
```
and

a) for your local machine

```shell
php artisan profiler:client
```

After that your browser should have new tab opened with Profiler Client connected to Profiler Server.

b) for Docker, Vagrant or any other machine different from local

```shell
php artisan profiler:client -m
```

... and open new browser tab according to instructions in console. Remember that you need
to connect Profiler Client to Profiler Server yourself because by default Profiler Client uses localhost.
You can do that in Profiler Client interface.

### Step 5: Verify installation

Run command

```shell
php artisan profiler:status
```

... to check Profiler status and see first data of Laravel execution in Profiler Client.

### Installation options / issues

a) If you have any issue with running Profiler Server or Profiler Client use npm scripts instead of artisan commands.
Add new scripts to your package.json file

```json
"scripts": {
    "profiler-server": "node node_modules/laravel-profiler-client/server/server.js http=8099 ws=1901",
    "profiler-client": "http-server node_modules/laravel-profiler-client/dist/ -o -s",
    "ps": "npm run profiler-server",
    "pc": "npm run profiler-client"    
}
```

... then run Profiler Server

```shell
npm run ps
```

... and Profiler Client

```shell
npm run pc
```

b) If you don't want to open new browser tab every time you run Profiler Client command use manual option

```shell
php artisan profiler:client -m
```

c) If default ports used by Profiler are taken on your machine configure them in config/profiler.php file.

### Done!
 
You are ready to use Laravel Profiler. Enjoy!

### Usage

#### Performance metrics

Profiler delivers basic performance metrics including peak of memory usage and Laravel execution time.
You can extend metrics by using Profiler helper functions:

```php
profiler_start('my time metric name');

// my code to track execution time

profiler_finish('my time metric name');
``` 

Then check results in Profiler Client (Performance > Custom tab). You should keep unique metric names
otherwise duplicates will be skipped and reported as an error (in a way according to your exception handling
settings in config/profiler.php file).

_Important notice: remove Profiler helper functions from your code
before moving to production or any environment without Profiler installed._

#### Laravel Profiler for testing environment

When testing Profiler will deliver the same data as for regular request / artisan command. However application should be
terminated. Lets see two default tests Laravel is shipped with:

```php
public function testBasicTest()
{
    $response = $this->get('/');

    $response->assertStatus(200);
}
``` 

First test will terminate application and Profiler will work as expected. However second test

```php
public function testBasicTest()
{
    $this->assertTrue(true);
}
```

... will not provide any data because this time application is not terminated. You can force
Profiler to work by adding terminate method:

```php
public function testBasicTest()
{
    $this->assertTrue(true);
    
    $this->app->terminate();
}
```

If you want to reset Profiler trackers you can use Profiler helper:
```php
public function testBasicTest()
{
    factory(User::class)->create();
    
    profiler_reset();
    
    // act and assert
}
```

_Important notice related to testing environment: peak of memory usage can not be tracked for each test separately
so is not shown in Profiler Client._

#### Using together with Laravel Debugbar

It is not recommended using Laravel Profiler and Laravel Debugbar together. Profiler will finish
its work after Debugbar and Profiler report of framework execution time and peak of memory usage will
be increased by Debugbar activity. Use Profiler or Debugbar one at a time.
