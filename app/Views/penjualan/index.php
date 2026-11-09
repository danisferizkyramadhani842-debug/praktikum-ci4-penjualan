<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">🧾 Daftar Penjualan</h2>
    <a href="<?= base_url('penjualan/tambah') ?>" class="btn btn-success">+ Tambah Penjualan</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="table-primary">
                <tr>
                    <th>No. Penjualan</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th class="text-end">Total</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($penjualan)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Belum ada data penjualan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($penjualan as $p): ?>
                        <tr>
                            <td><code><?= esc($p['no_penjualan']) ?></code></td>
                            <td><?= esc(date('d/m/Y', strtotime($p['tgl_penjualan']))) ?></td>
                            <td><?= esc($p['nama_pelanggan']) ?></td>
                            <td class="text-end">Rp <?= number_format((float)$p['total'], 0, ',', '.') ?></td>
                            <td><?= esc($p['keterangan'] ?? '') ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('penjualan/detail/' . esc($p['no_penjualan'])) ?>"
                                   class="btn btn-sm btn-info">🔍 Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
