# CLAUDE.md — SportPlace: Modul Mitra (Register → Login → Dashboard Mitra)

> Dokumen kerja untuk Claude Code. Sumber kebenaran kebutuhan: `docs/PRD_Sportplace.md`
> (simpan PRD di path tersebut). File ini hanya merangkum **bagian Mitra** dan urutan pengerjaannya.
> Bila ada konflik antara file ini dan PRD, **PRD menang**; catat konfliknya di `docs/DECISIONS.md`.

---

## 1. Konteks dan Lingkup Saya

SportPlace adalah platform web penyewaan lapangan olahraga multi-cabang (Laravel).
Ada 3 aktor: **Penyewa** (`customer`), **Mitra** (`mitra`), **Admin** (`admin`).

**Tugas saya: menyelesaikan seluruh sisi MITRA**, dimulai dari registrasi sebagai mitra dan login,
sampai dashboard, listing, jadwal, harga, pesanan, dan laporan.

| Termasuk lingkup | Di luar lingkup (dikerjakan orang lain, jangan disentuh) |
|---|---|
| Registrasi peran Mitra, OTP, login, reset password, logout | Halaman publik (beranda, explore, detail lapangan) |
| Pendaftaran mitra + dokumen legalitas, status verifikasi, edit/ajukan ulang | Alur Penyewa (kalender, checkout, bayar, refund, ulasan) |
| Dashboard Mitra, Venue, Lapangan, Jadwal & Blokir, Harga | Panel Admin (verifikasi mitra, moderasi, master data, komisi, refund) |
| Pesanan masuk, verifikasi transfer manual, no-show, completed (+bulk) | Payment gateway |
| Laporan pendapatan mitra, notifikasi sisi mitra | |

> **Catatan koordinasi:** halaman Register/Login (PUB-04, PUB-05, PUB-06) dipakai bersama semua peran.
> Bangun dengan benar untuk 3 peran sekaligus, tetapi fokus pengujian saya pada peran `mitra`.
> Beri tahu pemilik modul lain sebelum mengubah perilaku yang memengaruhi `customer`/`admin`.

---

## 2. Aturan Kerja (WAJIB)

1. **Baca bagian PRD yang relevan sebelum mengerjakan tiap task** (rujukan FR/AC ada di setiap task).
2. **Satu task = satu cabang/commit fokus.** Jangan mencampur beberapa fase.
3. **Jangan mengarang keputusan.** Item bertanda **Perlu Konfirmasi / OQ-xxx** di PRD belum diputuskan.
   Pakai *asumsi default* di bagian 9, buat dalam bentuk yang mudah diubah (config/konstanta/enum),
   dan catat di `docs/DECISIONS.md` (format: tanggal, OQ, asumsi, file terdampak).
4. **Perubahan skema hanya lewat migration Laravel baru.** Jangan mengedit skrip SQL ERD asli.
   Setiap migration harus menyebut GAP-xx yang diselesaikannya di komentar.
5. **Kepemilikan data (BR-023):** mitra hanya boleh melihat/mengubah venue, lapangan, pesanan miliknya.
   Wajib lewat **Laravel Policy**, bukan hanya filter query. Akses milik orang lain → **403**.
6. **Validasi lewat Form Request.** Pesan galat dalam Bahasa Indonesia, per field.
7. **Keamanan:** password hash bcrypt/argon2; rate limit login/OTP/reset; dokumen KTP/NPWP & bukti
   transfer disimpan di **disk privat** (bukan `public`) dan diakses lewat route terotorisasi.
8. **Jangan menambah warna, font, atau token UI di luar `desain.md`** (lihat bagian 5).
9. **Setiap task selesai harus punya test** (Feature test minimal untuk AC yang tercantum).
10. **Nama tabel/kolom persis seperti ERD** (termasuk ejaan `succes`, `complated_at`). Tabel `vanues`
    di SQL asli sudah dikoreksi PRD menjadi `venues` / kolom `*_venue` — ikuti PRD, catat pemetaan
    di model bila migration asli berbeda (OQ-024).
11. Jika terblokir keputusan/dependensi, **tandai task `BLOCKED` + alasan** lalu lanjut task lain.
    Jangan menebak lalu diam-diam mengimplementasikan.

---

## 3. Tech Stack dan Konvensi

Versi Laravel/PHP/DB **belum ditentukan di PRD** (OQ-007). Langkah pertama: baca `composer.json`
dan struktur repo yang sudah ada, ikuti yang ada. Jika repo kosong, gunakan asumsi default:

- Laravel (versi stabil terbaru yang didukung), PHP sesuai Laravel, **MySQL/MariaDB** (sintaks ERD).
- **Auth berbasis sesi web** (Laravel Breeze/Fortify-style) dengan Blade + Tailwind.
  PRD menyebut JWT (NFR02) tetapi cocok-tidaknya untuk aplikasi Blade belum diputuskan → catat di DECISIONS.
- Struktur **modular** (NFR08): pisahkan namespace/route untuk Auth, Mitra, (Booking/Payment/Admin bukan milik saya).
- Route area mitra: prefix `/mitra`, nama route `mitra.*`, middleware `auth` + `role:mitra`.
- Fitur bawaan Laravel yang dianjurkan PRD: `password_reset_tokens`, `notifications`, Policy/Gate,
  Form Request, Queue + Scheduler untuk job terjadwal.
- Zona waktu: satu zona waktu aplikasi dari `config/app.php` (OQ-023 belum diputuskan).
- Uang: simpan/hitung dengan presisi desimal; jangan float (GAP-23 menyarankan DECIMAL).

Struktur yang disarankan:

```
app/Http/Controllers/Auth/...
app/Http/Controllers/Mitra/{Dashboard,Onboarding,Venue,Field,Schedule,Pricing,Order,Report}Controller.php
app/Http/Requests/Mitra/...
app/Policies/{VenuePolicy,FieldPolicy,BookingDetailPolicy,MitraProfilePolicy}.php
app/Http/Middleware/{EnsureRole,EnsureMitraApproved}.php
app/Services/Mitra/...        (logika bisnis: pricing, slot, report)
resources/views/mitra/...
tests/Feature/Mitra/...
docs/{PRD_Sportplace.md,DECISIONS.md}
```

---

## 4. Referensi Data (dari ERD / PRD §13)

**Role:** `users.role` ∈ `admin`, `mitra`, `customer` (satu nilai per akun; peran ganda belum ditentukan, OQ-008).

**Status yang relevan untuk Mitra**

| Entitas | Nilai | Catatan |
|---|---|---|
| `mitra_profiles.status` | `pending`, `approved`, `rejected` | `rejected` + `alasan_penolakan` |
| `venues.status_venue` | `draft`, `pending_review`, `aktif`, `nonaktif`, `rejected` | |
| `fields.status_lapangan` | enum yang sama dengan venue | default `draft` (BR-003) |
| `field_schedule.status_hari` | `buka`, `tutup` | satu baris per hari per lapangan |
| `booking_details.status_item` | `ongoing`, `completed`, `no_show` | |

**Siklus listing (BR-025):**
`draft → pending_review → aktif | rejected`; `aktif → nonaktif`. Ajukan ulang dari `rejected` dan
reaktivasi `nonaktif` = Perlu Konfirmasi (asumsi default di bagian 9).

**Tabel yang saya sentuh:** `users`, `mitra_profiles`, `venues`, `fields`, `field_facilities`,
`field_photo`, `field_schedule`, `field_blocks`, `booking_details` & `booking_headers` (baca/ubah status item),
`payments` (verifikasi manual), `wallets` / `wallet_transactions` (hanya setelah OQ-006).
**Master data** (`master_cities`, `master_sports`, `master_facilities`) hanya **dibaca** (pilihan dropdown, hanya yang `aktif`).

**Gap ERD yang menghambat modul saya (butuh migration — lihat Fase 0):**

| GAP | Kebutuhan | Dipakai oleh |
|---|---|---|
| GAP-20 | `users.password` ber-UNIQUE → hapus UNIQUE | Register |
| GAP-09 | Status verifikasi OTP / penyimpanan OTP | Register, Login |
| GAP-08 | Status akun (aktif/suspend/banned) | Login |
| GAP-15 | `created_at`/`updated_at` di semua tabel | Antrian, laporan |
| GAP-12 | Dokumen KTP/NPWP/SIUP, data usaha | Pendaftaran mitra |
| GAP-17 | Deskripsi venue & lapangan | Form venue/lapangan |
| GAP-16 | FK `booking_details.field_id → fields.id` | Pesanan |
| GAP-02 | Bukti transfer, catatan penolakan, verifikator, `pg_transaction_id` nullable | Verifikasi pembayaran |
| GAP-25 | Status/kolom check-in kehadiran | Konfirmasi kehadiran |
| GAP-13 | Tabel notifikasi | Notifikasi |
| GAP-14 | `audit_log` | Perubahan status penting |
| GAP-07 | Tarif komisi | Laporan (setelah OQ-004) |
| GAP-21 | Skema `meta_harga` JSON | Harga peak/off-peak |

---

## 5. Design System (ringkas — baca `desain.md` untuk detail)

- **Font tunggal: Poppins** (400/500/600/700; weight 800 hanya hero Public, **tidak** di area Mitra/Admin).
- Area **Partner/Mitra** bergaya *professional, clear, operational*: restrained, data-oriented.
- Angka: `Rp 150.000`, `3.842 Booking`, `Rp 486,2 Jt`; pakai `tabular-nums` untuk harga, tanggal, jam,
  rating, komisi; angka tabel rata kanan.
- Breakpoint tipografi: mobile < 640px, tablet 640–1024px, desktop > 1024px. **Mobile-first.**
- Aksesibilitas: body ≥ 16px di mobile, target tap ≥ 44×44px, line-height body ≥ 1.5, status
  **tidak hanya dibedakan warna** (mis. Blocked memakai pola garis diagonal).
- Rasio warna 60/30/10. Jadikan token `desain.md` sebagai CSS variables / Tailwind config.
- Komponen yang harus ada: tombol (primary, secondary, outline, ghost, danger, disabled), input
  (default/focus/error/success), card, badge status, modal konfirmasi, toast success, alert warning,
  sidebar (desktop) / bottom navigation (mobile), empty state, tabel data.
- Badge status booking mengikuti `desain.md` Bagian I §9; state kalender mengikuti §10.
- Figma `DPBPP` **belum bisa diverifikasi** (OQ-001): bangun dari `desain.md` + daftar halaman PRD,
  bukan dari tebakan visual.

---

## 6. Peta Halaman Mitra

| ID | Halaman | Akses | FR |
|---|---|---|---|
| PUB-04 | Login | belum login | FR-AUTH-003 |
| PUB-05 | Register (pilih peran Penyewa / Mitra) | belum login | FR-AUTH-001/002 |
| PUB-06 | Lupa / Reset Password | belum login | FR-AUTH-004 |
| MTR-01 | Pendaftaran Mitra (data usaha + dokumen) | `mitra` (semua status) | FR-MITRA-001 |
| MTR-02 | Status Verifikasi Akun | `mitra` belum `approved` | FR-MITRA-002 |
| MTR-12 | Profil Mitra & Dokumen Legalitas | `mitra` (semua status) | FR-MITRA-004 |
| MTR-03 | Dashboard Mitra | `mitra` **approved** | FR-REPORT-001 |
| MTR-04 | Daftar Venue → Daftar Lapangan | approved | FR-VENUE-*, FR-FIELD-* |
| MTR-05b | Tambah/Edit Venue | approved | FR-VENUE-001/003 |
| MTR-05 | Tambah/Edit Lapangan | approved | FR-FIELD-001/003 |
| MTR-06 | Jadwal Operasional & Blokir | approved, milik sendiri | FR-SCHED-001/002 |
| MTR-07 | Pengaturan Harga | approved, milik sendiri | FR-FIELD-006 |
| MTR-08 | Kalender Booking Masuk | approved, milik sendiri | FR-ORDER-001 |
| MTR-09 | Detail Pesanan / Verifikasi Pembayaran | approved, milik sendiri | FR-PAY-003, FR-ORDER-002 |
| MTR-10 | Bulk Penyelesaian Booking | approved | FR-ORDER-003 |
| MTR-11 | Laporan Pendapatan & Transaksi | approved | FR-REPORT-002 |

**Gerbang akses (PRD §12.2 / §12.6):** mitra `pending`/`rejected` hanya boleh MTR-01, MTR-02, MTR-12.
Selain itu → redirect ke MTR-02 dengan pesan *"Akun mitra Anda masih menunggu verifikasi."* (EC-17).

---

## 7. DAFTAR TUGAS

Legenda: `[ ]` belum · `[x]` selesai · `BLOCKED` menunggu keputusan/dependensi.
Prioritas dari PRD §20 (**Usulan**): **M** = Must, **S** = Should.

### FASE 0 — Fondasi

- [x] **T0.1 Audit repo & setup.** Baca `composer.json`, struktur, `.env`. Buat `docs/DECISIONS.md`. Pasang PRD di `docs/`.
- [ ] **T0.2 Migration penyelesaian GAP** (satu migration per kelompok, beri komentar GAP-xx) — **sebagian**: `users` (GAP-20/09/08/15/10) dan tabel `otp_codes` (GAP-09) selesai; sisanya (mitra_profiles, venues/fields deskripsi, booking_details FK, field_schedule/field_facilities unique, audit_log) belum dikerjakan, menyusul sesuai fase terkait:
  - `users`: hapus UNIQUE `password` (GAP-20); tambah `email_verified_at`/`phone_verified_at`, `status_akun` (`aktif`,`suspend`,`banned`) (GAP-08/09); `timestamps` (GAP-15); `foto_profil` nullable (GAP-10).
  - tabel OTP (kode ter-hash, tujuan email/HP, `expires_at`, `attempts`) (GAP-09).
  - `mitra_profiles`: kolom data usaha (mis. `nama_usaha`, `alamat_usaha`), path berkas `ktp`, `npwp`, `siup` (nullable), `timestamps`; **NPWP dijadikan nullable** bila diputuskan opsional (KS-07, F08 "jika ada").
  - `venues`/`fields`: `deskripsi` nullable (GAP-17), `timestamps`; `nama_venue` dijadikan NOT NULL (KS-21, Usulan).
  - `booking_details`: tambah FK `field_id → fields.id` (GAP-16).
  - `field_schedule`: unique `(field_id, hari)`; `field_facilities`: unique `(field_id, facilities_id)`.
  - `audit_log` (aktor, entitas, status lama/baru, waktu) (GAP-14).
  - Bawaan Laravel: `password_reset_tokens`, `notifications`.
  - **Jangan** migrasi GAP-02/GAP-25/GAP-07 sebelum diputuskan (lihat T8.x, T9.x).
- [x] **T0.3 Seeder dev:** 1 admin, 1 mitra `approved`, 1 mitra `pending`, 1 mitra `rejected`, master data (kota, olahraga, fasilitas `aktif`), beberapa venue/lapangan/booking contoh untuk pengujian.
- [x] **T0.4 Artisan command dev:** `mitra:approve {email}` / `mitra:reject {email} {alasan}` agar saya bisa menguji alur tanpa menunggu panel Admin. **Hanya untuk lingkungan non-produksi.**
- [x] **T0.5 Design tokens:** terjemahkan `desain.md` ke Tailwind config / CSS variables; pasang Poppins.
- [x] **T0.6 Layout dasar:** layout Auth, layout Mitra (sidebar desktop + bottom nav mobile, header, slot toast/alert), komponen badge status, modal konfirmasi, empty state.
- [x] **T0.7 Middleware & Policy dasar:** `EnsureRole`, `EnsureMitraApproved`, kerangka Policy kepemilikan (BR-023).

### FASE 1 — Autentikasi (Register & Login) — **M**

Rujukan: FR-AUTH-001…005, PUB-04/05/06, UJ-01/UJ-04, BR-026/BR-029/BR-018.

- [x] **T1.1 Register dengan pilihan peran** (FR-AUTH-001, PUB-05)
  - Pilihan peran: Penyewa atau Mitra/Pemilik Lapangan.
  - Field: `name` (wajib, ≤100), `email` (wajib, format, ≤255, **unik**), `password` (wajib; aturan kompleksitas Perlu Konfirmasi → default min 8 karakter + konfirmasi), `no_hp` (wajib, ≤20).
  - Simpan `users.role` = `customer` | `mitra`; password ter-hash.
  - **`role=admin` dari input publik wajib diabaikan/ditolak** (AC-FR-AUTH-001-04). Role diambil dari whitelist server, bukan dipercaya dari request mentah.
  - Setelah sukses → mulai OTP (T1.2).
  - AC: AUTH-001-01…04. Test: email duplikat ditolak tanpa membuat baris; mitra mendapat `role=mitra`; password tidak plaintext; payload `role=admin` tidak membuat admin.
- [x] **T1.2 Verifikasi OTP** (FR-AUTH-002)
  - Kirim OTP via email/HP (penyedia belum ditentukan, DEP-02) → bungkus di interface `OtpSender`, implementasi awal = log/mail driver.
  - Kode ter-hash, masa berlaku & batas percobaan dari **config** (default: 6 digit, 10 menit, 5 percobaan — asumsi, catat DECISIONS).
  - Kirim ulang OTP dengan cooldown. Rate limit.
  - AC: AUTH-002-01/02. Test: OTP benar menandai verified; OTP salah/kedaluwarsa ditolak.
- [x] **T1.3 Login + redirect berdasarkan peran** (FR-AUTH-003, PUB-04)
  - Login email + password; pesan galat **generik** (tidak membedakan email/password salah).
  - Tolak akun `suspend`/`banned` dengan pesan status (BR-018). Akun belum terverifikasi OTP → arahkan ke layar OTP.
  - Redirect: `customer → REN-01`, `admin → ADM-01`, **`mitra`:** belum punya `mitra_profiles` → MTR-01; `pending`/`rejected` → MTR-02; `approved` → MTR-03.
  - Setelah login dari halaman terproteksi → kembali ke halaman asal (intended URL).
  - Rate limiting (NFR-REC-001). Halaman login/register tidak boleh diakses saat sudah login (PRD §12.6).
  - AC: AUTH-003-01…03. Test: tiap peran/status mitra diarahkan benar; banned ditolak.
  - *Jika REN-01 / ADM-01 belum ada, redirect ke placeholder sederhana dan catat TODO untuk pemilik modulnya.*
- [x] **T1.4 Lupa & reset password** (FR-AUTH-004, PUB-06)
  - Respons netral untuk email tidak terdaftar (cegah enumerasi). Token kedaluwarsa → galat. Password baru ikuti aturan T1.1.
  - AC: AUTH-004-01/02. Test: token valid ubah password, token invalid password tidak berubah.
- [x] **T1.5 Logout** (FR-AUTH-005)
  - Cabut sesi, arahkan ke login. AC: AUTH-005-01.
- [x] **T1.6 Penanganan sesi kedaluwarsa** (EC-23): arahkan ke login dengan pesan *"Sesi berakhir, silakan login kembali."* lalu kembali ke halaman asal.
- [x] **T1.7 Test keamanan auth:** rate limit, hash password, kepemilikan akses lintas peran (mitra tidak bisa membuka `/admin` atau halaman `REN-*`).

### FASE 2 — Onboarding & Verifikasi Mitra — **M**

Rujukan: FR-MITRA-001/002/004, UJ-04, BR-002/004/005, MTR-01/02/12.

- [x] **T2.1 Pendaftaran Mitra MTR-01** (FR-MITRA-001)
  - Form data usaha + `no_ktp` (wajib, ≤20) + `npwp` (≤24; wajib/opsional menunggu keputusan, default ikuti F08 "jika ada" → opsional) + unggah dokumen KTP, NPWP/SIUP.
  - Validasi berkas: tipe & ukuran dari config (default: JPG/PNG/PDF, maks 2 MB — asumsi). Progres unggah, galat per berkas (EC-20).
  - Simpan `mitra_profiles.status = pending`, `user_id` = user login. Satu profil per user (tambah guard unik walau ERD tidak UNIQUE).
  - Berkas di **disk privat**, nama acak, diakses lewat route terotorisasi (pemilik & admin).
  - Setelah sukses → MTR-02. (Notifikasi ke Admin = Usulan, T10.x.)
  - AC: MITRA-001-01…03. Test: `no_ktp` kosong ditolak; mitra `pending` tidak bisa membuat lapangan.
- [x] **T2.2 Status Verifikasi MTR-02** (FR-MITRA-002)
  - Tampilkan status `pending` (badge Info "verifikasi berlangsung"), `approved`, `rejected` + `alasan_penolakan`.
  - `rejected` → tombol **Edit & Ajukan Ulang**. `approved` → tombol ke Dashboard.
  - AC: MITRA-002-01.
- [x] **T2.3 Edit data & ajukan ulang MTR-12** (FR-MITRA-004)
  - Edit data/dokumen. Jika `rejected` dan mitra mengajukan ulang → `status = pending`; pengajuan muncul lagi di antrian Admin.
  - Perilaku `alasan_penolakan` lama setelah ajukan ulang & edit data legalitas oleh mitra `approved` = **Perlu Konfirmasi** (OQ-009) → default: pertahankan alasan lama sebagai riwayat/di-null-kan (pilih satu, catat DECISIONS); edit oleh `approved` **tidak** memicu verifikasi ulang (asumsi).
  - AC: MITRA-004-01.
- [x] **T2.4 Gerbang akses mitra** — middleware `EnsureMitraApproved` terpasang di seluruh MTR-03…MTR-11; redirect + pesan EC-17. Test untuk `pending`, `rejected`, `approved`.
- [x] **T2.5 Audit log** perubahan status verifikasi mitra yang berasal dari sisi mitra (ajukan ulang) (NFR-LOG-001, BR-028).

### FASE 3 — Dashboard Mitra (MTR-03) — **S** (kerangka dulu, metrik menyusul)

Rujukan: FR-REPORT-001, BR-016/BR-023.

- [x] **T3.1 Kerangka dashboard** dengan navigasi ke semua modul Mitra, kartu metrik memakai `text-metric`.
- [x] **T3.2 Kartu "Booking hari ini"** = jumlah `booking_details` pada lapangan milik mitra untuk tanggal hari ini. AC: REPORT-001-01 (3 booking → tampil 3). Empty state → 0.
- [x] **T3.3 Kartu "Ringkasan pendapatan"** — definisi kotor/bersih **Perlu Konfirmasi**; default: tampilkan pendapatan bersih bila komisi sudah ada, selain itu kotor dengan label jelas. Hubungkan ke T9.x.
- [x] **T3.4 Kartu "Rating rata-rata"** — **BLOCKED** oleh GAP-01/OQ-019 (tabel ulasan belum ada). Tampilkan placeholder "Belum ada ulasan".
- [x] **T3.5 Pintasan tindakan:** pesanan menunggu verifikasi transfer, booking siap diselesaikan, venue/lapangan ditolak (tampilkan alasan).

### FASE 4 — Venue (MTR-04, MTR-05b) — **M**

Rujukan: FR-VENUE-001…004, UJ-05, BR-023/BR-025.

- [ ] **T4.1 Daftar venue** milik mitra + status badge + CTA "Tambah Venue"; empty state *"Belum ada venue"* + CTA.
- [ ] **T4.2 Buat venue (draft)** (FR-VENUE-001): `city_id` (kota `aktif`), `nama_venue` (≤200, wajib), `alamat_lengkap` (wajib), `map_url` (opsional ≤255), `jam_buka`, `jam_tutup` (wajib), deskripsi. `mitra_id` = profil mitra login (**jangan** dari input). Validasi jam tutup > jam buka (lintas tengah malam = Perlu Konfirmasi; default tolak). AC: VENUE-001-01/02.
- [ ] **T4.3 Ajukan venue** (FR-VENUE-002): validasi kelengkapan; status asal ∈ {`draft`,`rejected`} → `pending_review`; tampilkan daftar field yang kurang. AC: VENUE-002-01.
- [ ] **T4.4 Edit venue** (FR-VENUE-003) + Policy. Edit venue `aktif` kembali ke moderasi atau tidak = OQ-011 → default: **tidak** memicu moderasi ulang (konfigurabel). Tampilkan `alasan_penolakan` bila `rejected`. AC: VENUE-003-01 (mitra A tidak bisa mengedit venue mitra B).
- [ ] **T4.5 Nonaktifkan venue** (FR-VENUE-004, **S**) dengan **dialog konfirmasi**. Cascade ke lapangan & perlakuan booking `confirmed` mendatang = OQ-012 → default: **tolak penonaktifan bila ada booking mendatang berstatus aktif**, tampilkan pesan. AC: VENUE-004-01.

### FASE 5 — Lapangan (MTR-04, MTR-05) — **M**

Rujukan: FR-FIELD-001…004, BR-002/003/031.

- [ ] **T5.1 Daftar lapangan per venue** + status + aksi (edit, ajukan, nonaktifkan).
- [ ] **T5.2 Buat lapangan (draft)** (FR-FIELD-001): pilih venue milik sendiri; `nama_lapangan` (≤100), `sport_id` (olahraga `aktif`), `harga_perjam` (numerik > 0), fasilitas (`aktif`, multi-pilih → `field_facilities`), deskripsi.
  - **Guard BR-002:** mitra bukan `approved` → ditolak (AC FIELD-001-02). `status_lapangan` default `draft` (AC FIELD-001-03: tidak tampil publik).
- [ ] **T5.3 Unggah foto lapangan:** multi-foto, tandai **foto utama** (`field_photo.is_primary`; wajib tepat satu = Perlu Konfirmasi → default: otomatis foto pertama jadi utama bila belum ada). Batas jumlah/format/ukuran dari config. Hapus/ganti foto. Simpan draft tanpa foto diperbolehkan, tetapi **ajukan** mensyaratkan ≥1 foto (asumsi).
- [ ] **T5.4 Ajukan lapangan** (FR-FIELD-002): syarat data lengkap + jadwal (T6.1) sudah diatur (Usulan). Apakah venue harus `aktif` dulu = OQ-010 → default: **venue harus `aktif`**. Status `draft`/`rejected` → `pending_review`. AC: FIELD-002-01.
- [ ] **T5.5 Edit lapangan** (FR-FIELD-003): sinkronkan `field_facilities` & `field_photo`; perubahan harga **tidak** memengaruhi booking lama (`harga_slot` tersimpan). AC: FIELD-003-01. Tampilkan `alasan_penolakan` bila `rejected`.
- [ ] **T5.6 Nonaktifkan lapangan** (FR-FIELD-004, **S**) + dialog konfirmasi + aturan booking mendatang seperti T4.5. AC: FIELD-004-01.

### FASE 6 — Jadwal Operasional & Blokir (MTR-06) — **M**

Rujukan: FR-SCHED-001/002, BR-020/BR-021.

- [ ] **T6.1 Jadwal mingguan per lapangan** (FR-SCHED-001): per hari → `status_hari` (`buka`/`tutup`), `jam_buka`, `jam_tutup`, `durasi` slot (TIME). Validasi jam tutup > jam buka dan **berada dalam jam venue** (Usulan KS-14). Fitur "salin ke semua hari". Pemetaan angka `hari` didokumentasikan (default ISO: 1=Senin … 7=Minggu; catat DECISIONS). AC: SCHED-001-01 (08:00–22:00, durasi 01:00 → 14 slot), SCHED-001-02 (hari tutup → tidak ada slot).
- [ ] **T6.2 Service pembentuk slot** `SlotGenerator` (dipakai juga modul Penyewa): input lapangan + tanggal → daftar slot dari `field_schedule` dikurangi `field_blocks` (dan booking aktif, bila modul booking sudah ada). **Beri unit test; ini fondasi kalender real-time.** Koordinasikan kontraknya dengan pemilik modul Booking.
- [ ] **T6.3 Blokir tanggal/jam** (FR-SCHED-002): `tanggal_mulai`, `tanggal_selesai`, `jam_mulai`, `jam_selesai`, `alasan_pemblokiran` (opsional ≤255). Validasi akhir ≥ awal. Tambah/ubah/hapus blokir. Interpretasi rentang (harian berulang vs kontinu) = KS-15 → default: **tiap hari dalam rentang tanggal, jam_mulai–jam_selesai** (catat DECISIONS). AC: SCHED-002-01.
- [ ] **T6.4 Konflik blokir vs booking `confirmed`** (OQ-013): default **tolak blokir** yang menabrak booking aktif dan tampilkan daftar booking yang bentrok sebagai peringatan.
- [ ] **T6.5 Tampilan "Blocked/Maintenance"** konsisten dengan state kalender `desain.md` §10 (pola diagonal, bukan hanya warna).

### FASE 7 — Harga Peak / Off-Peak (MTR-07) — **S**

Rujukan: FR-FIELD-006, BR-008, GAP-21/OQ-005.

- [ ] **T7.1 Definisikan skema `meta_harga`** (JSON). Asumsi default (catat DECISIONS, ubah bila OQ-005 diputuskan):
  ```json
  {
    "harga_off_peak": 100000,
    "harga_peak": 150000,
    "peak_rules": [ { "hari": [6, 7], "jam_mulai": "00:00", "jam_selesai": "24:00" } ]
  }
  ```
  Tanpa `meta_harga` → `harga_perjam` berlaku untuk semua slot.
- [ ] **T7.2 Form MTR-07** + validasi: harga tidak negatif, periode peak tidak tumpang-tindih (galat bila tumpang-tindih), struktur JSON valid.
- [ ] **T7.3 Service `PriceCalculator`** (dipakai modul Booking): input lapangan + slot → harga per jam. Unit test AC FIELD-006-01 (slot Sabtu memakai harga peak). Koordinasikan kontrak dengan modul Booking.

### FASE 8 — Pesanan Mitra (MTR-08, 09, 10) — **M**

Rujukan: FR-ORDER-001…003, FR-PAY-003, UJ-06, BR-009/010/013/014/015.

> Fase ini bergantung pada data booking dari modul Penyewa. Gunakan seeder untuk pengembangan.

- [ ] **T8.1 Daftar & kalender pesanan masuk** (FR-ORDER-001): query `booking_details` → `fields` → `venues.mitra_id = mitra login`. Filter lapangan/tanggal/status. Kalender per lapangan; klik → MTR-09. Empty state. Paparan data pribadi penyewa (nama/no HP) dibatasi seperlunya (NFR-PRIV-001). AC: ORDER-001-01 (tidak ada booking mitra lain).
- [ ] **T8.2 Detail pesanan MTR-09:** data booking, penyewa, slot, harga, status pembayaran, bukti transfer (bila ada).
- [ ] **T8.3 Verifikasi transfer manual** (FR-PAY-003) — **BLOCKED oleh GAP-02, OQ-017, OQ-027, OQ-016.**
  Setelah diputuskan & migration disetujui: tombol **Setujui** → booking `confirmed` + notifikasi penyewa; **Tolak** wajib `catatan_penolakan` (dialog). Hanya untuk lapangan milik mitra (AC PAY-003-02). Catat audit.
  Sementara BLOCKED: siapkan UI & Policy, tombol disembunyikan di balik feature flag.
- [ ] **T8.4 Konfirmasi kehadiran & `no_show`** (FR-ORDER-002, **S**): `no_show` hanya untuk booking `confirmed` pada hari itu **setelah jam mulai** (sebelum itu ditolak). Hadir → `ongoing` (pemetaan Usulan). Check-in/QR = Perlu Konfirmasi (GAP-25). AC: ORDER-002-01.
- [ ] **T8.5 Selesaikan per transaksi** (FR-ORDER-003): hanya jika `jam_selesai` sudah lewat dan booking terbayar → `completed`. AC: ORDER-003-02 (sebelum jam berakhir ditolak).
- [ ] **T8.6 Bulk completion MTR-10:** pilih banyak (checkbox) → **dialog konfirmasi** → item valid diproses, item tidak valid dilaporkan dalam ringkasan (EC-24). Semua item harus milik mitra. AC: ORDER-003-01 (5 booking → 5 `completed`). Gunakan transaksi DB per item agar sebagian gagal tidak menggagalkan semuanya.
- [ ] **T8.7 Audit log** perubahan status pesanan/pembayaran dari sisi mitra (NFR-LOG-001).
- [ ] **T8.8 Concurrency:** dua aksi bersamaan pada pesanan yang sama → aksi pertama berlaku, aksi kedua mendapat pesan "sudah diproses" (gunakan row lock / pengecekan status dalam transaksi).

### FASE 9 — Laporan Pendapatan (MTR-11) — **M**

Rujukan: FR-REPORT-002, BR-016/BR-017, UJ-10.

- [ ] **T9.1 Laporan & filter:** harian/bulanan (+ filter per lapangan), jumlah booking, tabel transaksi, angka rata kanan `tabular-nums`. Validasi rentang tanggal. Empty state.
- [ ] **T9.2 Komisi** — **BLOCKED oleh OQ-004/GAP-07.** Buat `CommissionResolver` dengan satu antarmuka; implementasi awal membaca satu tarif dari config (default 0 dan **ditampilkan jelas** sebagai belum ditetapkan). Jangan hardcode rumus tersebar.
- [ ] **T9.3 Perhitungan:** pendapatan kotor (`booking_details.harga_slot` untuk booking `completed` & terbayar) − komisi = bersih. AC: REPORT-002-01 (bersih = kotor − komisi).
- [ ] **T9.4 Ekspor laporan:** format default **CSV** (Perlu Konfirmasi; mudah ditambah XLSX/PDF). Galat ekspor → pesan jelas (EC).
- [ ] **T9.5 Wallet & penarikan mitra (FR-FIN-003)** — **BLOCKED oleh OQ-006.** Jangan dibangun sebelum diputuskan in/out scope.

### FASE 10 — Notifikasi, Polish, dan Penutupan — **S**

- [ ] **T10.1 Notifikasi sisi mitra** (FR-NOTIF-001, BR-027): status verifikasi mitra & moderasi venue/lapangan (disetujui/ditolak + alasan), booking baru. Pakai Laravel Notifications (database + mail); kanal WA/push = OQ-021. Kegagalan kirim **tidak** membatalkan transaksi utama (retry via queue). Halaman notifikasi mitra tidak ada di daftar halaman PRD → bangun sederhana atau lewati sampai dikonfirmasi.
- [ ] **T10.2 State UI lengkap** untuk semua halaman: loading, empty, error, success (toast), dialog konfirmasi untuk aksi destruktif.
- [ ] **T10.3 Responsif & aksesibilitas:** uji viewport <640 / 640–1024 / >1024; target tap ≥44px; kontras WCAG AA; tidak bergantung warna saja (NFR-A11Y-001…003).
- [ ] **T10.4 Performa:** index untuk `booking_details(field_id, tanggal_sewa)`, `fields.sport_id`, `venues.city_id` (NFR-REC-006). Hindari N+1 (eager loading) di daftar & laporan.
- [ ] **T10.5 Ulasan untuk rating dashboard** — selesaikan T3.4 setelah GAP-01 diputuskan.
- [ ] **T10.6 Review akhir:** jalankan seluruh test, cek matriks akses §12.4–12.6, perbarui `docs/DECISIONS.md`, dokumentasikan cara menjalankan seeder & command dev di `README`.

---

## 8. Urutan Eksekusi yang Disarankan

```
FASE 0 → FASE 1 → FASE 2 → (FASE 3 kerangka) → FASE 4 → FASE 5 → FASE 6 → FASE 7
        → FASE 8 (kecuali T8.3) → FASE 9 (kecuali T9.2/T9.5) → FASE 10
```

Alasan (PRD §20.3): fondasi & RBAC dulu → kemitraan & listing → jadwal/harga → operasional → laporan.
Task `BLOCKED` dikerjakan paling akhir setelah keputusan dikonfirmasi pemilik produk.

---

## 9. Open Questions dan Asumsi Default

Gunakan asumsi ini sampai ada keputusan resmi. Semuanya harus **mudah diubah** (config/konstanta) dan tercatat di `docs/DECISIONS.md`.

| OQ | Topik | Asumsi default saya |
|---|---|---|
| OQ-007 | JWT vs sesi | Sesi web Laravel + Blade; tidak memakai JWT |
| OQ-008 | Alur registrasi mitra | Dua tahap: buat akun (+OTP) → isi MTR-01. Satu akun = satu peran |
| OQ-009 | Edit data mitra `approved` | Tidak memicu verifikasi ulang |
| OQ-010 | Urutan venue → lapangan | Venue harus `aktif` sebelum lapangan boleh diajukan |
| OQ-011 | Edit listing `aktif` | Tidak memicu moderasi ulang (konfigurabel) |
| OQ-012 | Nonaktifkan dengan booking mendatang | Ditolak selama masih ada booking aktif mendatang |
| OQ-013 | Blokir vs booking `confirmed` | Blokir ditolak bila bentrok |
| OQ-005 | Harga peak/off-peak | Skema JSON pada T7.1 |
| OQ-004 | Komisi | Satu tarif dari config, default 0, ditampilkan jelas |
| OQ-006 | Wallet mitra | Tidak dibangun |
| OQ-016 | Status booking final | Acuan `desain.md` Bagian I §9 |
| OQ-017/027 | Transfer manual | Fitur BLOCKED; UI disiapkan di belakang feature flag |
| OQ-021 | Kanal notifikasi | Database + email saja |
| OQ-023 | Zona waktu | Satu zona waktu aplikasi |
| OQ-024 | Ejaan `succes`, `complated_at` | Dipertahankan; dipetakan di model/accessor |
| — | Format/ukuran berkas | JPG/PNG/PDF, maks 2 MB (config) |
| — | OTP | 6 digit, 10 menit, 5 percobaan (config) |

Bila ada keputusan baru dari pemilik produk, **perbarui tabel ini dan DECISIONS.md lebih dulu**, baru ubah kode.

---

## 10. Definition of Done (per task)

- [ ] Perilaku sesuai FR dan **semua AC** yang dirujuk lulus (Feature test tertulis & hijau).
- [ ] Otorisasi: Policy + test akses lintas mitra (403) dan lintas peran.
- [ ] Validasi via Form Request, pesan galat Bahasa Indonesia per field.
- [ ] State UI lengkap (loading/empty/error/success) dan dialog konfirmasi untuk aksi destruktif.
- [ ] Mematuhi token `desain.md`; responsif mobile-first; lolos cek aksesibilitas dasar.
- [ ] Migration reversible (`down()` berfungsi) dan menyebut GAP-xx.
- [ ] Perubahan status penting tercatat di `audit_log`.
- [ ] Keputusan/asumsi baru tercatat di `docs/DECISIONS.md`.
- [ ] Tidak ada perubahan di luar lingkup Mitra tanpa koordinasi.

---

## 11. Perintah Umum (sesuaikan dengan repo)

```bash
php artisan migrate:fresh --seed     # reset DB dev + seeder
php artisan mitra:approve {email}    # setujui mitra (dev saja)
php artisan test --filter=Mitra      # jalankan test modul Mitra
php artisan queue:work               # job notifikasi / OTP
php artisan schedule:work            # scheduler (dev)
```

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
