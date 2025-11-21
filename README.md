# Laravel REST API Perpustakaan

Ini adalah project **Backend Developer** (Laravel + PostgreSQL) untuk menyelesaikan soal test PT Aneka Bintang Gading:
- API Autentikasi
- API CRUD Kategori Buku
- API CRUD Buku
- API Peminjaman Buku
- API Pengembalian Buku
- API Daftar Buku yang Dipinjam

## Instalasi Project
```bash
1. Create DB `laravel_rest_api_perpustakaan`.
2. CD ke direktori `laravel-rest-api-perpustakaan`
3. composer install
4. Sesuaikan configurasi .env
4. Jalankan perintah `php artisan migrate --seed`
5. php artisan serve
```

## Testing API
Buka Postman:
import file collection (.json) yang ada di root project **`Rest API Perpustakaan.postman_collection.json`**