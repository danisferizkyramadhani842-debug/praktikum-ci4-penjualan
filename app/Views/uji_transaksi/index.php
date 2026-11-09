<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-1">🧪 Uji Coba Transaksi Database</h2>
<p class="text-muted mb-4">
    Database pengujian: <strong class="text-primary"><?= esc($namaDb) ?></strong>
    &mdash; data di <code>db_penjualan</code> (utama) <strong>tidak tersentuh</strong>.
</p>

<!-- ══ HASIL SKENARIO (muncul setelah POST) ═══════════════════════════════ -->
<?php if ($hasil !== null): ?>
    <?php
    $labelSkenario = [
        1 => 'Skenario 1 — BERHASIL (J20261003, P001×2 + P003×3)',
        2 => 'Skenario 2 — GAGAL: Produk tidak ditemukan (cek aplikasi)',
        3 => 'Skenario 3 — GAGAL: FK Violation (tanpa cek aplikasi)',
        4 => 'Skenario 4 — GAGAL: Stok tidak cukup (P004×999)',
    ];
    $labelAktif = $labelSkenario[$hasil['skenario']] ?? "Skenario {$hasil['skenario']}";
    ?>
    <div class="card mb-4 border-<?= $hasil['sukses'] ? 'success' : 'danger' ?> shadow-sm">
        <div class="card-header bg-<?= $hasil['sukses'] ? 'success' : 'danger' ?> text-white fw-bold">
            <?= $hasil['sukses'] ? '✅ COMMIT' : '❌ ROLLBACK' ?> — <?= esc($labelAktif) ?>
        </div>
        <div class="card-body">
            <p class="mb-3">
                <strong>Pesan:</strong>
                <span class="<?= $hasil['sukses'] ? 'text-success' : 'text-danger' ?>">
                    <?= esc($hasil['pesan']) ?>
                </span>
            </p>

            <div class="row g-3">
                <!-- Stok sebelum vs sesudah -->
                <div class="col-md-6">
                    <h6 class="fw-bold">Stok Produk — Sebelum</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Kode</th><th>Nama</th><th class="text-end">Stok</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hasil['sebelumStok'] as $s): ?>
                                <tr>
                                    <td><code><?= esc($s['kode_produk']) ?></code></td>
                                    <td><?= esc($s['nama_produk']) ?></td>
                                    <td class="text-end"><?= esc($s['stok']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Stok Produk — Sesudah</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Kode</th><th>Nama</th><th class="text-end">Stok</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            // Buat map sebelum untuk perbandingan
                            $mapSebelum = array_column($hasil['sebelumStok'], 'stok', 'kode_produk');
                            foreach ($hasil['sesudahStok'] as $s):
                                $stokLama = $mapSebelum[$s['kode_produk']] ?? $s['stok'];
                                $berubah  = (int)$s['stok'] !== (int)$stokLama;
                            ?>
                                <tr class="<?= $berubah ? 'table-warning' : '' ?>">
                                    <td><code><?= esc($s['kode_produk']) ?></code></td>
                                    <td><?= esc($s['nama_produk']) ?></td>
                                    <td class="text-end fw-bold">
                                        <?= esc($s['stok']) ?>
                                        <?php if ($berubah): ?>
                                            <span class="text-danger small">
                                                (<?= (int)$s['stok'] - (int)$stokLama ?>)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Daftar transaksi sebelum vs sesudah -->
                <div class="col-md-6">
                    <h6 class="fw-bold">Daftar Penjualan — Sebelum</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>No</th><th>Pelanggan</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($hasil['sebelumTrx'])): ?>
                                <tr><td colspan="3" class="text-muted text-center">Kosong</td></tr>
                            <?php else: ?>
                                <?php foreach ($hasil['sebelumTrx'] as $t): ?>
                                    <tr>
                                        <td><code><?= esc($t['no_penjualan']) ?></code></td>
                                        <td><?= esc($t['nama_pelanggan']) ?></td>
                                        <td class="text-end">Rp <?= number_format((float)$t['total'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Daftar Penjualan — Sesudah</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>No</th><th>Pelanggan</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($hasil['sesudahTrx'])): ?>
                                <tr><td colspan="3" class="text-muted text-center">Kosong</td></tr>
                            <?php else: ?>
                                <?php
                                $noSebelum = array_column($hasil['sebelumTrx'], 'no_penjualan');
                                foreach ($hasil['sesudahTrx'] as $t):
                                    $baru = !in_array($t['no_penjualan'], $noSebelum, true);
                                ?>
                                    <tr class="<?= $baru ? 'table-success' : '' ?>">
                                        <td><code><?= esc($t['no_penjualan']) ?></code></td>
                                        <td><?= esc($t['nama_pelanggan']) ?></td>
                                        <td class="text-end">Rp <?= number_format((float)$t['total'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div><!-- /row -->
        </div><!-- /card-body -->
    </div>
<?php endif; ?>

<!-- ══ KONDISI TERKINI ════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold bg-light">📦 Stok Produk Saat Ini</div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>Kode</th><th>Nama</th><th class="text-end">Stok</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stokAwal as $s): ?>
                            <tr>
                                <td><code><?= esc($s['kode_produk']) ?></code></td>
                                <td><?= esc($s['nama_produk']) ?></td>
                                <td class="text-end"><?= esc($s['stok']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold bg-light">🧾 Daftar Penjualan Saat Ini</div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>No</th><th>Tanggal</th><th>Pelanggan</th><th class="text-end">Total</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarTrx)): ?>
                            <tr><td colspan="4" class="text-muted text-center py-2">Belum ada transaksi.</td></tr>
                        <?php else: ?>
                            <?php foreach ($daftarTrx as $t): ?>
                                <tr>
                                    <td><code><?= esc($t['no_penjualan']) ?></code></td>
                                    <td><?= esc(date('d/m/Y', strtotime($t['tgl_penjualan']))) ?></td>
                                    <td><?= esc($t['nama_pelanggan']) ?></td>
                                    <td class="text-end">Rp <?= number_format((float)$t['total'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ══ TOMBOL SKENARIO ════════════════════════════════════════════════════ -->
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold bg-dark text-white">🎮 Jalankan Skenario Uji</div>
    <div class="card-body">
        <div class="row g-3">

            <!-- Skenario 1 -->
            <div class="col-md-6">
                <div class="card border-success h-100">
                    <div class="card-header bg-success text-white fw-bold">
                        ✅ Skenario 1 — BERHASIL
                    </div>
                    <div class="card-body">
                        <ul class="mb-3 small">
                            <li>No: <code>J20261003</code>, Pelanggan: C001</li>
                            <li>Detail: P001 × 2 dan P003 × 3</li>
                            <li>Harapan: total <strong>134.000</strong>, stok P001 −2, P003 −3</li>
                            <li>COMMIT berhasil</li>
                        </ul>
                        <form method="post" action="<?= base_url('uji-transaksi/jalankan') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="skenario" value="1">
                            <button class="btn btn-success w-100">▶ Jalankan Skenario 1</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Skenario 2 -->
            <div class="col-md-6">
                <div class="card border-danger h-100">
                    <div class="card-header bg-danger text-white fw-bold">
                        ❌ Skenario 2 — GAGAL (cek aplikasi)
                    </div>
                    <div class="card-body">
                        <ul class="mb-3 small">
                            <li>No: <code>J20261004</code>, Pelanggan: C002</li>
                            <li>Detail: P002 × 1 (valid) → <strong>P999 × 1</strong> (tidak ada)</li>
                            <li>Harapan: pesan "Produk P999 tidak ditemukan"</li>
                            <li>ROLLBACK — J20261004 tidak tersimpan, stok P002 tetap</li>
                        </ul>
                        <form method="post" action="<?= base_url('uji-transaksi/jalankan') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="skenario" value="2">
                            <button class="btn btn-danger w-100">▶ Jalankan Skenario 2</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Skenario 3 -->
            <div class="col-md-6">
                <div class="card border-warning h-100">
                    <div class="card-header bg-warning text-dark fw-bold">
                        ⚠️ Skenario 3 — GAGAL (FK Violation)
                    </div>
                    <div class="card-body">
                        <ul class="mb-3 small">
                            <li>No: <code>J20261005</code>, <code>validasiProduk = false</code></li>
                            <li>Detail: P001 × 1 (valid) → <strong>P999 × 1</strong> (FK error)</li>
                            <li>Harapan: DatabaseException SQLSTATE 23503</li>
                            <li>ROLLBACK — J20261005 tidak tersimpan, stok P001 tidak berubah</li>
                        </ul>
                        <form method="post" action="<?= base_url('uji-transaksi/jalankan') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="skenario" value="3">
                            <button class="btn btn-warning w-100">▶ Jalankan Skenario 3</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Skenario 4 -->
            <div class="col-md-6">
                <div class="card border-secondary h-100">
                    <div class="card-header bg-secondary text-white fw-bold">
                        🔴 Skenario 4 — GAGAL (Stok Tidak Cukup)
                    </div>
                    <div class="card-body">
                        <ul class="mb-3 small">
                            <li>No: <code>J20261006</code>, Pelanggan: C003</li>
                            <li>Detail: P001 × 1 (valid) → <strong>P004 × 999</strong> (stok hanya 25)</li>
                            <li>Harapan: pesan "Stok P004 tidak cukup"</li>
                            <li>ROLLBACK — tidak ada yang tersimpan</li>
                        </ul>
                        <form method="post" action="<?= base_url('uji-transaksi/jalankan') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="skenario" value="4">
                            <button class="btn btn-secondary w-100">▶ Jalankan Skenario 4</button>
                        </form>
                    </div>
                </div>
            </div>

        </div><!-- /row -->
    </div>
</div>

<!-- ══ SQL VERIFIKASI ════════════════════════════════════════════════════ -->
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold bg-dark text-white">
        🔎 SQL Verifikasi (jalankan di psql/pgAdmin pada <code>db_penjualan_test</code>)
    </div>
    <div class="card-body">
        <pre class="bg-dark text-light p-3 rounded small"><code>-- 1. Cek semua penjualan
SELECT no_penjualan, tgl_penjualan, kode_pelanggan, total
FROM penjualan ORDER BY no_penjualan;

-- 2. Cek semua detil
SELECT no_penjualan, no_urut, kode_produk, jumlah, harga, subtotal
FROM detil_penjualan ORDER BY no_penjualan, no_urut;

-- 3. Cek stok produk
SELECT kode_produk, nama_produk, stok FROM produk ORDER BY kode_produk;

-- 4. Validitas total header vs SUM detil (harus 0 baris)
SELECT p.no_penjualan,
       p.total                        AS total_header,
       COALESCE(SUM(d.subtotal), 0)   AS total_detail
FROM penjualan p
LEFT JOIN detil_penjualan d ON d.no_penjualan = p.no_penjualan
GROUP BY p.no_penjualan, p.total
HAVING p.total &lt;&gt; COALESCE(SUM(d.subtotal), 0);  -- harus 0 baris

-- 5. Reset data uji (impor ulang ke db_penjualan_test)
-- Jalankan di terminal:
-- psql -U postgres -d db_penjualan_test -f schema_seed_4tabel.sql</code></pre>
    </div>
</div>

<!-- ══ CONTOH LOG ════════════════════════════════════════════════════════ -->
<div class="card shadow-sm">
    <div class="card-header fw-bold bg-dark text-white">
        📄 Contoh Baris Log (writable/logs/log-<?= date('Y-m-d') ?>.log)
    </div>
    <div class="card-body">
        <pre class="bg-dark text-light p-3 rounded small"><code><?= date('Y-m-d') ?> INFO [TRX-PENJUALAN] MULAI no=J20261003 db=db_penjualan_test
<?= date('Y-m-d') ?> INFO [TRX-PENJUALAN] COMMIT no=J20261003

<?= date('Y-m-d') ?> INFO [TRX-PENJUALAN] MULAI no=J20261004 db=db_penjualan_test
<?= date('Y-m-d') ?> ERROR [TRX-PENJUALAN] ROLLBACK no=J20261004 alasan=Produk P999 tidak ditemukan.

<?= date('Y-m-d') ?> INFO [TRX-PENJUALAN] MULAI no=J20261005 db=db_penjualan_test
<?= date('Y-m-d') ?> ERROR [TRX-PENJUALAN] ROLLBACK no=J20261005 alasan=SQLSTATE[23503]: Foreign key violation: ...

<?= date('Y-m-d') ?> INFO [TRX-PENJUALAN] MULAI no=J20261006 db=db_penjualan_test
<?= date('Y-m-d') ?> ERROR [TRX-PENJUALAN] ROLLBACK no=J20261006 alasan=Stok P004 tidak cukup (tersedia: 25, diminta: 999).</code></pre>
        <p class="small text-muted mt-2 mb-0">
            Cara buka log: <code>cat writable/logs/log-<?= date('Y-m-d') ?>.log</code>
            &nbsp;|&nbsp;
            Filter: <code>grep "TRX-PENJUALAN" writable/logs/log-<?= date('Y-m-d') ?>.log</code>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
