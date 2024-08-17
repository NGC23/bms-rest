<?php

declare(strict_types=1);

namespace Test\Domain\Genral\Models;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\General\Models\Connection;

#[CoversClass(Connection::class)]
class ConnectionModelTest extends TestCase
{
    public function testConnectionModel(): void
    {
        $username = "username";
        $host = "test-dsn";
        $port = "3306";
        $database = "test";
        $password = "password";

        $connection = new Connection(
            $host, 
            $port, 
            $database, 
            $username, 
            $password
        );

        $this->assertEquals("mysql:host=$host;port=$port;dbname=$database", $connection->getDsn());
        $this->assertEquals($password, $connection->getPassword());
        $this->assertEquals($username, $connection->getUsername());
    }
}