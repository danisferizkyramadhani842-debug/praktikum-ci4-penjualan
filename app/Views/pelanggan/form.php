<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
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
                $formAction = $mode === 'tambah'
                    ? base_url('pelanggan/simpan')
                    : base_url('pelanggan/update/' . esc($pelanggan['kode_pelanggan']));
                ?>

                <form method="post" action="<?= $formAction ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Pelanggan <span class="text-danger">*</span></label>
                        <?php if ($mode === 'tambah'): ?>
                            <input type="text" name="kode_pelanggan" class="form-control text-uppercase"
                                   maxlength="10" required
                                   value="<?= esc(old('kode_pelanggan', '')) ?>"
                                   placeholder="Contoh: C004">
                        <?php else: ?>
                            <input type="text" class="form-control"
                                   value="<?= esc($pelanggan['kode_pelanggan']) ?>" disabled>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelanggan" class="form-control"
                               maxlength="100" required
                               value="<?= esc(old('nama_pelanggan', $pelanggan['nama_pelanggan'] ?? '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control"
                               maxlength="25"
                               value="<?= esc(old('no_telepon', $pelanggan['no_telepon'] ?? '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= esc(old('alamat', $pelanggan['alamat'] ?? '')) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <?= $mode === 'tambah' ? '💾 Simpan' : '💾 Update' ?>
                        </button>
                        <a href="<?= base_url('pelanggan') ?>" class="btn btn-outline-secondary">← Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
