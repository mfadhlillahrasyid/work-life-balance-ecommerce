<?php
// app/Controllers/Admin/UserController.php

namespace App\Controllers\Admin;

class UserController
{
    public static function index(): void
    {
        admin_auth();

        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));

        // Base query — exclude soft deleted
        $sql = "SELECT id, nama_lengkap, role, email, slug_uuid, created_at
                   FROM users
                   WHERE deleted_at IS NULL";
        $params = [];

        // Search by name or email
        if ($search !== '') {
            $sql .= " AND (nama_lengkap LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $allUsers = $stmt->fetchAll();

        // Pagination
        $pagination = paginate($allUsers, 10, $page);

        view('admin/users/index', [
            'users' => $pagination['data'],
            'pagination' => $pagination['meta'],
            'search' => $search,
        ]);
    }

    public static function create(): void
    {
        admin_auth();

        view('admin/users/create');
    }

    public static function store(): void
    {
        admin_auth();

        $namaLengkap = trim($_POST['nama_lengkap'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $role = trim($_POST['role'] ?? 'admin');

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($namaLengkap === '') {
            $errors[] = 'Nama lengkap wajib diisi.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }

        if ($password !== $passwordConfirmation) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (!in_array($role, ['admin', 'manager', 'supervisor'], true)) {
            $errors[] = 'Role tidak valid.';
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/users/create');
        }

        // ── Cek email duplikat ────────────────────────────────────────────────
        $check = db()->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND deleted_at IS NULL");
        $check->execute([':email' => $email]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Email sudah digunakan.');
            redirect('/admin/users/create');
        }

        // ── Generate slug & slug_uuid ─────────────────────────────────────────
        $uuid = uuid_v4();
        $slug = slugify($namaLengkap);
        $slugUuid = $slug . '-' . $uuid;

        // ── Insert ────────────────────────────────────────────────────────────
        $stmt = db()->prepare("
            INSERT INTO users (nama_lengkap, role, slug, slug_uuid, email, password, created_at)
            VALUES (:nama_lengkap, :role, :slug, :slug_uuid, :email, :password, :created_at)
        ");

        $stmt->execute([
            ':nama_lengkap' => $namaLengkap,
            ':role' => $role,
            ':slug' => $slug,
            ':slug_uuid' => $slugUuid,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':created_at' => date('Y-m-d H:i:s'),
        ]);

        flash('admin_success', 'User berhasil ditambahkan.');
        redirect('/admin/users');
    }

    public static function edit(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            SELECT id, nama_lengkap, role, email, slug_uuid
            FROM users
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $user = $stmt->fetch();

        if (!$user) {
            flash('admin_error', 'User tidak ditemukan.');
            redirect('/admin/users');
        }

        view('admin/users/edit', compact('user'));
    }

    public static function update(string $slugUuid): void
    {
        admin_auth();

        $namaLengkap = trim($_POST['nama_lengkap'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $role = trim($_POST['role'] ?? 'admin');

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($namaLengkap === '') {
            $errors[] = 'Nama lengkap wajib diisi.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }

        if ($password !== '' && strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }

        if ($password !== '' && $password !== $passwordConfirmation) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (!in_array($role, ['admin', 'manager', 'supervisor'], true)) {
            $errors[] = 'Role tidak valid.';
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/users/' . $slugUuid . '/edit');
        }

        // ── Ambil user by slug_uuid ───────────────────────────────────────────
        $stmt = db()->prepare("
            SELECT id, slug_uuid FROM users
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $user = $stmt->fetch();

        if (!$user) {
            flash('admin_error', 'User tidak ditemukan.');
            redirect('/admin/users');
        }

        // ── Cek email duplikat (exclude diri sendiri) ─────────────────────────
        $check = db()->prepare("
            SELECT COUNT(*) FROM users
            WHERE email = :email AND id != :id AND deleted_at IS NULL
        ");
        $check->execute([':email' => $email, ':id' => $user['id']]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Email sudah digunakan user lain.');
            redirect('/admin/users/' . $slugUuid . '/edit');
        }

        // ── Update slug (UUID tetap) ──────────────────────────────────────────
        // Ambil UUID dari slug_uuid lama
        $uuidPart = substr($slugUuid, -36); // UUID selalu 36 karakter
        $newSlug = slugify($namaLengkap);
        $newSlugUuid = $newSlug . '-' . $uuidPart;

        // ── Build query dinamis (password opsional) ───────────────────────────
        $fields = [
            'nama_lengkap = :nama_lengkap',
            'role         = :role',
            'email        = :email',
            'slug         = :slug',
            'slug_uuid    = :slug_uuid',
            'updated_at   = :updated_at',
        ];

        $params = [
            ':nama_lengkap' => $namaLengkap,
            ':role' => $role,
            ':email' => $email,
            ':slug' => $newSlug,
            ':slug_uuid' => $newSlugUuid,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $user['id'],
        ];

        // Password hanya diupdate kalau diisi
        if ($password !== '') {
            $fields[] = 'password = :password';
            $params[':password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        db()->prepare($sql)->execute($params);

        flash('admin_success', 'User berhasil diupdate.');
        redirect('/admin/users');
    }

    public static function destroy(string $slugUuid): void
    {
        admin_auth();

        // Cegah admin menghapus dirinya sendiri
        $currentAdminSlugUuid = $_SESSION['admin']['slug_uuid'] ?? '';
        if ($currentAdminSlugUuid === $slugUuid) {
            flash('admin_error', 'Tidak bisa menghapus akun sendiri.');
            redirect('/admin/users');
        }

        $stmt = db()->prepare("
            UPDATE users
            SET deleted_at = :deleted_at
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
        ");

        $stmt->execute([
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':slug_uuid' => $slugUuid,
        ]);

        if ($stmt->rowCount() > 0) {
            flash('admin_success', 'User berhasil dihapus.');
        } else {
            flash('admin_error', 'User tidak ditemukan.');
        }

        redirect('/admin/users');
    }
}