<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\LaravelExecution\HttpRoute;
use JKocik\Laravel\Profiler\LaravelExecution\NullRoute;
use JKocik\Laravel\Profiler\LaravelExecution\HttpRequest;
use JKocik\Laravel\Profiler\LaravelExecution\HttpSession;
use JKocik\Laravel\Profiler\LaravelExecution\HttpResponse;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyController;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyFormRequest;

class LaravelHttpExecutionTest extends TestCase
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->turnOffProcessors();

        $this->executionData = $this->app->make(ExecutionData::class);
    }

    /** @test */
    function has_http_request()
    {
        $this->get('/');
        $request = $this->executionData->request();

        $this->assertInstanceOf(HttpRequest::class, $request);
    }

    /** @test */
    function has_request_type()
    {
        $this->get('/');
        $request = $this->executionData->request();

        $this->assertEquals('http', $request->meta()->get('type'));
    }

    /** @test */
    function has_request_method()
    {
        $this->post('/', []);
        $request = $this->executionData->request();

        $this->assertEquals('POST', $request->meta()->get('method'));
    }

    /** @test */
    function has_request_path()
    {
        $this->get('/abc/xyz');
        $request = $this->executionData->request();

        $this->assertEquals('abc/xyz', $request->meta()->get('path'));
    }

    /** @test */
    function has_request_is_ajax()
    {
        $this->get('/');
        $this->assertFalse($this->executionData->request()->meta()->get('ajax'));

        $this->get('/', ['X-Requested-With' => 'XMLHttpRequest']);
        $this->assertTrue($this->executionData->request()->meta()->get('ajax'));
    }

    /** @test */
    function has_request_is_json()
    {
        $this->get('/');
        $this->assertFalse($this->executionData->request()->meta()->get('json'));

        $this->get('/', ['Content-Type' => 'application/json']);
        $this->assertTrue($this->executionData->request()->meta()->get('json'));
    }

    /** @test */
    function has_request_is_pjax()
    {
        $this->get('/');
        $this->assertFalse($this->executionData->request()->data()->get('pjax'));

        $this->get('/', ['X-PJAX' => true]);
        $this->assertTrue($this->executionData->request()->data()->get('pjax'));
    }

    /** @test */
    function has_request_url()
    {
        $this->get('/abc?ab=xy');
        $request = $this->executionData->request();

        $this->assertEquals("{$this->app->make('request')->root()}/abc", $request->data()->get('url'));
    }

    /** @test */
    function has_request_query()
    {
        $this->get('/');
        $this->assertEquals([], $this->executionData->request()->data()->get('query'));

        $this->get('/abc?ab=xy');
        $this->assertEquals(['ab' => 'xy'], $this->executionData->request()->data()->get('query'));
    }

    /** @test */
    function has_request_ip()
    {
        $this->get('/');
        $request = $this->executionData->request();

        $this->assertEquals($this->app->make('request')->ip(), $request->data()->get('ip'));
    }

    /** @test */
    function has_request_server()
    {
        $this->get('/');
        $request = $this->executionData->request();

        $this->assertEquals($this->app->make('request')->server(), $request->data()->get('server'));
    }

    /** @test */
    function has_request_header()
    {
        $this->get('/');
        $request = $this->executionData->request();

        $this->assertEquals($this->app->make('request')->header(), $request->data()->get('header'));
    }

    /** @test */
    function has_request_input()
    {
        $this->post('/', [
            'key-a' => 'val-a',
            'key-b' => 'val-b',
        ]);
        $request = $this->executionData->request();

        $this->assertEquals('val-a', $request->data()->get('input')['key-a']);
        $this->assertEquals('val-b', $request->data()->get('input')['key-b']);
    }

    /** @test */
    function has_request_all_files()
    {
        $this->tapLaravelVersionTill(5.3, function () {
            $this->assertTrue(true);
        });

        $this->tapLaravelVersionFrom(5.4, function () {
            $fileA = UploadedFile::fake()->image('file-val-a.jpg');
            $fileB = UploadedFile::fake()->image('file-val-a.jpg');

            $this->post('/', [
                'file-key-a' => $fileA,
                'file-key-b' => $fileB,
            ]);
            $request = $this->executionData->request();
            $this->assertEquals([
                'client_original_name' => $fileA->getClientOriginalName(),
                'client_original_extension' => $fileA->getClientOriginalExtension(),
                'client_mime_type' => $fileA->getClientMimeType(),
                'client_size' => $fileA->getClientSize(),
                'path' => $fileA->path(),
            ], $request->data()->get('files')['file-key-a']);
            $this->assertEquals([
                'client_original_name' => $fileB->getClientOriginalName(),
                'client_original_extension' => $fileB->getClientOriginalExtension(),
                'client_mime_type' => $fileB->getClientMimeType(),
                'client_size' => $fileB->getClientSize(),
                'path' => $fileB->path(),
            ], $request->data()->get('files')['file-key-b']);
        });
    }

    /** @test */
    function has_request_cookie()
    {
        $this->call('GET', '/', [], ['cookie-key-a' => Crypt::encrypt('cookie-val-a')]);
        $request = $this->executionData->request();

        $this->assertEquals(['cookie-key-a' => 'cookie-val-a'], $request->data()->get('cookie'));
    }

    /** @test */
    function has_http_route()
    {
        $this->get('/');
        $route = $this->executionData->route();

        $this->assertInstanceOf(HttpRoute::class, $route);
    }

    /** @test */
    function has_null_route_when_route_is_not_matched()
    {
        $this->get('/not-found');
        $route = $this->executionData->route();

        $this->assertInstanceOf(NullRoute::class, $route);
    }

    /** @test */
    function has_route_methods()
    {
        Route::get('route-a/{id}', function ($id) {
            return $id;
        });

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertEquals(['GET', 'HEAD'], $route->data()->get('methods'));
    }

    /** @test */
    function has_route_uri()
    {
        Route::get('route-a/{id}', function ($id) {
            return $id;
        });

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertEquals('route-a/{id}', $route->data()->get('uri'));
    }

    /** @test */
    function has_route_name()
    {
        Route::get('route-a/{id}', function ($id) {
            return $id;
        })->name('route.a.with.id');

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertEquals('route.a.with.id', $route->data()->get('name'));
    }

    /** @test */
    function has_route_middleware()
    {
        Route::get('route-a/{id}', function ($id) {
            return $id;
        })->middleware('auth');

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertEquals(['auth'], $route->data()->get('middleware'));
    }

    /** @test */
    function has_route_parameters()
    {
        Route::get('route-a/{id}', function ($id) {
            return $id;
        });

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertEquals(['id' => '123'], $route->data()->get('parameters'));
    }

    /** @test */
    function has_route_prefix()
    {
        Route::group(['prefix' => 'admin'], function () {
            Route::get('route-a/{id}', function ($id) {
                return $id;
            });
        });

        $this->get('/admin/route-a/123');
        $route = $this->executionData->route();

        $this->assertEquals('admin', $route->data()->get('prefix'));
    }

    /** @test */
    function has_route_regex()
    {
        Route::get('route-a/{id}', function ($id) {
            return $id;
        })->where('id', '[0-9]+');

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertContains('/route\-a', $route->data()->get('regex'));
        $this->assertContains('<id>[0-9]+', $route->data()->get('regex'));
    }

    /** @test */
    function has_route_closure_action()
    {
        $uses = function (DummyFormRequest $request) {
            return $request->get('id');
        };
        $action = new ReflectionFunction($uses);

        Route::get('route-a/{id}', $uses);

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertContains(
            'LaravelHttpExecutionTest.php:' . $action->getStartLine() . '-' . $action->getEndLine(),
            $route->data()->get('uses')['closure']
        );
        $this->assertEquals(
            DummyFormRequest::class,
            $route->data()->get('uses')['form_request']
        );
    }

    /** @test */
    function has_route_controller_action()
    {
        Route::get('route-a/{id}', '\JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyController@dummyAction');
        $action = new ReflectionMethod(DummyController::class, 'dummyAction');

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertContains(
            'DummyController@dummyAction:' . $action->getStartLine() . '-' . $action->getEndLine(),
            $route->data()->get('uses')['controller']
        );
        $this->assertEquals(
            DummyFormRequest::class,
            $route->data()->get('uses')['form_request']
        );
    }

    /** @test */
    function has_route_without_form_request_if_form_request_is_not_defined()
    {
        $uses = function ($id) {
            return $id;
        };

        Route::get('route-a/{id}', $uses);

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertEquals('', $route->data()->get('uses')['form_request']);
    }

    /** @test */
    function has_route_with_not_existing_controller()
    {
        Route::get('route-a/{id}', '\JKocik\Laravel\Profiler\Tests\Support\Fixtures\NotExistingController@dummyAction');

        $this->tapLaravelVersionTill(5.2, function () {
            $this->get('/route-a/123');
            $route = $this->executionData->route();

            $this->assertInstanceOf(NullRoute::class, $route);
        });

        $this->tapLaravelVersionBetween(5.3, 5.3, function () {
            try {
                $this->get('/route-a/123');
            } catch (ReflectionException $e) {
                $this->assertTrue(true);
            }
        });

        $this->tapLaravelVersionFrom(5.4, function () {
            $this->get('/route-a/123');
            $route = $this->executionData->route();

            $this->assertEquals([], $route->data()->get('uses'));
        });
    }

    /** @test */
    function has_http_session()
    {
        $this->get('/');
        $session = $this->executionData->session();

        $this->assertInstanceOf(HttpSession::class, $session);
    }

    /** @test */
    function has_http_session_data()
    {
        $this->withSession([
            'abc' => 123,
            'xyz' => 789,
        ])->get('/');
        $session = $this->executionData->session();

        $this->assertEquals(123, $session->data()->get('abc'));
        $this->assertEquals(789, $session->data()->get('xyz'));
    }

    /** @test */
    function has_http_response()
    {
        $this->get('/');
        $response = $this->executionData->response();

        $this->assertInstanceOf(HttpResponse::class, $response);
    }

    /** @test */
    function has_response_status()
    {
        $this->get('/i-can-not-find-that-page');
        $response = $this->executionData->response();

        $this->assertEquals(404, $response->meta()->get('status'));
    }

    /** @test */
    function has_response_headers()
    {
        $this->get('/');
        $response = $this->executionData->response();

        $this->assertArrayHasKey('content-type', $response->data()->get('headers'));
    }

    /** @test */
    function has_response_http_string()
    {
        $this->get('/');
        $response = $this->executionData->response();

        $this->assertContains('HTTP/1.1 200 OK', $response->data()->get('as_http_string'));
        $this->assertContains('</body>', $response->data()->get('as_http_string'));
        $this->assertContains('</html>', $response->data()->get('as_http_string'));
    }
}
