<?php
declare(strict_types=1);

namespace Chimera\Routing\Expressive;

use Chimera\Routing\Application as ApplicationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Application as Expressive;

final class Application implements ApplicationInterface
{
    private Expressive $application;

    public function __construct(Expressive $application)
    {
        $this->application = $application;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->application->handle($request);
    }

    public function run(): void
    {
        $this->application->run();
    }
}
