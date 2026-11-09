<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Aplikasi Penjualan CI4') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: .5px; }
        .table-hover tbody tr:hover { background-color: #f0f7ff; }
        footer { font-size: .82rem; color: #6c757d; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">📦 App Penjualan</a>
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'produk') ? 'active fw-bold' : '' ?>"
                       href="<?= base_url('produk') ?>">🛍️ Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'pelanggan') ? 'active fw-bold' : '' ?>"
                       href="<?= base_url('pelanggan') ?>">👥 Pelanggan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'penjualan') ? 'active fw-bold' : '' ?>"
                       href="<?= base_url('penjualan') ?>">🧾 Penjualan</a>
                </li>
                <?php if (ENVIRONMENT !== 'production'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'uji-transaksi') ? 'active fw-bold' : '' ?>"
                       href="<?= base_url('uji-transaksi') ?>">🧪 Uji Transaksi</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('db-test') ?>">🔌 DB Test</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mb-5">

    <?php if (session()->getFlashdata('sukses')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ <?= esc(session()->getFlashdata('sukses')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            ℹ️ <?= esc(session()->getFlashdata('info')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>

</div>

<footer class="text-center py-3 border-top">
    <small>Praktikum CodeIgniter 4 — Query Builder &amp; Transaction &bull; <?= date('Y') ?></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmTEBzJWFkMFPR9J0O3Ee9DVo7F"
        crossorigin="anonymous"></script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
