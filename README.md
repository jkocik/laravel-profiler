## Laravel Profiler

[![Build Status](https://travis-ci.org/jkocik/laravel-profiler.svg?branch=master)](https://travis-ci.org/jkocik/laravel-profiler)
[![Coverage Status](https://coveralls.io/repos/github/jkocik/laravel-profiler/badge.svg?branch=master)](https://coveralls.io/github/jkocik/laravel-profiler?branch=master)

The aim of this project is to track console and web Laravel framework execution and give developers
better understanding what is going on under the hood. Laravel Profiler is designed for Laravel 5.2+.

### How does it work?

Laravel Profiler delivers data about Laravel framework execution:
- when Laravel is executed via console (artisan)
- when Laravel is executed via browser request
- when Laravel is executed via web request not expecting HTML response (API)
- when tests are run (PHPUnit, Laravel Dusk)
- on any other action that terminates Laravel framework.

Laravel Profiler is divided into 3 parts:
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
- route
- views
- events
- session
- exceptions
- server status
- database queries
- request (web) / input (console)
- response (web) / output (console)
- application (Laravel status, config, loaded service providers, framework paths)

## Installation and configuration

### Step 1: Profiler Package installation

Requirements: PHP 7.1+ and Laravel 5.2+

It is recommended to install Profiler Package only for development

```
composer require jkocik/laravel-profiler --dev
```

... then add Service Provider to your providers array in config/app.php

```
'providers' => [
    ...    
    JKocik\Laravel\Profiler\ServiceProvider::class,
];
``` 

... and publish profiler.php config file to your config directory

```
php artisan vendor:publish --provider="JKocik\Laravel\Profiler\ServiceProvider"
```

Check profiler.php config file for Laravel Profiler settings.
Uncomment trackers you would like to use.

### Step 2: Profiler Client and Profiler Server installation

It is recommended to install Profiler Client and Profiler Server only for development

```
npm install laravel-profiler-client --save-dev
```

... then add new scripts to your package.json file

```
"scripts": {
    ...
    "profiler-server": "node node_modules/laravel-profiler-client/server/server.js http=8099 ws=1901",
    "profiler-client": "http-server node_modules/laravel-profiler-client/dist/",
    "ps": "npm run profiler-server",
    "pc": "npm run profiler-client"    
},
```

... then run Profiler Server

```
npm run ps
```

... and Profiler Client

```
npm run pc
```

Open new tab in you browser according to Profiler Client instructions given in console.

### Step 3: Configuration

You should be able to use Laravel Profiler without any other configuration. If you need to change
ports for HTTP and WebSockets protocols you can do it in profiler.php file, in your npm scripts
and using Laravel Client GUI (top right plugin icon).

### Done!
 
You are ready to use Laravel Profiler. Enjoy!

### Usage

#### Laravel Profiler for testing

When testing Profiler will deliver the same data as for regular request. However application should be
terminated. Lets see two default tests Laravel is shipped with:

```
public function testBasicTest()
{
    $response = $this->get('/');

    $response->assertStatus(200);
}
``` 

First test will terminate application and Profiler will work as expected. However second test

```
public function testBasicTest()
{
    $this->assertTrue(true);
}
```

... will not provide any data because this time application is not terminated. You can force
Profiler to work by adding terminate method:

```
public function testBasicTest()
{
    $this->assertTrue(true);
    
    $this->app->terminate();
}
```

Important notice: It is not possible to separate peak of memory usage for each test.
Anyway peak of memory usage is still visible (gray light font color).

#### Using together with Laravel Debugbar

It is not recommended using Laravel Profiler and Laravel Debugbar together. Profiler will always finish
it's work after Debugbar and Profiler execution time, peak of memory usage and tracked events will
be increased by Debugbar activity. Use Profiler or Debugbar one at a time.
