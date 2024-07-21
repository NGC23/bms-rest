<?php

declare(strict_types=1);

namespace App\Domain\General\Models;

class Connection
{
    public function __construct(
        private string $host,
        private string $port,
        private string $database,
        private string $username,
        private string $password
    ) {
    }

    /**
     * Get the value of dsn
     */
    public function getDsn(): string
    {
        return "mysql:host=$this->host;port=$this->port;dbname=$this->database";
    }

    /**
     * Get the value of username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
