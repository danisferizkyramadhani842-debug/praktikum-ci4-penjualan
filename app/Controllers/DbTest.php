<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * DbTest - Uji koneksi group default & tests
 * GET /db-test
 */
class DbTest extends BaseController
{
    public function index(): string
    {
        $hasil = [];

        // Uji group DEFAULT
        try {
            $db  = \Config\Database::connect('default');
            $ver = $db->query('SELECT version() AS ver')->getRow();
            $hasil['default'] = [
                'status'   => 'OK',
                'database' => $db->getDatabase(),
                'driver'   => $db->DBDriver,
                'versi'    => $ver->ver ?? '-',
            ];
        } catch (DatabaseException $e) {
            $hasil['default'] = ['status' => 'ERROR', 'pesan' => $e->getMessage()];
        }

        // Uji group TESTS
        try {
            $db  = \Config\Database::connect('tests');
            $ver = $db->query('SELECT version() AS ver')->getRow();
            $hasil['tests'] = [
                'status'   => 'OK',
                'database' => $db->getDatabase(),
                'driver'   => $db->DBDriver,
                'versi'    => $ver->ver ?? '-',
            ];
        } catch (DatabaseException $e) {
            $hasil['tests'] = ['status' => 'ERROR', 'pesan' => $e->getMessage()];
        }

        return view('db_test/index', [
            'title' => 'Uji Koneksi Database',
            'hasil' => $hasil,
        ]);
    }
}
