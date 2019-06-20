<?php
declare(strict_types=1);

namespace Chimera\Routing\Expressive\Tests;

use Chimera\Routing\Expressive\Application;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Expressive\Application as Expressive;
use Zend\Expressive\MiddlewareContainer;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Router\RouteCollector;
use Zend\Expressive\Router\RouterInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\MiddlewarePipeInterface;

/**
 * @coversDefaultClass \Chimera\Routing\Expressive\Application
 */
final class ApplicationTest extends TestCase
{
    /**
     * @var Expressive
     */
    private $expressive;

    /**
     * @var EmitterInterface&MockObject
     */
    private $emitter;

    /**
     * @var MiddlewarePipeInterface&MockObject
     */
    private $pipeline;

    /**
     * @before
     */
    public function createDependencies(): void
    {
        $this->pipeline = $this->createMock(MiddlewarePipeInterface::class);
        $this->emitter  = $this->createMock(EmitterInterface::class);

        $this->expressive = new Expressive(
            new MiddlewareFactory(new MiddlewareContainer($this->createMock(ContainerInterface::class))),
            $this->pipeline,
            new RouteCollector($this->createMock(RouterInterface::class)),
            new RequestHandlerRunner(
                $this->createMock(RequestHandlerInterface::class),
                $this->emitter,
                [ServerRequestFactory::class, 'fromGlobals'],
                [new ResponseFactory(), 'createResponse']
            )
        );
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::handle
     */
    public function handleShouldForwardPassRequestThroughThePipeline(): void
    {
        $request  = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->pipeline->expects(self::once())
                       ->method('handle')
                       ->with($request)
                       ->willReturn($response);

        $application = new Application($this->expressive);

        self::assertSame($response, $application->handle($request));
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::run
     */
    public function runShouldInvokeApplicationRunner(): void
    {
        $this->emitter->expects(self::once())
            ->method('emit')
            ->with(self::isInstanceOf(ResponseInterface::class));

        $application = new Application($this->expressive);
        $application->run();
    }
}
