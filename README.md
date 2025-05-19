
## Arisan Digital Web

Aplikasi yang digunakan untuk mengelola arisan dengan mudah.

## V2 Getting Started

### Inisialisasi
Jalankan ini untuk memulai migrasi ke versi terbaru
- Run php artisan migrate
- Generate owners group: http://localhost:8000/api/v2/group/generate-owner
- Generate user id members: http://localhost:8000/api/v2/group/generate-user-id-member
- Generate user code: http://localhost:8000/api/v2/user/generate-code
- Jalankan generate winners ke tabel baru: http://localhost:8000/api/v2
/arisan-history/init-winners

###
- Kolom owner_id di tabel members tidak digunakan lagi
