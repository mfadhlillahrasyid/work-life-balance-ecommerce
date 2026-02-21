<?php
// database/seeding/seed_shipping_zones.php

return function (PDO $pdo): int {

    $zones = [
        [
            'name' => 'Jawa & Bali',
            'kurir' => 'JNE REG',
            'provinces' => [31, 32, 33, 34, 35, 36, 51],
            'cost' => 20000,
        ],
        [
            'name' => 'Sumatera',
            'kurir' => 'JNE REG',
            'provinces' => [11, 12, 13, 14, 15, 16, 17, 18, 19, 21],
            'cost' => 30000,
        ],
        [
            'name' => 'Kalimantan',
            'kurir' => 'JNE REG',
            'provinces' => [61, 62, 63, 64, 65],
            'cost' => 35000,
        ],
        [
            'name' => 'Sulawesi',
            'kurir' => 'JNE REG',
            'provinces' => [71, 72, 73, 74, 75, 76],
            'cost' => 35000,
        ],
        [
            'name' => 'Nusa Tenggara',
            'kurir' => 'JNE REG',
            'provinces' => [52, 53],
            'cost' => 40000,
        ],
        [
            'name' => 'Maluku & Papua',
            'kurir' => 'JNE REG',
            'provinces' => [81, 82, 91, 92],
            'cost' => 45000,
        ],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO shipping_zones 
        (slug, slug_uuid, name, kurir, provinces, cost, created_at)
        VALUES (:slug, :slug_uuid, :name, :kurir, :provinces, :cost, :created_at)
    ");

    $now = date('Y-m-d H:i:s');
    $inserted = 0;

    foreach ($zones as $zone) {

        $baseSlug = slugify($zone['name'] . '-' . $zone['kurir']);
        $slug = $baseSlug;

        $i = 1;
        while (true) {
            $check = $pdo->prepare("SELECT COUNT(*) FROM shipping_zones WHERE slug = ?");
            $check->execute([$slug]);

            if ($check->fetchColumn() == 0) break;

            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        $uuid = uuid_v4();
        $slugUuid = $slug . '-' . $uuid;

        $stmt->execute([
            ':slug'       => $slug,
            ':slug_uuid'  => $slugUuid,
            ':name'       => $zone['name'],
            ':kurir'      => $zone['kurir'],
            ':provinces'  => json_encode($zone['provinces']),
            ':cost'       => $zone['cost'],
            ':created_at' => $now,
        ]);

        $inserted++;
    }

    return $inserted;
};