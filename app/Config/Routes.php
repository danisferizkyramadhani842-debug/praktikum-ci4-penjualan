<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Routes.php - Semua route aplikasi
 *
 * Route → Controller::Method
 * GET  /                              → redirect /produk
 * GET  /db-test                       → DbTest::index
 * GET  /produk                        → Produk::index
 * GET  /produk/tambah                 → Produk::tambah
 * POST /produk/simpan                 → Produk::simpan
 * GET  /produk/ubah/(:segment)        → Produk::ubah/$1
 * POST /produk/update/(:segment)      → Produk::update/$1
 * POST /produk/hapus/(:segment)       → Produk::hapus/$1
 * GET  /pelanggan                     → Pelanggan::index
 * GET  /pelanggan/tambah              → Pelanggan::tambah
 * POST /pelanggan/simpan              → Pelanggan::simpan
 * GET  /pelanggan/ubah/(:segment)     → Pelanggan::ubah/$1
 * POST /pelanggan/update/(:segment)   → Pelanggan::update/$1
 * POST /pelanggan/hapus/(:segment)    → Pelanggan::hapus/$1
 * GET  /penjualan                     → Penjualan::index
 * GET  /penjualan/tambah              → Penjualan::tambah
 * POST /penjualan/simpan              → Penjualan::simpan
 * GET  /penjualan/detail/(:segment)   → Penjualan::detail/$1
 * GET  /uji-transaksi                 → UjiTransaksi::index
 * POST /uji-transaksi/jalankan        → UjiTransaksi::jalankan
 */

/** @var RouteCollection $routes */

$routes->get('/', static function () {
    return redirect()->to('/produk');
});

$routes->get('db-test', 'DbTest::index');

// CRUD Produk
$routes->get('produk',                    'Produk::index');
$routes->get('produk/tambah',             'Produk::tambah');
$routes->post('produk/simpan',            'Produk::simpan');
$routes->get('produk/ubah/(:segment)',    'Produk::ubah/$1');
$routes->post('produk/update/(:segment)', 'Produk::update/$1');
$routes->post('produk/hapus/(:segment)',  'Produk::hapus/$1');

// CRUD Pelanggan
$routes->get('pelanggan',                    'Pelanggan::index');
$routes->get('pelanggan/tambah',             'Pelanggan::tambah');
$routes->post('pelanggan/simpan',            'Pelanggan::simpan');
$routes->get('pelanggan/ubah/(:segment)',    'Pelanggan::ubah/$1');
$routes->post('pelanggan/update/(:segment)', 'Pelanggan::update/$1');
$routes->post('pelanggan/hapus/(:segment)',  'Pelanggan::hapus/$1');

// Penjualan
$routes->get('penjualan',                   'Penjualan::index');
$routes->get('penjualan/tambah',            'Penjualan::tambah');
$routes->post('penjualan/simpan',           'Penjualan::simpan');
$routes->get('penjualan/detail/(:segment)', 'Penjualan::detail/$1');

// Uji Transaksi (hanya non-production)
if (ENVIRONMENT !== 'production') {
    $routes->get('uji-transaksi',           'UjiTransaksi::index');
    $routes->post('uji-transaksi/jalankan', 'UjiTransaksi::jalankan');
}
