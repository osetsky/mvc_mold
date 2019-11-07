<?php declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

//Dependency Injection Container
$dice = new \Dice\Dice;

//http shared objects begin
$requestParams = [
    'Http\HttpRequest' => [
        //Mark the class as shared so the same instance is returned each time
        'shared' => true,
        //The constructor arguments that will be supplied when the instance is created
        'constructParams' => [$_GET, $_POST, $_COOKIE, $_FILES, $_SERVER]
    ]
];

$responseParams = [
    'Http\HttpResponse' => [
        //Mark the class as shared so the same instance is returned each time
        'shared' => true
    ]
];

$dice = $dice->addRules($requestParams);
$dice = $dice->addRules($responseParams);
$request = $dice->create('Http\HttpRequest');
$response = $dice->create('Http\HttpResponse');
//Avoid replace same type header
foreach ($response->getHeaders() as $header) {
    header($header, false);
}
//http shared objects end

//template engine begin
$bladeParams = [
    '\eftec\bladeone\BladeOne' => [
        'shared' => true,
        'constructParams' => [__DIR__ . "/Views", __DIR__ . "Views/Compile"]
    ]
];
$dice = $dice->addRules($bladeParams);
$blade = $dice->create('\eftec\bladeone\BladeOne');
//template engine end

$routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
    $routes = include('Routes.php');
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};

$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());

switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page not found');
        $response->setStatusCode(404);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $controllerName = $routeInfo[1][0];
        $action = $routeInfo[1][1];
        $actionParams = $routeInfo[2];
        // $controller = new $controllerName($response);
        $controller = $dice->create($controllerName);
        $controller->$action($actionParams);
        break;
}

$blade->setMode(\eftec\bladeone\BladeOne::MODE_DEBUG);
echo $blade->run("templates." . $action, []);
//echo $response->getContent();

