<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">🛍️ Daftar Produk</h2>
    <a href="<?= base_url('produk/tambah') ?>" class="btn btn-success">+ Tambah Produk</a>
</div>

<!-- Form pencarian — keyword dipertahankan saat ganti halaman -->
<form class="row g-2 mb-3" method="get" action="<?= base_url('produk') ?>">
    <div class="col-sm-8 col-md-6">
        <input type="text" name="q" class="form-control"
               placeholder="Cari nama produk…"
               value="<?= esc($keyword) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" type="submit">🔍 Cari</button>
        <?php if ($keyword !== ''): ?>
            <a href="<?= base_url('produk') ?>" class="btn btn-outline-secondary">✕ Reset</a>
        <?php endif; ?>
    </div>
</form>

<?php if ($keyword !== ''): ?>
    <p class="text-muted small">
        Menampilkan <?= count($produk) ?> dari <?= $total ?> hasil untuk:
        <strong><?= esc($keyword) ?></strong>
    </p>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Satuan</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Stok</th>
                    <th class="text-center" style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produk)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            Tidak ada produk<?= $keyword !== '' ? ' yang sesuai pencarian' : '' ?>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produk as $p): ?>
                        <tr>
                            <td><code><?= esc($p['kode_produk']) ?></code></td>
                            <td><?= esc($p['nama_produk']) ?></td>
                            <td><?= esc($p['satuan']) ?></td>
                            <td class="text-end">Rp <?= number_format((float)$p['harga_jual'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float)$p['harga_beli'], 0, ',', '.') ?></td>
                            <td class="text-end"><?= esc($p['stok']) ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('produk/ubah/' . esc($p['kode_produk'])) ?>"
                                   class="btn btn-sm btn-warning">✏️ Ubah</a>

                                <!-- Hapus via POST + CSRF -->
                                <form method="post"
                                      action="<?= base_url('produk/hapus/' . esc($p['kode_produk'])) ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus produk <?= esc($p['kode_produk'], 'js') ?>?')">
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

<!-- Pagination manual — keyword dipertahankan -->
<?php if ($totalPage > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm justify-content-center">
            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link"
                       href="<?= base_url('produk') ?>?q=<?= urlencode($keyword) ?>&page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?= $this->endSection() ?>
