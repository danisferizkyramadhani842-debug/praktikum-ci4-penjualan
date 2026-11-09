<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">🧾 Detail Penjualan</h2>
    <a href="<?= base_url('penjualan') ?>" class="btn btn-outline-secondary">← Daftar Penjualan</a>
</div>

<!-- Header -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white fw-bold">Informasi Transaksi</div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3">
                <small class="text-muted d-block">No. Penjualan</small>
                <strong><?= esc($header['no_penjualan']) ?></strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Tanggal</small>
                <strong><?= esc(date('d/m/Y', strtotime($header['tgl_penjualan']))) ?></strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Pelanggan</small>
                <strong><?= esc($header['nama_pelanggan']) ?></strong>
                <small class="text-muted">(<?= esc($header['kode_pelanggan']) ?>)</small>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Total</small>
                <strong class="text-success fs-5">
                    Rp <?= number_format((float)$header['total'], 0, ',', '.') ?>
                </strong>
            </div>
            <?php if (!empty($header['keterangan'])): ?>
            <div class="col-12">
                <small class="text-muted d-block">Keterangan</small>
                <span><?= esc($header['keterangan']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tabel detil -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white fw-bold">Detail Produk</div>
    <div class="card-body p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width:50px">No.</th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Satuan</th>
                    <th class="text-end">Jumlah</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($detil)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada detail.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($detil as $d): ?>
                        <tr>
                            <td class="text-center"><?= esc($d['no_urut']) ?></td>
                            <td><code><?= esc($d['kode_produk']) ?></code></td>
                            <td><?= esc($d['nama_produk']) ?></td>
                            <td><?= esc($d['satuan']) ?></td>
                            <td class="text-end"><?= esc($d['jumlah']) ?></td>
                            <td class="text-end">Rp <?= number_format((float)$d['harga'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float)$d['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <td colspan="6" class="text-end">Total</td>
                    <td class="text-end text-success">
                        Rp <?= number_format((float)$header['total'], 0, ',', '.') ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="<?= base_url('penjualan/tambah') ?>" class="btn btn-success">+ Tambah Penjualan Baru</a>
</div>

<?= $this->endSection() ?>
