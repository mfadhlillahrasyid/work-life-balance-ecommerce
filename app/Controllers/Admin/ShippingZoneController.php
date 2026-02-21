<?php
// app/Controllers/Admin/ShippingZoneController.php

namespace App\Controllers\Admin;

class ShippingZoneController
{
    // =========================================================================
    // INDEX
    // =========================================================================
    public static function index(): void
    {
        admin_auth();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT *
            FROM shipping_zones
            WHERE 1=1";

        $params = [];

        if ($search !== '') {
            $sql .= " AND name LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY cost ASC";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $allZones = $stmt->fetchAll();

        // Decode provinces JSON
        $allZones = array_map(function ($z) {
            $z['provinces'] = json_decode($z['provinces'], true) ?? [];
            return $z;
        }, $allZones);

        $pagination = paginate($allZones, 10, $page);

        view('admin/shipping-zones/index', [
            'zones' => $pagination['data'],
            'pagination' => $pagination['meta'],
            'search' => $search,
        ]);
    }

    // =========================================================================
    // CREATE
    // =========================================================================
    public static function create(): void
    {
        // Load semua provinsi untuk checkbox
        $provinces = self::loadProvinces();

        view('admin/shipping-zones/create', [
            'provinces' => $provinces,
        ]);
    }

    public static function store(): void
    {
        admin_auth();

        $name = trim($_POST['name'] ?? '');
        $kurir = trim($_POST['kurir'] ?? '');
        $cost = (int) ($_POST['cost'] ?? 0);
        $provinces = $_POST['provinces'] ?? [];

        // VALIDASI DULU
        if ($name === '' || $kurir === '' || $cost < 0 || empty($provinces)) {
            $_SESSION['admin_error'] = 'Semua field wajib diisi dan minimal 1 provinsi dipilih';
            redirect('/admin/shipping-zones/create');
        }

        // BASE SLUG (name + kurir)
        $baseSlug = slugify($name . '-' . $kurir);
        $slug = $baseSlug;

        // CEK UNIQUE SLUG
        $i = 1;
        while (true) {
            $check = db()->prepare("SELECT COUNT(*) FROM shipping_zones WHERE slug = ?");
            $check->execute([$slug]);

            if ($check->fetchColumn() == 0) {
                break;
            }

            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        // UUID & SLUG_UUID
        $uuid = uuid_v4();
        $slugUuid = $slug . '-' . $uuid;

        // Upload icon
        $icon = null;
        if (!empty($_FILES['icon']['name'])) {
            $uploaded = self::uploadIcon($_FILES['icon']);
            if (isset($uploaded['error'])) {
                $_SESSION['admin_error'] = $uploaded['error'];
                redirect('/admin/shipping-zones/create');
            }
            $icon = $uploaded;
        }

        $now = date('Y-m-d H:i:s');

        db()->prepare("
        INSERT INTO shipping_zones 
        (slug, slug_uuid, name, kurir, icon, provinces, cost, created_at)
        VALUES (:slug, :slug_uuid, :name, :kurir, :icon, :provinces, :cost, :created_at)
    ")->execute([
                    ':slug' => $slug,
                    ':slug_uuid' => $slugUuid,
                    ':name' => $name,
                    ':kurir' => $kurir,
                    ':icon' => $icon,
                    ':provinces' => json_encode(array_map('intval', $provinces)),
                    ':cost' => $cost,
                    ':created_at' => $now,
                ]);

        $_SESSION['admin_success'] = 'Shipping zone berhasil ditambahkan';
        redirect('/admin/shipping-zones');
    }

    // =========================================================================
    // EDIT
    // =========================================================================
    public static function edit(string $slugUuid): void
    {
        $stmt = db()->prepare("
            SELECT * 
            FROM shipping_zones 
            WHERE slug_uuid = :slug_uuid 
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $zone = $stmt->fetch();

        if (!$zone) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $zone['provinces'] = json_decode($zone['provinces'], true) ?? [];
        $provinces = self::loadProvinces();

        view('admin/shipping-zones/edit', [
            'zone' => $zone,
            'provinces' => $provinces,
        ]);
    }

    public static function update(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            SELECT * 
            FROM shipping_zones 
            WHERE slug_uuid = :slug_uuid 
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $zone = $stmt->fetch();

        if (!$zone) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $kurir = trim($_POST['kurir'] ?? '');
        $cost = (int) ($_POST['cost'] ?? 0);
        $provinces = $_POST['provinces'] ?? [];

        if ($name === '' || $kurir === '' || $cost < 0 || empty($provinces)) {
            $_SESSION['admin_error'] = 'Semua field wajib diisi dan minimal 1 provinsi dipilih';
            redirect('/admin/shipping-zones/' . $slugUuid . '/edit');
        }

        // HANDLE ICON
        $icon = $zone['icon'];

        if (!empty($_FILES['icon']['name'])) {
            $uploaded = self::uploadIcon($_FILES['icon']);
            if (isset($uploaded['error'])) {
                $_SESSION['admin_error'] = $uploaded['error'];
                redirect('/admin/shipping-zones/' . $slugUuid . '/edit');
            }

            if ($icon && file_exists(ROOT_PATH . '/storage/icons/' . $icon)) {
                unlink(ROOT_PATH . '/storage/icons/' . $icon);
            }

            $icon = $uploaded;
        }

        if (!empty($_POST['remove_icon'])) {
            if ($icon && file_exists(ROOT_PATH . '/storage/icons/' . $icon)) {
                unlink(ROOT_PATH . '/storage/icons/' . $icon);
            }
            $icon = null;
        }

        $oldSlugUuid = $zone['slug_uuid'];

        // REGENERATE SLUG JIKA name ATAU kurir berubah
        // cek perubahan name / kurir
        if ($name !== $zone['name'] || $kurir !== $zone['kurir']) {

            $baseSlug = slugify($name . '-' . $kurir);
            $slug = $baseSlug;

            $i = 1;
            while (true) {
                $check = db()->prepare("
            SELECT COUNT(*) 
            FROM shipping_zones 
            WHERE slug = ? AND id != ?
        ");
                $check->execute([$slug, $slugUuid]);

                if ($check->fetchColumn() == 0)
                    break;

                $slug = $baseSlug . '-' . $i;
                $i++;
            }

            // 🔒 ambil UUID lama
            $oldSlugUuid = $zone['slug_uuid'];
            $uuid = substr($oldSlugUuid, -36); // ambil 36 karakter terakhir (uuid v4)

            // rebuild slug_uuid dengan uuid lama
            $slugUuid = $slug . '-' . $uuid;

        } else {

            $slug = $zone['slug'];
            $slugUuid = $zone['slug_uuid'];
        }

        db()->prepare("
                    UPDATE shipping_zones
                    SET slug       = :slug,
                        slug_uuid  = :new_slug_uuid,
                        name       = :name,
                        kurir      = :kurir,
                        icon       = :icon,
                        provinces  = :provinces,
                        cost       = :cost,
                        updated_at = :updated_at
                    WHERE slug_uuid = :old_slug_uuid
                ")->execute([
                    ':slug' => $slug,
                    ':new_slug_uuid' => $slugUuid,      // slug_uuid baru
                    ':name' => $name,
                    ':kurir' => $kurir,
                    ':icon' => $icon,
                    ':provinces' => json_encode(array_map('intval', $provinces)),
                    ':cost' => $cost,
                    ':updated_at' => date('Y-m-d H:i:s'),
                    ':old_slug_uuid' => $oldSlugUuid,   // slug_uuid lama untuk WHERE
                ]);

        $_SESSION['admin_success'] = 'Shipping zone berhasil diupdate';
        redirect('/admin/shipping-zones');
    }

    // =========================================================================
    // DELETE
    // =========================================================================
    public static function destroy(string $slugUuid): void
    {
        $stmt = db()->prepare("SELECT * FROM shipping_zones WHERE slug_uuid = :slug_uuid LIMIT 1");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $zone = $stmt->fetch();

        if ($zone) {
            if ($zone['icon'] && file_exists(ROOT_PATH . '/storage/icons/' . $zone['icon'])) {
                unlink(ROOT_PATH . '/storage/icons/' . $zone['icon']);
            }
            db()->prepare("DELETE FROM shipping_zones WHERE slug_uuid = :slug_uuid")
                ->execute([':slug_uuid' => $slugUuid]);
        }

        $_SESSION['admin_success'] = 'Shipping zone berhasil dihapus';
        redirect('/admin/shipping-zones');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================
    private static function loadProvinces(): array
    {
        $path = ROOT_PATH . '/database/wilayah/provinces.json';
        if (!file_exists($path))
            return [];

        $data = json_decode(file_get_contents($path), true) ?? [];
        usort($data, fn($a, $b) => strcmp($a['name'], $b['name']));
        return $data;
    }

    private static function uploadIcon(array $file): string|array
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Format icon harus JPG, PNG, WEBP, atau SVG'];
        }

        if ($file['size'] > 1 * 1024 * 1024) {
            return ['error' => 'Ukuran icon maksimal 1MB'];
        }

        $uploadDir = ROOT_PATH . '/storage/icons/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'zone_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return ['error' => 'Gagal upload icon'];
        }

        return $filename;
    }
}