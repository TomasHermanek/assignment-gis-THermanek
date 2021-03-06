<?php
    require_once __DIR__ . '/vendor/autoload.php';

    use Database\PostgressDb;
    use Repository\BarRepository;
    use Core\Routing;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    $parametersJson = file_get_contents('config/parameters.json');
    $parameters = json_decode($parametersJson, true);

    $database = new PostgressDb($parameters['host'], $parameters['port'], $parameters['dbName'],
                                $parameters['userName'], $parameters['userPassword']);
    $database->connect();


    $wtfRepository = new BarRepository();
    $request = Request::createFromGlobals();

    $router = new Routing();
    try {
        $routeParameters = $router->matchRoute($request);
        $objectString = 'Controller\\'.$routeParameters['controller'];
        $object = new $objectString($database, $wtfRepository);
        $responseData = $object->{$routeParameters['action']}($request);
    }
    catch (Symfony\Component\Routing\Exception\ResourceNotFoundException $exception) {
        $response = new Response();
        $response->prepare($request);
        $response->setContent('Not Found');
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->send();
    }

    $database->disconnect();