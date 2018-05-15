<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\LaravelExecution\HttpRequest;
use JKocik\Laravel\Profiler\LaravelExecution\HttpResponse;

class LaravelHttpExecutionTest extends TestCase
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * @return bool
     */
    protected function isNotAbleToTestUploadedFile(): bool
    {
        if (! in_array('fake', get_class_methods(UploadedFile::class))) {
            $this->assertTrue(true);
            return true;
        }

        return false;
    }

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
        if ($this->isNotAbleToTestUploadedFile()) {
            return;
        }

        $this->post('/', [
            'file-key-a' => UploadedFile::fake()->image('file-val-a'),
        ]);
        $request = $this->executionData->request();

        $this->assertInstanceOf(UploadedFile::class, $request->data()->get('files')['file-key-a']);
    }

    /** @test */
    function has_request_cookie()
    {
        $this->call('GET', '/', [], ['cookie-key-a' => Crypt::encrypt('cookie-val-a')]);
        $request = $this->executionData->request();

        $this->assertEquals(['cookie-key-a' => 'cookie-val-a'], $request->data()->get('cookie'));
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
