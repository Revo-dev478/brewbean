-- =====================================================
-- UPDATE SCHEMA: Tambah Kolom Delivery Tracking
-- =====================================================
-- Script ini menambahkan kolom delivery_status dan delivery_confirmed_at
-- ke tabel transaksi_midtrans untuk tracking pengiriman pesanan

-- Tambah kolom delivery_status
ALTER TABLE transaksi_midtrans 
ADD COLUMN delivery_status VARCHAR(50) DEFAULT 'processing' AFTER settlement_time;

-- Tambah kolom delivery_confirmed_at
ALTER TABLE transaksi_midtrans 
ADD COLUMN delivery_confirmed_at DATETIME NULL AFTER delivery_status;

-- Update existing records: set status ke 'processing' untuk transaksi yang sudah settlement
UPDATE transaksi_midtrans 
SET delivery_status = 'processing' 
WHERE transaction_status IN ('settlement', 'capture') AND delivery_status IS NULL;

-- Optional: Set beberapa sample data untuk testing
-- UPDATE transaksi_midtrans 
-- SET delivery_status = 'shipped' 
-- WHERE transaction_status = 'settlement' 
-- ORDER BY transaction_time DESC 
-- LIMIT 2;

-- UPDATE transaksi_midtrans 
-- SET delivery_status = 'delivered' 
-- WHERE transaction_status = 'settlement' 
-- ORDER BY transaction_time DESC 
-- LIMIT 1;

-- Cek hasil
SELECT order_id, transaction_status, delivery_status, delivery_confirmed_at 
FROM transaksi_midtrans 
ORDER BY transaction_time DESC 
LIMIT 10;
