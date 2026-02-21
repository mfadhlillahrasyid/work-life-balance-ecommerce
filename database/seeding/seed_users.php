<?php
// database/seeding/seed_users.php

return function (PDO $pdo): int {

    $stmt = $pdo->prepare("
        INSERT INTO users (
            nama_lengkap,
            role,
            slug,
            slug_uuid,
            email,
            password,
            created_at
        ) VALUES (
            :nama_lengkap,
            :role,
            :slug,
            :slug_uuid,
            :email,
            :password,
            :created_at
        )
    ");

    $stmt->execute([
        ':nama_lengkap' => 'Muhammad Fadhlillah Rasyid',
        ':role'         => 'admin',
        ':slug'         => slugify('Muhammad Fadhlillah Rasyid'),
        ':slug_uuid'    => uuid_v4(),
        ':email'        => 'muhammadfadhlillahrasyid@gmail.com',
        ':password'     => password_hash('Fadhli26071997M', PASSWORD_BCRYPT),
        ':created_at'   => date('Y-m-d H:i:s'),
    ]);

    return 1;
};