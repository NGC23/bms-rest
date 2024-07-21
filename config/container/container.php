<?php 

declare(strict_types=1);

print_r($_SERVER);
print_r($_ENV);

error_reporting(E_ALL);

use Dotenv\Dotenv;
use League\Container\Container;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$container = new Container();

$container->add(\App\Application\Booking\BookingController::class)
    ->addArgument(App\Domain\Booking\Interfaces\IBookingRepository::class)
    ->addArgument(App\Domain\Booking\Interfaces\IBookingDetailsRepository::class)
    ->addArgument(App\Domain\Events\Interfaces\IEventRepository::class);

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

//fix plural and singular in naming, bloody idiot!!!
$container->add(
    App\Domain\Booking\Interfaces\IBookingRepository::class,
    App\Infrastructure\Bookings\Repository\BookingRepository::class
)->addArgument(App\Domain\General\Interfaces\IConnectionFactory::class);

$container->add(
    App\Domain\Booking\Interfaces\IBookingDetailsRepository::class,
    App\Infrastructure\Bookings\Repository\BookingDetailsRepository::class
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
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_DATABASE'],
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_ROOT_PASSWORD']
        )
    );


