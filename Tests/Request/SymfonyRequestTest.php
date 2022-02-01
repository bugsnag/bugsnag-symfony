<?php

namespace Bugsnag\BugsnagBundle\Tests\Request;

use Bugsnag\BugsnagBundle\Request\SymfonyRequest;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Request\NullRequest;
use Bugsnag\Request\RequestInterface;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SymfonyRequestTest extends TestCase
{
    use MockeryTrait;

    public function testCanResolveNullRequest()
    {
        $resolver = new SymfonyResolver();

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(NullRequest::class, $request);
    }

    public function testCanResolveSymfonyRequest()
    {
        $resolver = new SymfonyResolver();

        $resolver->set(new Request());

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(SymfonyRequest::class, $request);
    }

    public function testResolveSessionWhenPreviousSessionDoesNotExists()
    {
        /** @var MockObject&Request $symfonyRequest */
        $symfonyRequest = $this->getMockBuilder(Request::class)
            ->setMethods(['hasPreviousSession', 'getSession'])
            ->getMock();

        $resolver = new SymfonyResolver();
        $resolver->set($symfonyRequest);

        $request = $resolver->resolve();

        $symfonyRequest->expects($this->once())
            ->method('hasPreviousSession')
            ->willReturn(false);

        $symfonyRequest->expects($this->never())
            ->method('getSession');

        $session = $request->getSession();

        $this->assertSame([], $session);
    }

    public function testResolveSessionWhenPreviousSessionExists()
    {
        /** @var MockObject&Request $symfonyRequest */
        $symfonyRequest = $this->getMockBuilder(Request::class)
            ->setMethods(['hasPreviousSession', 'getSession'])
            ->getMock();

        /** @var MockObject&Session $symfonySession */
        $symfonySession = $this->getMockBuilder(Session::class)
            ->setMethods(['all'])
            ->getMock();

        $resolver = new SymfonyResolver();
        $resolver->set($symfonyRequest);

        $request = $resolver->resolve();

        $symfonyRequest->expects($this->once())
            ->method('hasPreviousSession')
            ->willReturn(true);

        $symfonyRequest->expects($this->once())
            ->method('getSession')
            ->willReturn($symfonySession);

        $symfonySession->expects($this->once())
            ->method('all')
            ->willReturn(['foobar' => 'baz']);

        $session = $request->getSession();

        $this->assertSame(['foobar' => 'baz'], $session);
    }

    public function testItIsARequest()
    {
        $request = new SymfonyRequest(new Request());

        $this->assertTrue($request->isRequest());
    }

    public function testItReturnsCookieData()
    {
        $cookies = ['a_cookie' => 'a value for the first cookie', 'another_cookie' => 'another value'];

        $symfonyRequest = new Request([], [], [], $cookies);

        $request = new SymfonyRequest($symfonyRequest);

        $this->assertSame($cookies, $request->getCookies());
    }

    public function testItReturnsCookieDataWhenThereAreNoCookies()
    {
        $symfonyRequest = new Request();

        $request = new SymfonyRequest($symfonyRequest);

        $this->assertSame([], $request->getCookies());
    }

    public function testItReturnsMetadataWhenThereIsNoRequestData()
    {
        $symfonyRequest = new Request();

        $request = new SymfonyRequest($symfonyRequest);

        $expected = [
            'request' => [
                'url' => 'http://:/',
                'httpMethod' => 'GET',
                'params' => [],
                'clientIp' => null,
            ],
        ];

        $this->assertSame($expected, $request->getMetaData());
    }

    public function testItReturnsMetadataWhenThereIsSomeRequestData()
    {
        $symfonyRequest = new Request(
            ['x' => 'y'],
            [],
            [],
            [],
            [],
            [
                'HTTPS' => 'on',
                'PHP_SELF' => '/index.php',
                'QUERY_STRING' => 'x=y',
                'REMOTE_ADDR' => '1.2.3.4',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/abc',
                'SCRIPT_NAME' => '/index.php',
                'SERVER_NAME' => 'www.example.com',
                'SERVER_PORT' => '1234',
            ]
        );

        $request = new SymfonyRequest($symfonyRequest);

        $expected = [
            'request' => [
                'url' => 'https://www.example.com:1234/abc?x=y',
                'httpMethod' => 'GET',
                'params' => ['x' => 'y'],
                'clientIp' => '1.2.3.4',
            ],
        ];

        $this->assertSame($expected, $request->getMetaData());
    }

    public function testItIncludesHeadersJsonAndUserAgentInMetadataWhenPresent()
    {
        $symfonyRequest = new Request(
            ['x' => 'y'],
            [],
            [],
            [],
            [],
            [
                'HTTPS' => 'on',
                'HTTP_ACCEPT' => 'text/html',
                'HTTP_ACCEPT_LANGUAGE' => 'en',
                'HTTP_ACCEPT_ENCODING' => 'gzip',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_HOST' => 'www.example.com:1234',
                'HTTP_USER_AGENT' => 'bugsnag',
                'PHP_SELF' => '/index.php',
                'QUERY_STRING' => 'x=y',
                'REMOTE_ADDR' => '1.2.3.4',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/abc',
                'SCRIPT_NAME' => '/index.php',
                'SERVER_NAME' => 'www.example.com',
                'SERVER_PORT' => '1234',
            ],
            '{ "a": "b" }'
        );

        $request = new SymfonyRequest($symfonyRequest);

        $expected = [
            'request' => [
                'url' => 'https://www.example.com:1234/abc?x=y',
                'httpMethod' => 'GET',
                'params' => ['a' => 'b', 'x' => 'y'],
                'clientIp' => '1.2.3.4',
                'userAgent' => 'bugsnag',
                'headers' => [
                    'accept' => ['text/html'],
                    'accept-language' => ['en'],
                    'accept-encoding' => ['gzip'],
                    'content-type' => ['application/json'],
                    'host' => ['www.example.com:1234'],
                    'user-agent' => ['bugsnag'],
                ],
            ],
        ];

        $this->assertSame($expected, $request->getMetaData());
    }

    /**
     * @dataProvider jsonContentTypeProvider
     */
    public function testItDecodesJson($contentType)
    {
        $symfonyRequest = new Request(
            [],
            [],
            [],
            [],
            [],
            ['HTTP_CONTENT_TYPE' => $contentType],
            '{ "a": "b", "c": "d", "x": { "y": 123 } }'
        );

        $request = new SymfonyRequest($symfonyRequest);

        $expected = ['a' => 'b', 'c' => 'd', 'x' => ['y' => 123]];
        $metadata = $request->getMetaData();

        $this->assertSame($expected, $metadata['request']['params']);
        $this->assertSame($contentType, $metadata['request']['headers']['content-type'][0]);
    }

    public function jsonContentTypeProvider()
    {
        return [
            'content type: "application/json"' => ['application/json'],
            'content type: "/JsOn"' => ['/JsOn'],
            'content type: "TEXT/JSON"' => ['TEXT/JSON'],
            'content type: "application/stuff+json"' => ['application/stuff+json'],
            'content type: "+jSoN"' => ['+jSoN'],
        ];
    }

    /**
     * @dataProvider nonJsonContentTypeProvider
     */
    public function testItDoesNotDecodeNonJsonContentTypes($contentType)
    {
        $symfonyRequest = new Request(
            [],
            [],
            [],
            [],
            [],
            ['HTTP_CONTENT_TYPE' => $contentType],
            '{ "a": "b", "c": "d", "x": { "y": 123 } }'
        );

        $request = new SymfonyRequest($symfonyRequest);

        $metadata = $request->getMetaData();

        $this->assertEmpty($metadata['request']['params']);
        $this->assertSame($contentType, $metadata['request']['headers']['content-type'][0]);
    }

    public function nonJsonContentTypeProvider()
    {
        return [
            'content type: "application/xml"' => ['application/xml'],
            'content type: "/xml"' => ['/xml'],
            'content type: "text/xml"' => ['text/xml'],
            'content type: "application/stuff+yaml"' => ['application/stuff+yaml'],
            'content type: "+yaml"' => ['+yaml'],
        ];
    }

    public function testItDoesNotDecodeTheBodyWhenThereIsNoContentType()
    {
        $symfonyRequest = new Request();

        $request = new SymfonyRequest($symfonyRequest);

        $metadata = $request->getMetaData();

        $this->assertEmpty($metadata['request']['params']);
    }

    public function testItReturnsTheRequestContext()
    {
        $symfonyRequest = new Request([], [], [], [], [], [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/some/route',
        ]);

        $request = new SymfonyRequest($symfonyRequest);

        $this->assertSame('GET /some/route', $request->getContext());
    }

    public function testItReturnsTheRequestContextForAPostRequest()
    {
        $symfonyRequest = new Request([], [], [], [], [], [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/some/other/route',
        ]);

        $request = new SymfonyRequest($symfonyRequest);

        $this->assertSame('POST /some/other/route', $request->getContext());
    }

    public function testItReturnsTheRequestContextWhenServerInformationIsMissing()
    {
        $symfonyRequest = new Request();

        $request = new SymfonyRequest($symfonyRequest);

        $this->assertSame('GET /', $request->getContext());
    }

    public function testItReturnsTheUserId()
    {
        $symfonyRequest = new Request([], [], [], [], [], [
            'REMOTE_ADDR' => '1.2.3.4',
        ]);

        $request = new SymfonyRequest($symfonyRequest);

        $this->assertSame('1.2.3.4', $request->getUserId());
    }

    public function testItReturnsTheUserIdWhenServerInformationIsMissing()
    {
        $symfonyRequest = new Request();

        $request = new SymfonyRequest($symfonyRequest);

        $this->assertNull($request->getUserId());
    }
}
