-- schema_seed_4tabel.sql
-- Urutan DROP: tabel anak dulu, lalu induk
DROP TABLE IF EXISTS detil_penjualan CASCADE;
DROP TABLE IF EXISTS penjualan CASCADE;
DROP TABLE IF EXISTS pelanggan CASCADE;
DROP TABLE IF EXISTS produk CASCADE;

-- =============================================
-- TABEL PRODUK
-- =============================================
CREATE TABLE produk (
    kode_produk  VARCHAR(10)   PRIMARY KEY,
    nama_produk  VARCHAR(100)  NOT NULL,
    satuan       VARCHAR(20)   NOT NULL,
    harga_jual   NUMERIC(12,2) NOT NULL CHECK (harga_jual >= 0),
    harga_beli   NUMERIC(12,2) NOT NULL CHECK (harga_beli >= 0),
    stok         INTEGER       NOT NULL DEFAULT 0 CHECK (stok >= 0),
    deskripsi    TEXT
);

-- =============================================
-- TABEL PELANGGAN
-- =============================================
CREATE TABLE pelanggan (
    kode_pelanggan  VARCHAR(10)  PRIMARY KEY,
    nama_pelanggan  VARCHAR(100) NOT NULL,
    no_telepon      VARCHAR(25),
    alamat          TEXT
);

-- =============================================
-- TABEL PENJUALAN (FK ke pelanggan)
-- =============================================
CREATE TABLE penjualan (
    no_penjualan    VARCHAR(15)   PRIMARY KEY,
    tgl_penjualan   DATE          NOT NULL,
    kode_pelanggan  VARCHAR(10)   NOT NULL REFERENCES pelanggan(kode_pelanggan),
    total           NUMERIC(12,2) NOT NULL DEFAULT 0 CHECK (total >= 0),
    keterangan      TEXT
);

-- =============================================
-- TABEL DETIL_PENJUALAN (FK ke penjualan & produk)
-- =============================================
CREATE TABLE detil_penjualan (
    no_penjualan  VARCHAR(15)   NOT NULL REFERENCES penjualan(no_penjualan) ON DELETE CASCADE,
    no_urut       INTEGER       NOT NULL CHECK (no_urut > 0),
    kode_produk   VARCHAR(10)   NOT NULL REFERENCES produk(kode_produk),
    jumlah        INTEGER       NOT NULL CHECK (jumlah > 0),
    harga         NUMERIC(12,2) NOT NULL CHECK (harga >= 0),
    subtotal      NUMERIC(12,2) NOT NULL CHECK (subtotal >= 0),
    PRIMARY KEY (no_penjualan, no_urut)
);

-- =============================================
-- INDEX
-- =============================================
CREATE INDEX idx_penjualan_tanggal   ON penjualan(tgl_penjualan);
CREATE INDEX idx_penjualan_pelanggan ON penjualan(kode_pelanggan);
CREATE INDEX idx_detil_produk        ON detil_penjualan(kode_produk);

-- =============================================
-- DATA SEED
-- =============================================
INSERT INTO produk (kode_produk, nama_produk, satuan, harga_jual, harga_beli, stok, deskripsi) VALUES
('P001', 'Kertas A4',     'rim',    58000, 47000,  40, 'Kertas A4 80 gsm'),
('P002', 'Pulpen Hitam',  'buah',    3500,  2000, 150, 'Pulpen tinta hitam'),
('P003', 'Buku Tulis',    'buah',    6000,  3800, 100, 'Buku tulis 58 lembar'),
('P004', 'Tinta Printer', 'botol',  85000, 69000,  25, 'Tinta printer hitam'),
('P005', 'Map Plastik',   'buah',    2500,  1200, 200, 'Map dokumen');

INSERT INTO pelanggan (kode_pelanggan, nama_pelanggan, no_telepon, alamat) VALUES
('C001', 'Toko Maju',       '081200000001', 'Surabaya'),
('C002', 'Koperasi Kampus', '081200000002', 'Sidoarjo'),
('C003', 'CV Sejahtera',    '081200000003', 'Gresik');

INSERT INTO penjualan (no_penjualan, tgl_penjualan, kode_pelanggan, total, keterangan) VALUES
('J20261001', '2026-10-01', 'C001', 119000, 'Transaksi contoh'),
('J20261002', '2026-10-02', 'C002',  18000, 'Transaksi contoh');

-- harga di detil = harga saat transaksi (bisa beda dari harga_jual saat ini)
INSERT INTO detil_penjualan (no_penjualan, no_urut, kode_produk, jumlah, harga, subtotal) VALUES
('J20261001', 1, 'P001', 2, 58000, 116000),
('J20261001', 2, 'P002', 1,  3000,   3000),
('J20261002', 1, 'P003', 3,  6000,  18000);
