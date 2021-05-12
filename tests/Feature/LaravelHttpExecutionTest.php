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
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyClassA;
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
    protected function setUp(): void
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
        $this->get('/abc?ab=xy');
        $this->assertEquals(['ab' => 'xy'], $this->executionData->request()->data()->get('query'));
    }

    /** @test */
    function can_have_empty_request_query()
    {
        $this->get('/');
        $this->assertEquals([], $this->executionData->request()->data()->get('query'));
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
        $server = $this->executionData->server();

        $this->assertCount(0, $server->meta());
        $this->assertEquals($this->app->make('request')->server(), $server->data()->toArray());
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

        $this->tapLaravelVersionBetween(5.4, 6, function () {
            $fileA = UploadedFile::fake()->image('file-val-a.png');
            $fileB = UploadedFile::fake()->image('file-val-b.png');
            $typeA = get_class($fileA);
            $typeB = get_class($fileB);

            $this->post('/', [
                'file-key-a' => $fileA,
                'file-key-b' => $fileB,
            ]);

            $request = $this->executionData->request();

            $this->assertEquals([
                'client original name' => $fileA->getClientOriginalName(),
                'client original extension' => $fileA->getClientOriginalExtension(),
                'client mime type' => $fileA->getClientMimeType(),
                'client size' => $fileA->getClientSize(),
                'path' => $fileA->path(),
            ], $request->data()->get('files')['file-key-a'][$typeA]);
            $this->assertEquals([
                'client original name' => $fileB->getClientOriginalName(),
                'client original extension' => $fileB->getClientOriginalExtension(),
                'client mime type' => $fileB->getClientMimeType(),
                'client size' => $fileB->getClientSize(),
                'path' => $fileB->path(),
            ], $request->data()->get('files')['file-key-b'][$typeB]);
        });

        $this->tapLaravelVersionFrom(7, function () {
            $fileA = UploadedFile::fake()->image('file-val-a.png');
            $fileB = UploadedFile::fake()->image('file-val-b.png');
            $typeA = get_class($fileA);
            $typeB = get_class($fileB);

            $this->post('/', [
                'file-key-a' => $fileA,
                'file-key-b' => $fileB,
            ]);

            $request = $this->executionData->request();

            $this->assertEquals([
                'client original name' => $fileA->getClientOriginalName(),
                'client original extension' => $fileA->getClientOriginalExtension(),
                'client mime type' => $fileA->getClientMimeType(),
                'client size' => $fileA->getSize(),
                'path' => $fileA->path(),
            ], $request->data()->get('files')['file-key-a'][$typeA]);
            $this->assertEquals([
                'client original name' => $fileB->getClientOriginalName(),
                'client original extension' => $fileB->getClientOriginalExtension(),
                'client mime type' => $fileB->getClientMimeType(),
                'client size' => $fileB->getSize(),
                'path' => $fileB->path(),
            ], $request->data()->get('files')['file-key-b'][$typeB]);
        });
    }

    /** @test */
    function has_request_all_files_if_they_are_in_array()
    {
        $this->tapLaravelVersionTill(5.3, function () {
            $this->assertTrue(true);
        });

        $this->tapLaravelVersionFrom(5.4, function () {
            $fileA = UploadedFile::fake()->image('file-val-a.png');
            $fileB = UploadedFile::fake()->image('file-val-b.png');
            $fileX = UploadedFile::fake()->image('file-val-x.png');
            $fileY = UploadedFile::fake()->image('file-val-y.png');
            $typeA = get_class($fileA);
            $typeB = get_class($fileB);
            $typeX = get_class($fileX);
            $typeY = get_class($fileY);

            $this->post('/', [
                'file-key-1' => [
                    'a' => $fileA,
                    'b' => $fileB,
                ],
                'file-key-2' => [
                    'subkey' => [
                        'x' => $fileX,
                        'y' => $fileY,
                    ],
                ],
            ]);

            $request = $this->executionData->request();

            $this->assertContains($fileA->path(), $request->data()->get('files')['file-key-1']['a'][$typeA]);
            $this->assertContains($fileB->path(), $request->data()->get('files')['file-key-1']['b'][$typeB]);
            $this->assertContains($fileX->path(), $request->data()->get('files')['file-key-2']['subkey']['x'][$typeX]);
            $this->assertContains($fileY->path(), $request->data()->get('files')['file-key-2']['subkey']['y'][$typeY]);
        });
    }

    /** @test */
    function has_request_cookie()
    {
        $this->tapLaravelVersionTill('5.4', function () {
            $this->call('GET', '/', [], ['cookie-key-a' => Crypt::encrypt('cookie-val-a')]);
            $request = $this->executionData->request();

            $this->assertStringContainsString('cookie-val-a', $request->data()->get('cookie')['cookie-key-a']);
        });

        $this->tapLaravelVersionBetween('5.5', '5.5', function () {
            $this->call('GET', '/', [], ['cookie-key-a' => [Crypt::encrypt('cookie-val-a')]]);
            $request = $this->executionData->request();

            $this->assertStringContainsString('cookie-val-a', Crypt::decrypt($request->data()->get('cookie')['cookie-key-a'][0]));
        });

        $this->tapLaravelVersionBetween('5.6', '5.8', function () {
            $this->call('GET', '/', [], ['cookie-key-a' => Crypt::encrypt('cookie-val-a')]);
            $request = $this->executionData->request();

            $this->assertStringContainsString('cookie-val-a', $request->data()->get('cookie')['cookie-key-a']);
        });

        $this->tapLaravelVersionFrom('6', function () {
            $this->call('GET', '/', [], ['cookie-key-a' => [Crypt::encrypt('cookie-val-a')]]);
            $request = $this->executionData->request();

            $this->assertStringContainsString('cookie-val-a', Crypt::decrypt($request->data()->get('cookie')['cookie-key-a'][0]));
        });
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
    function has_route_closure_action()
    {
        $uses = function (DummyClassA $a, DummyFormRequest $request) {
            return $request->get('id');
        };
        $action = new ReflectionFunction($uses);

        Route::get('route-a/{id}', $uses);

        $this->get('/route-a/123');
        $route = $this->executionData->route();

        $this->assertStringContainsString(
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

        $this->assertStringContainsString(
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
        $this->assertEquals('Not Found', $response->meta()->get('status_text'));
    }

    /** @test */
    function can_have_response_unknown_status()
    {
        Route::get('unknown', function () {
            abort(419);
        });

        $this->get('/unknown');
        $response = $this->executionData->response();

        $this->assertEquals(419, $response->meta()->get('status'));
        $this->assertEquals('unknown status', $response->meta()->get('status_text'));
    }

    /** @test */
    function has_response_headers()
    {
        $this->get('/');
        $response = $this->executionData->response();

        $this->assertArrayHasKey('content-type', $response->data()->get('headers'));
    }

    /** @test */
    function has_response_content()
    {
        $this->get('/');
        $content = $this->executionData->content();

        $this->assertCount(0, $content->meta());
        $this->assertStringNotContainsString('HTTP/1.1 200 OK', $content->data()->get('content'));
        $this->assertStringContainsString('</body>', $content->data()->get('content'));
        $this->assertStringContainsString('</html>', $content->data()->get('content'));
    }
}
