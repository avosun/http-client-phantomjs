<?php
/**
 * @license see LICENSE
 */
namespace Serps\HttpClient;

use Psr\Http\Message\ResponseInterface;
use Serps\HttpClient\PhantomJsClient;
use Zend\Diactoros\Request;

use Zend\Diactoros\Response;

/**
 * @covers Serps\HttpClient\CurlClient
 * @covers Serps\HttpClient\CurlClient\Curl
 * @covers Serps\HttpClient\CurlClient\ResponseBuilder
 */
class PhantomJsClientTest extends \PHPUnit_Framework_TestCase
{

    public function testGetRequest()
    {
        $client = new PhantomJsClient(__DIR__ . "/../../vendor/bin/phantomjs");

        $request = new Request('http://httpbin.org/get', 'GET');
        $request = $request->withHeader('User-Agent', 'test-user-agent');
        $request = $request->withHeader('Accept', 'application/json');

        $response = $client->sendRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        var_dump($response->getBody()->__toString());

        $responseData = json_decode($response->getBody()->__toString(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test-user-agent', $responseData['headers']['User-Agent']);
        $this->assertCount(1, $response->getHeader('X-SERPS-EFFECTIVE-URL'));
        $this->assertEquals('http://httpbin.org/get', $response->getHeader('X-SERPS-EFFECTIVE-URL')[0]);
    }

    public function testRedirectRequest()
    {
        $client = new PhantomJsClient(__DIR__ . "/../../vendor/bin/phantomjs");

        $request = new Request('http://httpbin.org/redirect-to?url=get', 'GET');
        $request = $request->withHeader('User-Agent', 'test-user-agent');

        $response = $client->sendRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $responseData = json_decode($response->getBody()->__toString(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test-user-agent', $responseData['headers']['User-Agent']);
        $this->assertCount(1, $response->getHeader('X-SERPS-EFFECTIVE-URL'));
        $this->assertEquals('http://httpbin.org/get', $response->getHeader('X-SERPS-EFFECTIVE-URL')[0]);
    }
}
