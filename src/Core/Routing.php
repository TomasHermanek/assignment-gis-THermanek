<?php

namespace Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * Class Routing handles routing in app
 * @package Core
 */
class Routing {
    private $routeCollection;

    /**
     * Function load routes from configuration
     */
    private function loadRoutes() {
        $routesJson = file_get_contents('config/routing.json');
        $routesArray = json_decode($routesJson, true);

        foreach ($routesArray as $routeElement) {
            $route = new Route($routeElement['route'], array(
                'controller' => $routeElement['controller'],
                'action' => $routeElement['action']
            ));
            $this->routeCollection->add($routeElement['name'], $route);
        }
    }

    public function __construct() {
        $this->routeCollection = new RouteCollection();
        $this->loadRoutes();
    }

    /**
     * This function matches request traversing private routeCollection. Matcher ignores $_GET parameters while
     * matching correct route.
     *
     * @param Request $request
     * @return array
     */
    public function matchRoute(Request $request){
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routeCollection, $context);
        $parameters = $matcher->match($context->getPathInfo());
        return $parameters;
    }
}