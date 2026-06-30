<?php

require_once __DIR__ . '/Model.php';

class Alumni extends Model
{
    protected $table = "alumni";

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($keyword = '', $status = '')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 
                (nim LIKE :keyword OR nama_lengkap LIKE :keyword OR jurusan LIKE :keyword OR email LIKE :keyword)";
        if (!empty($status)) {
            $sql .= " AND status = :status";
        }
        $sql .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);
        $kw = "%" . $keyword . "%";
        $stmt->bindParam(':keyword', $kw);
        if (!empty($status)) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (nim, nama_lengkap, jurusan, tahun_lulus, email, no_telepon, pekerjaan_saat_ini, alamat, foto, status) 
                VALUES 
                (:nim, :nama_lengkap, :jurusan, :tahun_lulus, :email, :no_telepon, :pekerjaan_saat_ini, :alamat, :foto, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nim', $data['nim']);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':jurusan', $data['jurusan']);
        $stmt->bindParam(':tahun_lulus', $data['tahun_lulus']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':no_telepon', $data['no_telepon']);
        $stmt->bindParam(':pekerjaan_saat_ini', $data['pekerjaan_saat_ini']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':foto', $data['foto']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                nim = :nim, nama_lengkap = :nama_lengkap, jurusan = :jurusan, tahun_lulus = :tahun_lulus,
                email = :email, no_telepon = :no_telepon, pekerjaan_saat_ini = :pekerjaan_saat_ini,
                alamat = :alamat, status = :status";
        if (!empty($data['foto'])) {
            $sql .= ", foto = :foto";
        }
        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nim', $data['nim']);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':jurusan', $data['jurusan']);
        $stmt->bindParam(':tahun_lulus', $data['tahun_lulus']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':no_telepon', $data['no_telepon']);
        $stmt->bindParam(':pekerjaan_saat_ini', $data['pekerjaan_saat_ini']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':status', $data['status']);
        if (!empty($data['foto'])) {
            $stmt->bindParam(':foto', $data['foto']);
        }
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function isNimExists($nim, $excludeId = null)
    {
        $sql = "SELECT id FROM {$this->table} WHERE nim = :nim";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nim', $nim);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function countAll()
    {
        return $this->conn->query("SELECT COUNT(*) AS total FROM {$this->table}")->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countByStatus($status)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE status = :status");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
