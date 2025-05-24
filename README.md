
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

### Notes
- Kolom owner_id di tabel members tidak digunakan lagi

## Notifications
Kirim ke anggota
- (done) Undangan masuk ke grub
- (done) reminder request invitation member notifikasi manual
- (done) Tagih bayar arisan
- (done) Didaftarkan atau dihapus sebagai pengelola
- (done) Pemenang arisan
- (done) Dihapus dari anggota

Kirim ke pengelola
- (done) Anggota menerima undangan
- (done) Request daftar sebagai member dari anggota