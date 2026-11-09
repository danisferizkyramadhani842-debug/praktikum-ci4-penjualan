<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $defaultGroup = 'default';

    // Group DEFAULT → db_penjualan (data utama)
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'postgres',
        'password'     => 'postgres',
        'database'     => 'db_penjualan',
        'DBDriver'     => 'Postgre',   // HARUS persis "Postgre"
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => '',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 5432,
        'numberNative' => false,
        'schema'       => 'public',
    ];

    // Group TESTS → db_penjualan_test (uji transaksi)
    // Menggantikan SQLite3 :memory: bawaan CI4 sepenuhnya
    public array $tests = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'postgres',
        'password'     => 'postgres',
        'database'     => 'db_penjualan_test',
        'DBDriver'     => 'Postgre',   // HARUS persis "Postgre"
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => '',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 5432,
        'numberNative' => false,
        'schema'       => 'public',
    ];
}
