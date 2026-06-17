# Dokumentasi Proyek PC Lab Inventory System

## Identitas Pengembang
* **Nama:** Gamma Assyafi Fadhillah Ar Rasyad
* **NIM:** L0125013
* **Kelas/Prodi:** A/S1 Informatika 2025

---

## Link Deliverables
* **Repository Mirror GitHub:** [justhenix/crud-app](https://github.com/justhenix/crud-app.git)
* **Aplikasi Live:** [crud.henix.my.id](https://crud.henix.my.id)

---

## Arsitektur Sistem & Stack

Berikut adalah komponen teknologi yang digunakan dalam sistem:

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Framework Utama** | Laravel TALL Stack | Laravel, Livewire, Tailwind CSS, Alpine.js |
| **Runtime & Bundler** | Bun | Pengganti Node.js untuk asset bundling cepat |
| **Database** | PostgreSQL 16 | Penyimpanan data relasional utama |
| **Kontainerisasi** | Docker & Compose | Standardisasi lingkungan dev & production |
| **Repository & CI/CD** | GitLab (Local) | Manajemen source code dan pipeline deploy otomatis |
| **Mirroring** | GitHub | Mirror repository untuk publikasi kode |
| **Infrastruktur VPS** | Azure VM VPS | Virtual Private Server berbasis Ubuntu/Debian |
| **Sertifikat SSL** | Let's Encrypt | Keamanan koneksi HTTPS aplikasi live |

---

## Dashboard GitLab Lokal

![Image 1 (Dashboard GitLab)](https://crimson-famous-silkworm-794.mypinata.cloud/ipfs/bafkreievlevx4mlkkvhigp2mn2ci2zioygkwwgd6pc67gmrmr5azf2d2oq)

---

## Konfigurasi CI/CD (.gitlab-ci.yml)

Pipeline CI/CD dibagi menjadi dua tahap (*stages*) utama:

### 1. Stage Test
* **Tujuan:** Memvalidasi kualitas kode sebelum masuk ke tahap deploy.
* **Proses:**
  * Menggunakan docker image `php:8.4-alpine`.
  * Menginstal modul php yang dibutuhkan (PDO, SQLite, MBString, dll).
  * Mengunduh dependency project via Composer.
  * Menjalankan unit & feature testing (`php artisan test`).

### 2. Stage Deploy
* **Tujuan:** Mengirimkan kode terbaru ke VPS Azure secara otomatis.
* **Syarat:** Hanya berjalan pada *branch* `main`.
* **Proses:**
  * Menggunakan docker image `alpine:latest`.
  * Menyiapkan SSH key untuk koneksi aman ke VPS.
  * Sinkronisasi file kode via `rsync` (mengecualikan `.git`, `node_modules`, dll).
  * Membangun ulang kontainer Docker di VPS via Docker Compose.
  * Melakukan migrasi database otomatis.
  * Melakukan optimalisasi performa Laravel (cache config, route, view, dan event).

---

## Repository GitHub Mirror

![Image 2 (Repository GitHub Mirror)](https://crimson-famous-silkworm-794.mypinata.cloud/ipfs/bafkreigpv5o44zpkwojrk2gn7d6mikasqjryryobkc6bw2yznwpdrtxqwq)

---

## Aplikasi Live

![Image 3 (Aplikasi Live Ber-SSL)](https://crimson-famous-silkworm-794.mypinata.cloud/ipfs/bafkreia2gix5fugpsbdcqwwzt6mmzwt5drlx6shrfelwsrkxapglz2hyne)
