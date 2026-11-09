<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-4">🔌 Uji Koneksi Database</h2>

<?php foreach ($hasil as $group => $info): ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-<?= $info['status'] === 'OK' ? 'success' : 'danger' ?> text-white fw-bold">
            Group: <?= esc(strtoupper($group)) ?> — Status: <?= esc($info['status']) ?>
        </div>
        <div class="card-body">
            <?php if ($info['status'] === 'OK'): ?>
                <table class="table table-sm table-bordered mb-0">
                    <tr><th style="width:180px">Database</th><td><?= esc($info['database']) ?></td></tr>
                    <tr><th>Driver</th><td><?= esc($info['driver']) ?></td></tr>
                    <tr><th>PostgreSQL Version</th><td><?= esc($info['versi']) ?></td></tr>
                </table>
            <?php else: ?>
                <p class="text-danger mb-0"><strong>Error:</strong> <?= esc($info['pesan']) ?></p>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

<a href="<?= base_url('produk') ?>" class="btn btn-primary">← Ke Produk</a>

<?= $this->endSection() ?>
