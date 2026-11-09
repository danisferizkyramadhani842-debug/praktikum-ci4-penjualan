## Requirement
- PHP 8.2+
- Composer
- XAMPP (Apache)
- PostgreSQL 14+ & pgAdmin 4

---

## Cara Install

### 1. Clone repo & install dependencies
```bash
git clone https://github.com/USERNAME/NAMA_REPO.git
cd NAMA_REPO
composer install
```

### 2. Buat 2 database di pgAdmin
- `db_penjualan`
- `db_penjualan_test`

### 3. Import schema + seed data
Buka **Query Tool** di pgAdmin, jalankan file `schema_seed_4tabel.sql`
ke **kedua** database.

### 4. Setup konfigurasi
```bash
copy env.example .env
```
Edit `.env`, sesuaikan:
```ini
app.baseURL = 'http://localhost/NAMA_FOLDER/public/'
database.default.password = PASSWORD_POSTGRESQL_ANDA
database.tests.password   = PASSWORD_POSTGRESQL_ANDA
```

### 5. Edit `app/Config/App.php`
```php
public string $baseURL = 'http://localhost/NAMA_FOLDER/public/';
```

### 6. Edit `public/.htaccess`
Uncomment dan sesuaikan:
```apache
RewriteBase /NAMA_FOLDER/public/
```

### 7. Aktifkan ekstensi PostgreSQL di `php.ini` XAMPP
```ini
extension=pdo_pgsql
extension=pgsql
```
Restart Apache setelah diubah.

---

## URL Aplikasi

| URL | Halaman |
|-----|---------|
| `/public/db-test` | Uji koneksi 2 database |
| `/public/produk` | CRUD Produk + pencarian + pagination |
| `/public/pelanggan` | CRUD Pelanggan |
| `/public/penjualan` | Daftar Penjualan |
| `/public/penjualan/tambah` | Form Tambah Transaksi |
| `/public/uji-transaksi` | Uji 4 Skenario Transaksi |

---

## Skenario Uji Transaksi

| No | Keterangan | Harapan |
|----|-----------|---------|
| 1 | J20261003, P001×2 + P003×3 | ✅ COMMIT, total 134.000 |
| 2 | J20261004, P002×1 + **P999×1** (tidak ada) | ❌ ROLLBACK, pesan error |
| 3 | J20261005, FK violation tanpa cek aplikasi | ❌ ROLLBACK, DatabaseException |
| 4 | J20261006, P004×999 (stok hanya 25) | ❌ ROLLBACK, stok tidak cukup |

---

## Lihat Log Transaksi