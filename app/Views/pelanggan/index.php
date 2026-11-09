<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">👥 Daftar Pelanggan</h2>
    <a href="<?= base_url('pelanggan/tambah') ?>" class="btn btn-success">+ Tambah Pelanggan</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Kode</th>
                    <th>Nama Pelanggan</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th class="text-center" style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pelanggan)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Belum ada data pelanggan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pelanggan as $p): ?>
                        <tr>
                            <td><code><?= esc($p['kode_pelanggan']) ?></code></td>
                            <td><?= esc($p['nama_pelanggan']) ?></td>
                            <td><?= esc($p['no_telepon'] ?? '-') ?></td>
                            <td><?= esc($p['alamat'] ?? '-') ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('pelanggan/ubah/' . esc($p['kode_pelanggan'])) ?>"
                                   class="btn btn-sm btn-warning">✏️ Ubah</a>

                                <form method="post"
                                      action="<?= base_url('pelanggan/hapus/' . esc($p['kode_pelanggan'])) ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus pelanggan <?= esc($p['kode_pelanggan'], 'js') ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
