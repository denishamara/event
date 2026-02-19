# Panduan Phase Rollover Otomatis

## Fitur Baru: Rollover Phase Berdasarkan Waktu

Sistem sekarang dapat **otomatis memindahkan phase** ketika waktu H-X hari sudah tercapai, meskipun kuota belum habis. Sisa kuota akan dilimpahkan ke phase berikutnya dengan harga yang sama dengan phase tersebut.

## Cara Kerja

### 1. **Pengecekan Waktu Phase**
- Setiap phase memiliki `start_day` dan `end_day` (dalam format H-X hari sebelum event)
- Contoh: Early Bird = H-60 sampai H-30
- Ketika hari ini sudah melewati `end_day` (misalnya sudah H-29), maka phase Early Bird dianggap expired

### 2. **Transfer Quota Otomatis**
- Jika phase sudah expired tetapi masih ada `quota_remaining`, quota akan ditransfer ke phase berikutnya
- Quota yang ditransfer akan ditambahkan ke `quota_total` dan `quota_remaining` phase berikutnya
- **Harga mengikuti phase berikutnya** (otomatis)

### 3. **Contoh Skenario**

**Event: Konser Musik**
- Event Date: 15 Maret 2026
- Hari ini: 3 Februari 2026 (H-40)

**Phase Setup:**
- Early Bird: H-60 s/d H-30 (Rp 150.000, quota 100)
- Presale: H-29 s/d H-14 (Rp 200.000, quota 150)
- Last Minute: H-13 s/d H-0 (Rp 300.000, quota 250)

**Yang Terjadi:**
1. Pada H-40 → Early Bird masih aktif, harga Rp 150.000
2. Pada H-30 → Misalnya masih ada 30 tiket Early Bird tersisa
3. Pada H-29 → Sistem rollover akan:
   - Transfer 30 tiket dari Early Bird ke Presale
   - Presale quota menjadi: 150 + 30 = 180 tiket
   - **Harga 30 tiket tersebut menjadi Rp 200.000** (ikut harga Presale)
   - Early Bird quota_remaining = 0

## Cara Menjalankan

### **Opsi 1: Manual (untuk testing)**
Akses URL berikut:
```
http://localhost/event/public/index.php/cron/price/rollover
```

### **Opsi 2: Cron Job (Otomatis - Recommended)**

#### Windows Task Scheduler:
1. Buka Task Scheduler
2. Create Basic Task
3. Set trigger: Daily, jam 00:01
4. Action: Start a program
5. Program: `C:\xampp\php\php.exe`
6. Arguments: `-f "C:\xampp\htdocs\event\public\index.php" cron/price/rollover`

#### Linux Cron:
```bash
# Edit crontab
crontab -e

# Tambahkan baris ini (jalan setiap hari jam 00:01)
1 0 * * * /usr/bin/php /path/to/event/public/index.php cron/price/rollover
```

#### Menggunakan CURL (Alternative):
```bash
# Windows (Command Prompt)
curl http://localhost/event/public/index.php/cron/price/rollover

# Linux
curl http://yourdomain.com/index.php/cron/price/rollover
```

## Log & Monitoring

Setiap transfer quota akan dicatat di log file:
```
writable/logs/log-YYYY-MM-DD.log
```

Format log:
```
INFO - Phase rollover: Event ID 5, Phase "Early Bird" expired (H-29), transferred 30 quota to "Presale" at price 200.000
```

## Perbedaan Sebelum vs Sesudah

### ❌ **Sebelum:**
- Phase hanya berpindah ketika quota habis
- Jika Early Bird masih sisa 50 tiket di H-25, tetap dijual dengan harga Early Bird
- Presale tidak akan pernah aktif jika Early Bird masih ada quota

### ✅ **Sesudah:**
- Phase berpindah berdasarkan waktu ATAU quota (mana yang duluan)
- Jika Early Bird expired di H-30, sisa quota otomatis pindah ke Presale dengan harga Presale
- Sistem lebih adil dan sesuai dengan prinsip Early Bird (diskon untuk pembeli awal)

## Tips & Best Practices

1. **Jalankan cron setiap hari** (sekali sehari sudah cukup, misalnya jam 00:01)
2. **Monitor log file** untuk memastikan rollover berjalan
3. **Test dulu di development** sebelum production
4. **Backup database** sebelum enable fitur ini

## Troubleshooting

### Problem: Rollover tidak jalan otomatis
**Solusi:** 
- Pastikan cron job sudah di-setup
- Test manual via browser: `/cron/price/rollover`
- Cek log file untuk error

### Problem: Quota tidak bertambah di phase berikutnya
**Solusi:**
- Cek `order_index` di tabel `event_prices` sudah urut dari 1, 2, 3
- Pastikan semua phase memiliki `use_quota = 1`

### Problem: Harga tidak berubah setelah transfer
**Solusi:**
- Ini adalah behavior yang **benar**
- Quota yang ditransfer otomatis menggunakan harga phase tujuan
- Tidak perlu update harga manual

## Database Schema Reference

Tabel: `event_prices`
```sql
- id
- event_id
- phase (Early Bird, Presale, Last Minute)
- order_index (1, 2, 3 - untuk urutan phase)
- price
- start_day (contoh: 60 untuk H-60)
- end_day (contoh: 30 untuk H-30)
- use_quota (1 atau 0)
- quota_total
- quota_remaining
```

## Support

Jika ada pertanyaan atau issue, hubungi tim development.
