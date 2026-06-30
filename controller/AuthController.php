<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/User.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    public function login($username, $password)
    {
        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['login_time'] = time();
            return ['success' => true, 'message' => 'Login berhasil'];
        }
        return ['success' => false, 'message' => 'Username atau password salah'];
    }

    public function register($nama, $username, $password, $confirmPassword)
    {
        
        if (empty($nama) || empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Semua field wajib diisi'];
        }
        if (strlen($username) < 4) {
            return ['success' => false, 'message' => 'Username minimal 4 karakter'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }
        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Konfirmasi password tidak cocok'];
        }
        if ($this->userModel->findByUsername($username)) {
            return ['success' => false, 'message' => 'Username sudah digunakan'];
        }

        if ($this->userModel->register($nama, $username, $password)) {
            return ['success' => true, 'message' => 'Registrasi berhasil, silakan login'];
        }
        return ['success' => false, 'message' => 'Registrasi gagal, coba lagi'];
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function checkSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php?msg=session_expired");
            exit();
        }
    }
}
