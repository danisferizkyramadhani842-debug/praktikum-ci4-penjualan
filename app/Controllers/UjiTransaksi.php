<?php

namespace App\Controllers;

use App\Models\PenjualanModel;

/**
 * UjiTransaksi - Demonstrasi transaksi DB CI4 pada database pengujian (group "tests").
 * Controller ini hanya aktif jika ENVIRONMENT !== 'production' (dijaga di Routes.php).
 *
 * Memakai db_penjualan_test → data db_penjualan (utama) TIDAK tersentuh sama sekali.
 */
class UjiTransaksi extends BaseController
{
    private \CodeIgniter\Database\BaseConnection $dbTest;
    private PenjualanModel $model;

    public function __construct()
    {
        // Hubungkan ke group "tests" (db_penjualan_test)
        $this->dbTest = \Config\Database::connect('tests');
        // Suntikkan koneksi tests ke PenjualanModel
        $this->model  = new PenjualanModel($this->dbTest);
    }

    // GET /uji-transaksi
    public function index(): string
    {
        return view('uji_transaksi/index', [
            'title'     => 'Uji Coba Transaksi Database',
            'namaDb'    => $this->dbTest->getDatabase(),
            'stokAwal'  => $this->ambilStok(),
            'daftarTrx' => $this->ambilDaftarTrx(),
            'hasil'     => null,
        ]);
    }

    // POST /uji-transaksi/jalankan
    public function jalankan()
    {
        $skenario = (int) $this->request->getPost('skenario');

        // Snapshot sebelum transaksi
        $sebelumStok = $this->ambilStok();
        $sebelumTrx  = $this->ambilDaftarTrx();

        $hasil = $this->jalankanSkenario($skenario);

        // Snapshot sesudah transaksi
        $sesudahStok = $this->ambilStok();
        $sesudahTrx  = $this->ambilDaftarTrx();

        return view('uji_transaksi/index', [
            'title'       => 'Uji Coba Transaksi Database',
            'namaDb'      => $this->dbTest->getDatabase(),
            'stokAwal'    => $sesudahStok,   // tampilkan kondisi terkini
            'daftarTrx'   => $sesudahTrx,
            'hasil'       => [
                'skenario'    => $skenario,
                'sukses'      => $hasil['sukses'],
                'pesan'       => $hasil['pesan'],
                'sebelumStok' => $sebelumStok,
                'sesudahStok' => $sesudahStok,
                'sebelumTrx'  => $sebelumTrx,
                'sesudahTrx'  => $sesudahTrx,
            ],
        ]);
    }

    // ── Jalankan skenario yang dipilih ───────────────────────────────────────

    private function jalankanSkenario(int $skenario): array
    {
        return match ($skenario) {
            1 => $this->skenario1(),
            2 => $this->skenario2(),
            3 => $this->skenario3(),
            4 => $this->skenario4(),
            default => ['sukses' => false, 'pesan' => 'Skenario tidak dikenal.'],
        };
    }

    /**
     * Skenario 1 - BERHASIL
     * J20261003, C001, P001×2 + P003×3
     * Harapan: total 134000, stok P001 -2, P003 -3
     */
    private function skenario1(): array
    {
        $header = [
            'no_penjualan'   => 'J20261003',
            'tgl_penjualan'  => date('Y-m-d'),
            'kode_pelanggan' => 'C001',
            'keterangan'     => 'Skenario 1 - uji berhasil',
        ];
        $detail = [
            ['kode_produk' => 'P001', 'jumlah' => 2],
            ['kode_produk' => 'P003', 'jumlah' => 3],
        ];

        return $this->model->simpanTransaksi($header, $detail, true);
    }

    /**
     * Skenario 2 - GAGAL: kode produk tidak ditemukan (cek aplikasi)
     * J20261004, C002, P002×1 kemudian P999×1 (invalid)
     * Harapan: RuntimeException "Produk P999 tidak ditemukan", ROLLBACK total
     */
    private function skenario2(): array
    {
        $header = [
            'no_penjualan'   => 'J20261004',
            'tgl_penjualan'  => date('Y-m-d'),
            'kode_pelanggan' => 'C002',
            'keterangan'     => 'Skenario 2 - uji gagal cek aplikasi',
        ];
        $detail = [
            ['kode_produk' => 'P002', 'jumlah' => 1],
            ['kode_produk' => 'P999', 'jumlah' => 1],   // kode tidak ada
        ];

        return $this->model->simpanTransaksi($header, $detail, true);
    }

    /**
     * Skenario 3 - GAGAL: FK violation (tanpa cek aplikasi)
     * J20261005, validasiProduk = false, P001×1 (valid) + P999 (invalid → FK error)
     * Harapan: DatabaseException SQLSTATE 23503, ROLLBACK total
     */
    private function skenario3(): array
    {
        $header = [
            'no_penjualan'   => 'J20261005',
            'tgl_penjualan'  => date('Y-m-d'),
            'kode_pelanggan' => 'C001',
            'keterangan'     => 'Skenario 3 - uji FK violation',
        ];
        $detail = [
            ['kode_produk' => 'P001', 'jumlah' => 1],
            ['kode_produk' => 'P999', 'jumlah' => 1],   // akan ditolak FK
        ];

        // validasiProduk = false → lewati cek aplikasi, biarkan DB yang menolak
        return $this->model->simpanTransaksi($header, $detail, false);
    }

    /**
     * Skenario 4 (Bonus) - GAGAL: stok tidak cukup
     * J20261006, P004×999 (stok hanya 25)
     * Harapan: RuntimeException "Stok P004 tidak cukup", ROLLBACK
     */
    private function skenario4(): array
    {
        $header = [
            'no_penjualan'   => 'J20261006',
            'tgl_penjualan'  => date('Y-m-d'),
            'kode_pelanggan' => 'C003',
            'keterangan'     => 'Skenario 4 - uji stok tidak cukup',
        ];
        $detail = [
            ['kode_produk' => 'P001', 'jumlah' => 1],
            ['kode_produk' => 'P004', 'jumlah' => 999],  // stok hanya 25
        ];

        return $this->model->simpanTransaksi($header, $detail, true);
    }

    // ── Helper: ambil snapshot stok produk dari db_penjualan_test ────────────

    private function ambilStok(): array
    {
        return $this->dbTest->table('produk')
            ->select('kode_produk, nama_produk, stok')
            ->orderBy('kode_produk')
            ->get()
            ->getResultArray();
    }

    private function ambilDaftarTrx(): array
    {
        return $this->dbTest->table('penjualan p')
            ->select('p.no_penjualan, p.tgl_penjualan, p.total, pl.nama_pelanggan')
            ->join('pelanggan pl', 'pl.kode_pelanggan = p.kode_pelanggan')
            ->orderBy('p.no_penjualan')
            ->get()
            ->getResultArray();
    }
}
