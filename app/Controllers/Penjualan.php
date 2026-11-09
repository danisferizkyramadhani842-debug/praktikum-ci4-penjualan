<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\PelangganModel;
use App\Models\ProdukModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Penjualan - Form + simpan transaksi, daftar, detail
 */
class Penjualan extends BaseController
{
    private PenjualanModel $model;
    private PelangganModel $pelangganModel;
    private ProdukModel    $produkModel;

    public function __construct()
    {
        $this->model          = new PenjualanModel();
        $this->pelangganModel = new PelangganModel();
        $this->produkModel    = new ProdukModel();
    }

    // GET /penjualan
    public function index(): string
    {
        return view('penjualan/index', [
            'title'     => 'Daftar Penjualan',
            'penjualan' => $this->model->daftarPenjualan(),
        ]);
    }

    // GET /penjualan/tambah
    public function tambah(): string
    {
        return view('penjualan/form', [
            'title'       => 'Tambah Penjualan',
            'noPenjualan' => $this->model->generateNoPenjualan(),
            'pelanggan'   => $this->pelangganModel->semuaPelanggan(),
            'produk'      => $this->produkModel->semuaProduk(),
        ]);
    }

    // POST /penjualan/simpan
    public function simpan()
    {
        $post = $this->request->getPost();

        // ── Ambil & bersihkan data header ────────────────────────────────────
        $noPenjualan   = trim($post['no_penjualan'] ?? '');
        $tglPenjualan  = trim($post['tgl_penjualan'] ?? '');
        $kodePelanggan = trim($post['kode_pelanggan'] ?? '');
        $keterangan    = trim($post['keterangan'] ?? '');

        // ── Validasi header sederhana ─────────────────────────────────────────
        $errors = [];

        if ($noPenjualan === '') {
            $errors[] = 'No. Penjualan wajib diisi.';
        }
        if ($tglPenjualan === '') {
            $errors[] = 'Tanggal wajib diisi.';
        }
        if ($kodePelanggan === '') {
            $errors[] = 'Pelanggan wajib dipilih.';
        }

        // ── Ambil & validasi baris detail ────────────────────────────────────
        $kodeProdukArr = $post['kode_produk'] ?? [];
        $jumlahArr     = $post['jumlah'] ?? [];

        $detailValid  = [];
        $kodeSudahAda = [];

        foreach ($kodeProdukArr as $i => $kode) {
            $kode   = trim($kode);
            $jumlah = trim($jumlahArr[$i] ?? '');

            // Abaikan baris kosong
            if ($kode === '' && $jumlah === '') {
                continue;
            }

            if ($kode === '') {
                $errors[] = "Baris " . ($i + 1) . ": kode produk wajib diisi.";
                continue;
            }

            if (! is_numeric($jumlah) || (int)$jumlah <= 0) {
                $errors[] = "Baris " . ($i + 1) . " ({$kode}): jumlah harus berupa integer > 0.";
                continue;
            }

            // Cek duplikat kode produk
            if (in_array($kode, $kodeSudahAda, true)) {
                $errors[] = "Produk {$kode} muncul lebih dari satu kali. Gunakan satu baris per produk.";
                continue;
            }

            $kodeSudahAda[]  = $kode;
            $detailValid[]   = ['kode_produk' => $kode, 'jumlah' => (int)$jumlah];
        }

        if (count($detailValid) < 2) {
            $errors[] = 'Minimal 2 baris detail produk yang valid harus diisi.';
        }

        // Jika ada error validasi controller, kembali ke form
        if (! empty($errors)) {
            session()->setFlashdata('error', implode('<br>', $errors));
            return redirect()->to('/penjualan/tambah')->withInput();
        }

        // ── Jalankan transaksi ────────────────────────────────────────────────
        $header = [
            'no_penjualan'   => $noPenjualan,
            'tgl_penjualan'  => $tglPenjualan,
            'kode_pelanggan' => $kodePelanggan,
            'keterangan'     => $keterangan,
        ];

        $hasil = $this->model->simpanTransaksi($header, $detailValid, true);

        if ($hasil['sukses']) {
            session()->setFlashdata('sukses', $hasil['pesan']);
            return redirect()->to('/penjualan/detail/' . $hasil['no_penjualan']);
        }

        session()->setFlashdata('error', 'Transaksi gagal: ' . $hasil['pesan']);
        return redirect()->to('/penjualan/tambah')->withInput();
    }

    // GET /penjualan/detail/:no
    public function detail(string $no): string
    {
        $header = $this->model->ambilHeader($no);
        if (! $header) {
            throw new PageNotFoundException("Penjualan {$no} tidak ditemukan.");
        }

        return view('penjualan/detail', [
            'title'  => "Detail Penjualan {$no}",
            'header' => $header,
            'detil'  => $this->model->ambilDetil($no),
        ]);
    }
}
