<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PenjualanModel extends Model
{
    protected $table            = 'penjualan';
    protected $primaryKey       = 'no_penjualan';
    protected $useAutoIncrement = false;   // PK varchar
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'no_penjualan', 'tgl_penjualan', 'kode_pelanggan', 'total', 'keterangan',
    ];

    /**
     * Konstruktor menerima koneksi DB opsional.
     * Dipakai UjiTransaksi untuk menyuntik koneksi group "tests".
     */
    public function __construct(?BaseConnection $db = null)
    {
        parent::__construct();
        if ($db !== null) {
            $this->db = $db;
        }
    }

    /**
     * Generate nomor penjualan otomatis: J<tahun><4 digit urut>
     * Contoh: J20261003 (setelah seed yang sudah ada J20261001 & J20261002)
     * Raw SQL dipakai karena perlu MAX + SUBSTR + CAST yang sulit di QB murni.
     */
    public function generateNoPenjualan(): string
    {
        $tahun  = date('Y');
        $prefix = "J{$tahun}";

        // Ambil MAX no_penjualan yang prefix-nya sama dengan tahun ini
        $row = $this->db->query(
            "SELECT MAX(no_penjualan) AS max_no FROM penjualan WHERE no_penjualan LIKE ?",
            ["{$prefix}%"]
        )->getRow();

        if ($row && $row->max_no) {
            // Ambil 4 digit terakhir, tambah 1
            $urut = (int) substr($row->max_no, -4) + 1;
        } else {
            $urut = 1;
        }

        return $prefix . str_pad((string)$urut, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan transaksi penjualan dalam satu unit transaksi DB.
     *
     * @param  array $header          Data header penjualan
     * @param  array $detail          Array baris detail [kode_produk, jumlah]
     * @param  bool  $validasiProduk  true = cek stok di aplikasi;
     *                                false = lewati cek, biarkan FK DB yang menolak
     * @return array ['sukses' => bool, 'pesan' => string, 'no_penjualan' => string]
     */
    public function simpanTransaksi(array $header, array $detail, bool $validasiProduk = true): array
    {
        $db          = $this->db;
        $noPenjualan = $header['no_penjualan'];

        log_message('info', "[TRX-PENJUALAN] MULAI no={$noPenjualan} db={$db->getDatabase()}");

        $db->transBegin();
        try {
            // ── LANGKAH 1: Insert header dengan total = 0 ────────────────────
            $sisipHeader = $db->table('penjualan')->insert([
                'no_penjualan'   => $noPenjualan,
                'tgl_penjualan'  => $header['tgl_penjualan'],
                'kode_pelanggan' => $header['kode_pelanggan'],
                'total'          => 0,
                'keterangan'     => $header['keterangan'] ?? '',
            ]);

            if (! $sisipHeader) {
                throw new \RuntimeException("Gagal menyimpan header penjualan {$noPenjualan}.");
            }

            // ── LANGKAH 2: Loop detail ────────────────────────────────────────
            $noUrut = 1;
            foreach ($detail as $baris) {
                $kodeProduk = $baris['kode_produk'];
                $jumlah     = (int) $baris['jumlah'];

                if ($validasiProduk) {
                    // 2a. Ambil produk dengan FOR UPDATE (lock baris saat transaksi)
                    // Raw SQL dipakai karena Query Builder CI4 tidak mendukung FOR UPDATE secara langsung
                    $produk = $db->query(
                        'SELECT kode_produk, harga_jual, stok FROM produk WHERE kode_produk = ? FOR UPDATE',
                        [$kodeProduk]
                    )->getRow();

                    // 2b. Produk tidak ada → rollback
                    if (! $produk) {
                        throw new \RuntimeException("Produk {$kodeProduk} tidak ditemukan.");
                    }

                    // 2c. Stok tidak cukup → rollback
                    if ($produk->stok < $jumlah) {
                        throw new \RuntimeException(
                            "Stok {$kodeProduk} tidak cukup (tersedia: {$produk->stok}, diminta: {$jumlah})."
                        );
                    }

                    $harga    = (float) $produk->harga_jual;
                    $subtotal = $harga * $jumlah;

                } else {
                    // validasiProduk = false: ambil harga dari DB tanpa FOR UPDATE,
                    // kode invalid akan ditolak FK PostgreSQL (SQLSTATE 23503)
                    $produk = $db->query(
                        'SELECT kode_produk, harga_jual FROM produk WHERE kode_produk = ?',
                        [$kodeProduk]
                    )->getRow();

                    // Jika ada di DB, pakai harganya. Jika tidak ada, FK akan menolak INSERT.
                    $harga    = $produk ? (float) $produk->harga_jual : 0;
                    $subtotal = $harga * $jumlah;
                }

                // 2d. Insert baris detail via Query Builder
                $db->table('detil_penjualan')->insert([
                    'no_penjualan' => $noPenjualan,
                    'no_urut'      => $noUrut,
                    'kode_produk'  => $kodeProduk,
                    'jumlah'       => $jumlah,
                    'harga'        => $harga,
                    'subtotal'     => $subtotal,
                ]);

                // 2e. Kurangi stok hanya jika validasi aplikasi aktif
                if ($validasiProduk) {
                    // AND stok >= jumlah sebagai guard tambahan
                    $db->query(
                        'UPDATE produk SET stok = stok - ? WHERE kode_produk = ? AND stok >= ?',
                        [$jumlah, $kodeProduk, $jumlah]
                    );
                }

                $noUrut++;
            }

            // ── LANGKAH 3: Update total header dari SUM detil ────────────────
            $db->query(
                'UPDATE penjualan SET total = (
                    SELECT COALESCE(SUM(subtotal), 0) FROM detil_penjualan WHERE no_penjualan = ?
                ) WHERE no_penjualan = ?',
                [$noPenjualan, $noPenjualan]
            );

            // ── LANGKAH 4: Commit ────────────────────────────────────────────
            $db->transCommit();
            log_message('info', "[TRX-PENJUALAN] COMMIT no={$noPenjualan}");

            return ['sukses' => true, 'pesan' => "Penjualan {$noPenjualan} berhasil disimpan.", 'no_penjualan' => $noPenjualan];

        } catch (\Throwable $e) {
            // Setelah error di PostgreSQL, satu-satunya langkah valid adalah rollback
            $db->transRollback();
            $alasan = $e->getMessage();
            log_message('error', "[TRX-PENJUALAN] ROLLBACK no={$noPenjualan} alasan={$alasan}");

            return ['sukses' => false, 'pesan' => $alasan, 'no_penjualan' => $noPenjualan];
        }
    }

    /**
     * Daftar penjualan JOIN pelanggan untuk halaman index
     */
    public function daftarPenjualan(): array
    {
        return $this->db->table('penjualan p')
            ->select('p.no_penjualan, p.tgl_penjualan, p.total, p.keterangan, pl.nama_pelanggan')
            ->join('pelanggan pl', 'pl.kode_pelanggan = p.kode_pelanggan')
            ->orderBy('p.no_penjualan', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil 1 header penjualan JOIN pelanggan
     */
    public function ambilHeader(string $noPenjualan): ?array
    {
        $row = $this->db->table('penjualan p')
            ->select('p.*, pl.nama_pelanggan')
            ->join('pelanggan pl', 'pl.kode_pelanggan = p.kode_pelanggan')
            ->where('p.no_penjualan', $noPenjualan)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Ambil semua baris detil JOIN produk untuk 1 penjualan
     */
    public function ambilDetil(string $noPenjualan): array
    {
        return $this->db->table('detil_penjualan d')
            ->select('d.*, pr.nama_produk, pr.satuan')
            ->join('produk pr', 'pr.kode_produk = d.kode_produk')
            ->where('d.no_penjualan', $noPenjualan)
            ->orderBy('d.no_urut', 'ASC')
            ->get()
            ->getResultArray();
    }
}
