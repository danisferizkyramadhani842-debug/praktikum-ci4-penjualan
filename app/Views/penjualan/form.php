<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-3">🧾 <?= esc($title) ?></h2>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        ❌ <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Datalist produk untuk input[list] di baris detail -->
<datalist id="daftarProduk">
    <?php foreach ($produk as $pr): ?>
        <option
            value="<?= esc($pr['kode_produk']) ?>"
            data-harga="<?= (float)$pr['harga_jual'] ?>"
            data-nama="<?= esc($pr['nama_produk']) ?>">
            <?= esc($pr['kode_produk']) ?> - <?= esc($pr['nama_produk']) ?>
        </option>
    <?php endforeach; ?>
</datalist>

<form method="post" action="<?= base_url('penjualan/simpan') ?>" id="formPenjualan">
    <?= csrf_field() ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white fw-bold">Header Penjualan</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">No. Penjualan <span class="text-danger">*</span></label>
                    <input type="text" name="no_penjualan" class="form-control"
                           value="<?= esc(old('no_penjualan', $noPenjualan)) ?>"
                           maxlength="15" required>
                    <div class="form-text">Dibuat otomatis, boleh diubah manual.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_penjualan" class="form-control"
                           value="<?= esc(old('tgl_penjualan', date('Y-m-d'))) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pelanggan <span class="text-danger">*</span></label>
                    <select name="kode_pelanggan" class="form-select" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($pelanggan as $pl): ?>
                            <option value="<?= esc($pl['kode_pelanggan']) ?>"
                                <?= old('kode_pelanggan') === $pl['kode_pelanggan'] ? 'selected' : '' ?>>
                                <?= esc($pl['kode_pelanggan']) ?> - <?= esc($pl['nama_pelanggan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control"
                           value="<?= esc(old('keterangan', '')) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel detail dinamis -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-secondary text-white fw-bold">
            Detail Produk
            <span class="badge bg-warning text-dark ms-2">Minimal 2 baris</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0" id="tabelDetail">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="width:200px">Kode Produk</th>
                        <th>Nama Produk</th>
                        <th style="width:100px">Jumlah</th>
                        <th style="width:150px" class="text-end">Harga</th>
                        <th style="width:160px" class="text-end">Subtotal</th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody id="bodyDetail">
                    <!-- Baris diisi oleh JavaScript -->
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="5" class="text-end">Total</td>
                        <td class="text-end" id="totalAkhir">Rp 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-outline-success btn-sm" id="btnTambahBaris">
                ➕ Tambah Baris
            </button>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">💾 Simpan Transaksi</button>
        <a href="<?= base_url('penjualan') ?>" class="btn btn-outline-secondary">← Batal</a>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Data produk dari PHP → JavaScript (untuk lookup harga) ──────────────────
const dataProduk = {};
document.querySelectorAll('#daftarProduk option').forEach(opt => {
    dataProduk[opt.value] = {
        harga : parseFloat(opt.dataset.harga) || 0,
        nama  : opt.dataset.nama || ''
    };
});

// ── Data lama dari withInput (jika form gagal validasi) ─────────────────────
const oldKode   = <?= json_encode(old('kode_produk', [])) ?>;
const oldJumlah = <?= json_encode(old('jumlah', [])) ?>;

let barisCount = 0;

function formatRp(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('.baris-subtotal').forEach(el => {
        total += parseFloat(el.dataset.nilai || 0);
    });
    document.getElementById('totalAkhir').textContent = formatRp(total);
}

function buatBaris(kodeAwal = '', jumlahAwal = '') {
    const idx    = barisCount++;
    const tbody  = document.getElementById('bodyDetail');
    const tr     = document.createElement('tr');
    tr.id        = `baris-${idx}`;
    tr.dataset.idx = idx;

    tr.innerHTML = `
        <td class="align-middle text-center">${idx + 1}</td>
        <td>
            <input type="text" name="kode_produk[]"
                   class="form-control form-control-sm input-kode text-uppercase"
                   list="daftarProduk"
                   value="${kodeAwal}"
                   placeholder="Kode Produk"
                   autocomplete="off">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm input-nama"
                   value="" readonly placeholder="(nama otomatis)">
        </td>
        <td>
            <input type="number" name="jumlah[]"
                   class="form-control form-control-sm input-jumlah"
                   min="1" value="${jumlahAwal}" placeholder="0">
        </td>
        <td class="text-end align-middle">
            <span class="input-harga">Rp 0</span>
            <input type="hidden" class="val-harga" value="0">
        </td>
        <td class="text-end align-middle">
            <span class="baris-subtotal input-subtotal" data-nilai="0">Rp 0</span>
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris"
                    onclick="hapusBaris(${idx})">✕</button>
        </td>
    `;

    tbody.appendChild(tr);

    const inputKode   = tr.querySelector('.input-kode');
    const inputJumlah = tr.querySelector('.input-jumlah');

    // Event: kode produk berubah → isi harga & nama
    inputKode.addEventListener('change', () => updateHarga(tr));
    inputKode.addEventListener('input',  () => updateHarga(tr));

    // Event: jumlah berubah → hitung subtotal
    inputJumlah.addEventListener('input', () => hitungSubtotal(tr));

    // Jika ada nilai awal (withInput), isi harga sekarang
    if (kodeAwal !== '') {
        updateHarga(tr);
        if (jumlahAwal !== '') hitungSubtotal(tr);
    }
}

function updateHarga(tr) {
    const kode  = tr.querySelector('.input-kode').value.trim().toUpperCase();
    const info  = dataProduk[kode] || { harga: 0, nama: '' };

    tr.querySelector('.input-nama').value        = info.nama;
    tr.querySelector('.input-harga').textContent = formatRp(info.harga);
    tr.querySelector('.val-harga').value         = info.harga;

    hitungSubtotal(tr);
}

function hitungSubtotal(tr) {
    const harga   = parseFloat(tr.querySelector('.val-harga').value) || 0;
    const jumlah  = parseInt(tr.querySelector('.input-jumlah').value) || 0;
    const sub     = harga * jumlah;
    const spanSub = tr.querySelector('.baris-subtotal');

    spanSub.textContent    = formatRp(sub);
    spanSub.dataset.nilai  = sub;

    hitungTotal();
}

function hapusBaris(idx) {
    const tbody = document.getElementById('bodyDetail');
    if (tbody.rows.length <= 1) {
        alert('Minimal harus ada 1 baris detail.');
        return;
    }
    const tr = document.getElementById(`baris-${idx}`);
    if (tr) tr.remove();
    hitungTotal();
    // Perbarui nomor urut tampilan
    document.querySelectorAll('#bodyDetail tr').forEach((row, i) => {
        row.cells[0].textContent = i + 1;
    });
}

// ── Tombol tambah baris ─────────────────────────────────────────────────────
document.getElementById('btnTambahBaris').addEventListener('click', () => buatBaris());

// ── Inisialisasi: 2 baris awal (atau restore dari withInput) ────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (oldKode.length > 0) {
        // Restore baris dari withInput setelah validasi gagal
        oldKode.forEach((kode, i) => buatBaris(kode, oldJumlah[i] || ''));
    } else {
        // Form baru: tampilkan 2 baris kosong
        buatBaris();
        buatBaris();
    }
});
</script>
<?= $this->endSection() ?>
