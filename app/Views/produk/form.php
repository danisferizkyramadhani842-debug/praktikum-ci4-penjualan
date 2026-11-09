<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?= esc($title) ?></h5>
            </div>
            <div class="card-body">

                <?php if (isset($validation)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($validation->getErrors() as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php
                // URL dan method berbeda untuk tambah vs ubah
                $formAction = $mode === 'tambah'
                    ? base_url('produk/simpan')
                    : base_url('produk/update/' . esc($produk['kode_produk']));
                ?>

                <form method="post" action="<?= $formAction ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Produk <span class="text-danger">*</span></label>
                        <?php if ($mode === 'tambah'): ?>
                            <input type="text" name="kode_produk" class="form-control text-uppercase"
                                   maxlength="10" required
                                   value="<?= esc(old('kode_produk', '')) ?>"
                                   placeholder="Contoh: P006">
                        <?php else: ?>
                            <!-- Kode tidak bisa diubah saat edit -->
                            <input type="text" class="form-control" value="<?= esc($produk['kode_produk']) ?>" disabled>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control"
                               maxlength="100" required
                               value="<?= esc(old('nama_produk', $produk['nama_produk'] ?? '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="satuan" class="form-control"
                               maxlength="20" required
                               value="<?= esc(old('satuan', $produk['satuan'] ?? '')) ?>"
                               placeholder="Contoh: rim, buah, botol">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="harga_jual" class="form-control"
                                   min="0" step="0.01" required
                                   value="<?= esc(old('harga_jual', $produk['harga_jual'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Beli (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="harga_beli" class="form-control"
                                   min="0" step="0.01" required
                                   value="<?= esc(old('harga_beli', $produk['harga_beli'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control"
                               min="0" required
                               value="<?= esc(old('stok', $produk['stok'] ?? '0')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= esc(old('deskripsi', $produk['deskripsi'] ?? '')) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <?= $mode === 'tambah' ? '💾 Simpan' : '💾 Update' ?>
                        </button>
                        <a href="<?= base_url('produk') ?>" class="btn btn-outline-secondary">← Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
