<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'kode_produk';
    protected $useAutoIncrement = false;   // PK varchar, bukan auto-increment
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    // Semua kolom termasuk PK masuk allowedFields
    protected $allowedFields = [
        'kode_produk', 'nama_produk', 'satuan',
        'harga_jual', 'harga_beli', 'stok', 'deskripsi',
    ];

    // Aturan validasi TAMBAH (kode_produk wajib & unik)
    protected $validationRules = [];

    // Validasi tambah dipanggil manual dari controller
    public function aturanTambah(): array
    {
        return [
            'kode_produk' => [
                'label' => 'Kode Produk',
                'rules' => 'required|max_length[10]|is_unique[produk.kode_produk]',
            ],
            'nama_produk' => [
                'label' => 'Nama Produk',
                'rules' => 'required|max_length[100]',
            ],
            'satuan' => [
                'label' => 'Satuan',
                'rules' => 'required|max_length[20]',
            ],
            'harga_jual' => [
                'label' => 'Harga Jual',
                'rules' => 'required|decimal|greater_than_equal_to[0]',
            ],
            'harga_beli' => [
                'label' => 'Harga Beli',
                'rules' => 'required|decimal|greater_than_equal_to[0]',
            ],
            'stok' => [
                'label' => 'Stok',
                'rules' => 'required|integer|greater_than_equal_to[0]',
            ],
        ];
    }

    // Validasi ubah — tanpa is_unique pada kode_produk (kode tidak bisa diubah)
    public function aturanUbah(): array
    {
        return [
            'nama_produk' => [
                'label' => 'Nama Produk',
                'rules' => 'required|max_length[100]',
            ],
            'satuan' => [
                'label' => 'Satuan',
                'rules' => 'required|max_length[20]',
            ],
            'harga_jual' => [
                'label' => 'Harga Jual',
                'rules' => 'required|decimal|greater_than_equal_to[0]',
            ],
            'harga_beli' => [
                'label' => 'Harga Beli',
                'rules' => 'required|decimal|greater_than_equal_to[0]',
            ],
            'stok' => [
                'label' => 'Stok',
                'rules' => 'required|integer|greater_than_equal_to[0]',
            ],
        ];
    }

    /**
     * Ambil semua produk dengan pencarian ILIKE (case-insensitive di PostgreSQL)
     * dan pagination.
     */
    public function cariPaginate(string $keyword = '', int $perPage = 10): array
    {
        $builder = $this->builder();

        if ($keyword !== '') {
            // like() dengan true di param ke-4 → ILIKE di driver Postgre
            $builder->like('nama_produk', $keyword, 'both', null, true);
        }

        $builder->orderBy('kode_produk', 'ASC');

        // Hitung total untuk pager
        $total = (clone $builder)->countAllResults(false);

        $page  = (int) ($_GET['page'] ?? 1);
        $data  = $builder->get($perPage, ($page - 1) * $perPage)->getResultArray();

        return ['data' => $data, 'total' => $total];
    }

    /**
     * Ambil semua produk (untuk datalist di form penjualan)
     */
    public function semuaProduk(): array
    {
        return $this->orderBy('kode_produk')->findAll();
    }
}
