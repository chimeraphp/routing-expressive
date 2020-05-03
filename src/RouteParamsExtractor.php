<?php
declare(strict_types=1);

namespace Chimera\Routing\Expressive;

use Chimera\Routing\RouteParamsExtractor as RouteParamsExtractorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

final class RouteParamsExtractor implements RouteParamsExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getParams(ServerRequestInterface $request): array
    {
        $routeResult = $request->getAttribute(RouteResult::class);

        if (! $routeResult instanceof RouteResult) {
            return [];
        }

        return $routeResult->getMatchedParams();
    }
}
