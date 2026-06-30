<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Alumni.php';

class AlumniController
{
    private $alumniModel;
    private $uploadDir = __DIR__ . '/../public/uploads/';

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->alumniModel = new Alumni($db);
    }

    public function getData($keyword = '', $status = '')
    {
        if (!empty($keyword) || !empty($status)) {
            return $this->alumniModel->search($keyword, $status);
        }
        return $this->alumniModel->getAll();
    }

    public function getById($id)
    {
        return $this->alumniModel->getById($id);
    }

    private function validate($data, $id = null)
    {
        $errors = [];

        if (empty(trim($data['nim']))) {
            $errors[] = "NIM wajib diisi";
        } elseif (!preg_match('/^[0-9]{5,20}$/', $data['nim'])) {
            $errors[] = "NIM harus berupa angka (5-20 digit)";
        } elseif ($this->alumniModel->isNimExists($data['nim'], $id)) {
            $errors[] = "NIM sudah terdaftar, gunakan NIM lain";
        }

        if (empty(trim($data['nama_lengkap']))) {
            $errors[] = "Nama lengkap wajib diisi";
        }

        if (empty(trim($data['jurusan']))) {
            $errors[] = "Jurusan wajib diisi";
        }

        if (empty($data['tahun_lulus']) || !is_numeric($data['tahun_lulus']) ||
            $data['tahun_lulus'] < 2000 || $data['tahun_lulus'] > (date('Y') + 1)) {
            $errors[] = "Tahun lulus tidak valid";
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        }

        if (empty($data['no_telepon']) || !preg_match('/^[0-9+\-\s]{8,20}$/', $data['no_telepon'])) {
            $errors[] = "Nomor telepon tidak valid";
        }

        return $errors;
    }

    private function handleUpload($file)
    {
        if (empty($file['name'])) {
            return ['success' => true, 'filename' => null]; 
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; 

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Terjadi kesalahan saat upload file'];
        }
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format file harus JPG/JPEG/PNG'];
        }
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 2MB'];
        }

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'alumni_' . time() . '_' . uniqid() . '.' . $ext;
        $target = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => true, 'filename' => $filename];
        }
        return ['success' => false, 'message' => 'Gagal menyimpan file'];
    }

    public function store($data, $file)
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $uploadResult = $this->handleUpload($file);
        if (!$uploadResult['success']) {
            return ['success' => false, 'errors' => [$uploadResult['message']]];
        }

        $data['foto'] = $uploadResult['filename'];

        if ($this->alumniModel->create($data)) {
            return ['success' => true, 'message' => 'Data alumni berhasil ditambahkan'];
        }
        return ['success' => false, 'errors' => ['Gagal menyimpan data ke database']];
    }

    public function updateData($id, $data, $file)
    {
        $errors = $this->validate($data, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $uploadResult = $this->handleUpload($file);
        if (!$uploadResult['success']) {
            return ['success' => false, 'errors' => [$uploadResult['message']]];
        }
        $data['foto'] = $uploadResult['filename'];

        if ($this->alumniModel->update($id, $data)) {
            return ['success' => true, 'message' => 'Data alumni berhasil diperbarui'];
        }
        return ['success' => false, 'errors' => ['Gagal memperbarui data']];
    }

    public function destroy($id)
    {
        $alumni = $this->alumniModel->getById($id);
        if ($alumni && !empty($alumni['foto'])) {
            $fotoPath = $this->uploadDir . $alumni['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }
        return $this->alumniModel->delete($id);
    }

    public function getStats()
    {
        return [
            'total' => $this->alumniModel->countAll(),
            'bekerja' => $this->alumniModel->countByStatus('Bekerja'),
            'belum_bekerja' => $this->alumniModel->countByStatus('Belum Bekerja'),
            'wirausaha' => $this->alumniModel->countByStatus('Wirausaha'),
            'studi_lanjut' => $this->alumniModel->countByStatus('Studi Lanjut'),
        ];
    }
}
