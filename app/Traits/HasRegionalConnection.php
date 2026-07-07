<?php

namespace App\Traits;

trait HasRegionalConnection
{
    public function getConnectionName()
    {
        if ($this->connection) {
            return $this->connection;
        }

        $idRegional = session('current_regional', 1);

        return match ($idRegional) {
            2 => 'pgsql_r2',
            3 => 'pgsql_r3',
            'pusat' => 'pgsql_pusat',
            default => 'pgsql',
        };
    }
}
