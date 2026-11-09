<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Produk - CRUD produk dengan pencarian + pagination
 */
class Produk extends BaseController
{
    private ProdukModel $model;

    public function __construct()
    {
        $this->model = new ProdukModel();
    }

    // GET /produk
    public function index(): string
    {
        $keyword = trim($this->request->getGet('q') ?? '');
        $perPage = 10;

        $result = $this->model->cariPaginate($keyword, $perPage);

        // Buat pager sederhana manual
        $page      = (int) ($this->request->getGet('page') ?? 1);
        $total     = $result['total'];
        $totalPage = (int) ceil($total / $perPage);

        return view('produk/index', [
            'title'     => 'Daftar Produk',
            'produk'    => $result['data'],
            'keyword'   => $keyword,
            'page'      => $page,
            'totalPage' => $totalPage,
            'total'     => $total,
            'perPage'   => $perPage,
        ]);
    }

    // GET /produk/tambah
    public function tambah(): string
    {
        return view('produk/form', [
            'title'  => 'Tambah Produk',
            'mode'   => 'tambah',
            'produk' => [],
        ]);
    }

    // POST /produk/simpan
    public function simpan()
    {
        $aturan = $this->model->aturanTambah();

        if (! $this->validate($aturan)) {
            return view('produk/form', [
                'title'      => 'Tambah Produk',
                'mode'       => 'tambah',
                'produk'     => [],
                'validation' => $this->validator,
            ]);
        }

        $data = [
            'kode_produk' => strtoupper(trim($this->request->getPost('kode_produk'))),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'satuan'      => $this->request->getPost('satuan'),
            'harga_jual'  => $this->request->getPost('harga_jual'),
            'harga_beli'  => $this->request->getPost('harga_beli'),
            'stok'        => $this->request->getPost('stok'),
            'deskripsi'   => $this->request->getPost('deskripsi') ?? '',
        ];

        // insert() mengembalikan true/false, bukan ID (PK varchar)
        if ($this->model->insert($data) !== false) {
            session()->setFlashdata('sukses', "Produk {$data['kode_produk']} berhasil ditambahkan.");
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan produk.');
        }

        return redirect()->to('/produk');
    }

    // GET /produk/ubah/:kode
    public function ubah(string $kode): string
    {
        $produk = $this->model->find($kode);
        if (! $produk) {
            throw new PageNotFoundException("Produk {$kode} tidak ditemukan.");
        }

        return view('produk/form', [
            'title'  => 'Ubah Produk',
            'mode'   => 'ubah',
            'produk' => $produk,
        ]);
    }

    // POST /produk/update/:kode
    public function update(string $kode)
    {
        $produk = $this->model->find($kode);
        if (! $produk) {
            throw new PageNotFoundException("Produk {$kode} tidak ditemukan.");
        }

        $aturan = $this->model->aturanUbah();

        if (! $this->validate($aturan)) {
            return view('produk/form', [
                'title'      => 'Ubah Produk',
                'mode'       => 'ubah',
                'produk'     => $produk,
                'validation' => $this->validator,
            ]);
        }

        // Jangan sertakan kode_produk di array data saat update
        $data = [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'satuan'      => $this->request->getPost('satuan'),
            'harga_jual'  => $this->request->getPost('harga_jual'),
            'harga_beli'  => $this->request->getPost('harga_beli'),
            'stok'        => $this->request->getPost('stok'),
            'deskripsi'   => $this->request->getPost('deskripsi') ?? '',
        ];

        if ($this->model->update($kode, $data) !== false) {
            session()->setFlashdata('sukses', "Produk {$kode} berhasil diperbarui.");
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui produk.');
        }

        return redirect()->to('/produk');
    }

    // POST /produk/hapus/:kode
    public function hapus(string $kode)
    {
        $produk = $this->model->find($kode);
        if (! $produk) {
            session()->setFlashdata('error', "Produk {$kode} tidak ditemukan.");
            return redirect()->to('/produk');
        }

        // Cek apakah produk sudah dipakai di detil_penjualan
        $db    = \Config\Database::connect();
        $pakai = $db->table('detil_penjualan')
                    ->where('kode_produk', $kode)
                    ->countAllResults();

        if ($pakai > 0) {
            session()->setFlashdata('error',
                "Produk {$kode} tidak bisa dihapus karena sudah digunakan dalam {$pakai} transaksi.");
            return redirect()->to('/produk');
        }

        try {
            $this->model->delete($kode);
            session()->setFlashdata('sukses', "Produk {$kode} berhasil dihapus.");
        } catch (DatabaseException $e) {
            // Tangkap juga FK violation yang mungkin lolos pengecekan di atas
            session()->setFlashdata('error',
                "Gagal menghapus produk {$kode}: produk sudah digunakan dalam transaksi.");
            log_message('error', '[HAPUS-PRODUK] ' . $e->getMessage());
        }

        return redirect()->to('/produk');
    }
}
