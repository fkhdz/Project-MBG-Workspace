CREATE DATABASE IF NOT EXISTS db_mbg;
USE db_mbg;

-- ============================
-- TABLE: USER
-- ============================
CREATE TABLE USER (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    no_hp VARCHAR(20),
    tanggal_daftar DATE,
    last_login DATE,
    status_akun VARCHAR(50)
);

-- ============================
-- TABLE: PENERIMA
-- ============================
CREATE TABLE PENERIMA (
    penerima_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama_lengkap VARCHAR(100) NOT NULL,
    tanggal_lahir DATE,
    jenis_kelamin VARCHAR(20),
    alamat VARCHAR(255),
    kecamatan VARCHAR(100),
    kelurahan VARCHAR(100),
    kategori_penerima VARCHAR(100),
    penghasilan_bulanan INT,
    jumlah_tanggungan INT,
    status_validasi VARCHAR(50),
    tanggal_validasi DATE,
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

-- ============================
-- TABLE: MITRA
-- ============================
CREATE TABLE MITRA (
    mitra_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama_mitra VARCHAR(100) NOT NULL,
    jenis_mitra VARCHAR(100),
    kontak_person VARCHAR(100),
    no_hp VARCHAR(20),
    alamat_mitra VARCHAR(255),
    wilayah_operasional VARCHAR(150),
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

-- ============================
-- TABLE: PAKETBANTUAN
-- ============================
CREATE TABLE PAKETBANTUAN (
    paket_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_paket VARCHAR(100) NOT NULL,
    deskripsi VARCHAR(255),
    jenis_bantuan VARCHAR(100),
    kalori_total INT,
    berat_total INT,
    kadaluarsa DATE,
    kuantitas INT
);

-- ============================
-- TABLE: DISTRIBUSI
-- ============================
CREATE TABLE DISTRIBUSI (
    distribusi_id INT AUTO_INCREMENT PRIMARY KEY,
    paket_id INT,
    penerima_id INT,
    mitra_id INT,
    tanggal_kirim DATE,
    tanggal_terima DATE,
    lokasi_pengiriman VARCHAR(255),
    status_pengiriman VARCHAR(100),
    bukti_pengiriman VARCHAR(255),
    catatan_petugas VARCHAR(255),
    FOREIGN KEY (paket_id) REFERENCES PAKETBANTUAN(paket_id),
    FOREIGN KEY (penerima_id) REFERENCES PENERIMA(penerima_id),
    FOREIGN KEY (mitra_id) REFERENCES MITRA(mitra_id)
);

-- ============================
-- TABLE: LAPORANDATA
-- ============================
CREATE TABLE LAPORANDATA (
    laporan_id INT AUTO_INCREMENT PRIMARY KEY,
    distribusi_id INT,
    tanggal_laporan DATE,
    status_laporan VARCHAR(100),
    jumlah_paket_dikirim INT,
    jumlah_paket_diterima INT,
    catatan VARCHAR(255),
    dibuat_oleh INT,
    FOREIGN KEY (distribusi_id) REFERENCES DISTRIBUSI(distribusi_id),
    FOREIGN KEY (dibuat_oleh) REFERENCES USER(user_id)
);

-- ============================
-- TABLE: ITEM (Gudang)
-- ============================
CREATE TABLE ITEM (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_item VARCHAR(100) NOT NULL,
    satuan VARCHAR(50),
    stok_gudang INT DEFAULT 0
);

-- ============================
-- TABLE: DETAIL_PAKET (Komposisi)
-- ============================
CREATE TABLE DETAIL_PAKET (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    paket_id INT,
    item_id INT,
    jumlah_per_paket INT,
    FOREIGN KEY (paket_id) REFERENCES PAKETBANTUAN(paket_id),
    FOREIGN KEY (item_id) REFERENCES ITEM(item_id)
);
