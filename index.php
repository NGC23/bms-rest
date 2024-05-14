<?php

declare(strict_types=1);


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

require_once 'config/container/container.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: *');

header('Access-Control-Allow-Headers: *');

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$responseFactory = new Laminas\Diactoros\ResponseFactory();

$strategy = (new League\Route\Strategy\JsonStrategy($responseFactory))->setContainer($container);
$router   = (new League\Route\Router)->setStrategy($strategy);

$router->map(
    'GET',
    '/bookings',
    'App\Bms\Application\Booking\BookingController::get'
);

try {
    $response = $router->dispatch($request);

    // send the response to the browser
    (new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
} catch (Exception $e) {
    var_dump($e->getMessage());
    die("router exception");
}
