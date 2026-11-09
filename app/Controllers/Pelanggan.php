<?php

namespace App\Controllers;

use App\Models\PelangganModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Pelanggan - CRUD pelanggan
 */
class Pelanggan extends BaseController
{
    private PelangganModel $model;

    public function __construct()
    {
        $this->model = new PelangganModel();
    }

    // GET /pelanggan
    public function index(): string
    {
        $data = $this->model->orderBy('kode_pelanggan')->findAll();

        return view('pelanggan/index', [
            'title'     => 'Daftar Pelanggan',
            'pelanggan' => $data,
        ]);
    }

    // GET /pelanggan/tambah
    public function tambah(): string
    {
        return view('pelanggan/form', [
            'title'     => 'Tambah Pelanggan',
            'mode'      => 'tambah',
            'pelanggan' => [],
        ]);
    }

    // POST /pelanggan/simpan
    public function simpan()
    {
        if (! $this->validate($this->model->aturanTambah())) {
            return view('pelanggan/form', [
                'title'      => 'Tambah Pelanggan',
                'mode'       => 'tambah',
                'pelanggan'  => [],
                'validation' => $this->validator,
            ]);
        }

        $data = [
            'kode_pelanggan' => strtoupper(trim($this->request->getPost('kode_pelanggan'))),
            'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
            'no_telepon'     => $this->request->getPost('no_telepon') ?? '',
            'alamat'         => $this->request->getPost('alamat') ?? '',
        ];

        if ($this->model->insert($data) !== false) {
            session()->setFlashdata('sukses', "Pelanggan {$data['kode_pelanggan']} berhasil ditambahkan.");
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan pelanggan.');
        }

        return redirect()->to('/pelanggan');
    }

    // GET /pelanggan/ubah/:kode
    public function ubah(string $kode): string
    {
        $pelanggan = $this->model->find($kode);
        if (! $pelanggan) {
            throw new PageNotFoundException("Pelanggan {$kode} tidak ditemukan.");
        }

        return view('pelanggan/form', [
            'title'     => 'Ubah Pelanggan',
            'mode'      => 'ubah',
            'pelanggan' => $pelanggan,
        ]);
    }

    // POST /pelanggan/update/:kode
    public function update(string $kode)
    {
        $pelanggan = $this->model->find($kode);
        if (! $pelanggan) {
            throw new PageNotFoundException("Pelanggan {$kode} tidak ditemukan.");
        }

        if (! $this->validate($this->model->aturanUbah())) {
            return view('pelanggan/form', [
                'title'      => 'Ubah Pelanggan',
                'mode'       => 'ubah',
                'pelanggan'  => $pelanggan,
                'validation' => $this->validator,
            ]);
        }

        // Jangan sertakan kode_pelanggan di array data
        $data = [
            'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
            'no_telepon'     => $this->request->getPost('no_telepon') ?? '',
            'alamat'         => $this->request->getPost('alamat') ?? '',
        ];

        if ($this->model->update($kode, $data) !== false) {
            session()->setFlashdata('sukses', "Pelanggan {$kode} berhasil diperbarui.");
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui pelanggan.');
        }

        return redirect()->to('/pelanggan');
    }

    // POST /pelanggan/hapus/:kode
    public function hapus(string $kode)
    {
        $pelanggan = $this->model->find($kode);
        if (! $pelanggan) {
            session()->setFlashdata('error', "Pelanggan {$kode} tidak ditemukan.");
            return redirect()->to('/pelanggan');
        }

        // Cek apakah pelanggan sudah punya penjualan
        $db    = \Config\Database::connect();
        $pakai = $db->table('penjualan')
                    ->where('kode_pelanggan', $kode)
                    ->countAllResults();

        if ($pakai > 0) {
            session()->setFlashdata('error',
                "Pelanggan {$kode} tidak bisa dihapus karena memiliki {$pakai} data penjualan.");
            return redirect()->to('/pelanggan');
        }

        try {
            $this->model->delete($kode);
            session()->setFlashdata('sukses', "Pelanggan {$kode} berhasil dihapus.");
        } catch (DatabaseException $e) {
            session()->setFlashdata('error',
                "Gagal menghapus pelanggan {$kode}: pelanggan sudah memiliki data transaksi.");
            log_message('error', '[HAPUS-PELANGGAN] ' . $e->getMessage());
        }

        return redirect()->to('/pelanggan');
    }
}
