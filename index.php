<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

require_once 'config/container/container.php';

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: *');

header('Access-Control-Allow-Headers: *');

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$responseFactory = new Laminas\Diactoros\ResponseFactory();

$strategy = new League\Route\Strategy\JsonStrategy($responseFactory);
$strategy->setContainer($container);

$router   = (new League\Route\Router())->setStrategy($strategy);

//USER
$router->map(
    'POST',
    '/user/register',
    'App\Application\User\UserRegistrationController::create'
);

$router->map(
    'POST',
    '/user/login',
    'App\Application\User\UserLoginController::login'
);
//BOOKINGS
$router->map(
    'POST',
    '/bookings/create',
    'App\Application\Booking\BookingController::create'
);

$router->map(
    'GET',
    '/bookings/{userId}',
    'App\Application\Booking\BookingController::getAll'
);

$router->map(
    'GET',
    '/bookings/booker/{bookerId}',
    'App\Application\Booking\BookingController::getAllbyBookerId'
);

$router->map(
    'GET',
    '/bookings/details/{bookingId}',
    'App\Application\Booking\BookingController::getById'
);
//EVENTS
$router->map(
    'POST',
    '/events/create',
    'App\Application\Events\EventController::create'
);

$router->map(
    'PUT',
    '/events/update',
    'App\Application\Events\EventController::update'
);

$router->map(
    'DELETE',
    '/events/delete/{userId}/{id}',
    'App\Application\Events\EventController::delete'
);

$router->map(
    'GET',
    '/events/{userId}',
    'App\Application\Events\EventController::getAll'
);

$router->map(
    'GET',
    '/events/details/{userId}/{id}',
    'App\Application\Events\EventController::getbyId'
);

try {
    $response = $router->dispatch($request);
    // send the response to the browser
    (new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
} catch (Exception $e) {
    var_dump($e->getMessage());
    die("router exception");
}
