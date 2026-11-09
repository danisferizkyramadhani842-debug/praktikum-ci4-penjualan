<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table            = 'pelanggan';
    protected $primaryKey       = 'kode_pelanggan';
    protected $useAutoIncrement = false;   // PK varchar
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'kode_pelanggan', 'nama_pelanggan', 'no_telepon', 'alamat',
    ];

    public function aturanTambah(): array
    {
        return [
            'kode_pelanggan' => [
                'label' => 'Kode Pelanggan',
                'rules' => 'required|max_length[10]|is_unique[pelanggan.kode_pelanggan]',
            ],
            'nama_pelanggan' => [
                'label' => 'Nama Pelanggan',
                'rules' => 'required|max_length[100]',
            ],
            'no_telepon' => [
                'label' => 'No. Telepon',
                'rules' => 'permit_empty|max_length[25]',
            ],
        ];
    }

    public function aturanUbah(): array
    {
        return [
            'nama_pelanggan' => [
                'label' => 'Nama Pelanggan',
                'rules' => 'required|max_length[100]',
            ],
            'no_telepon' => [
                'label' => 'No. Telepon',
                'rules' => 'permit_empty|max_length[25]',
            ],
        ];
    }

    // Semua pelanggan untuk dropdown form penjualan
    public function semuaPelanggan(): array
    {
        return $this->orderBy('kode_pelanggan')->findAll();
    }
}
