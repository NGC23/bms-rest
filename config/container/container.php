<?php 

declare(strict_types=1);

use Dotenv\Dotenv;
use League\Container\Container;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$container = new Container();

$container->add(\App\Application\Booking\BookingController::class);

$container->add(App\Application\Events\EventController::class)
    ->addArgument(App\Domain\Events\Interfaces\IEventRepository::class);

$container->add(App\Application\User\UserRegistrationController::class)
    ->addArgument(App\Domain\User\Interfaces\IUserRepository::class);

$container->add(App\Application\User\UserLoginController::class)
    ->addArgument(App\Domain\User\Interfaces\IAuthenticationRepository::class);

$container->add(
    App\Domain\User\Interfaces\IAuthenticationRepository::class,
    App\Infrastructure\User\Repository\AuthenticationRepository::class
)->addArgument(App\Domain\General\Interfaces\IConnectionFactory::class);

$container->add(
    App\Domain\User\Interfaces\IUserRepository::class,
    App\Infrastructure\User\Repository\UserRepository::class
)->addArgument(App\Domain\General\Interfaces\IConnectionFactory::class);

$container->add(
    App\Domain\Events\Interfaces\IEventRepository::class,
    App\Infrastructure\Events\Repository\EventRepository::class
)->addArgument(App\Domain\General\Interfaces\IConnectionFactory::class);

$container->add(
    App\Domain\General\Interfaces\IConnectionFactory::class,
    App\Domain\General\Factory\PDOConnectionFactory::class
)->addArgument(
        new App\Domain\General\Models\Connection(
            "mysql:host=jeeves-mysql;port=3306;dbname=jeeves",
            "root",
            "gymmer4Life2024#"
        )
    );


