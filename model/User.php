<?php

require_once __DIR__ . '/Model.php';

class User extends Model
{
    protected $table = "users";

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($nama, $username, $password)
    {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO {$this->table} (nama_lengkap, username, password) VALUES (:nama, :username, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed);
        return $stmt->execute();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT id, nama_lengkap, username, created_at FROM {$this->table}");
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
        return $this->register($data['nama_lengkap'], $data['username'], $data['password']);
    }

    public function update($id, $data)
    {
        
        return false;
    }

    public function delete($id)
    {
        
        return false;
    }
}
