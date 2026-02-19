<?php
// data datang dari controller
// $user = [...]

$page_title = 'Edit User';

ob_start();

?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center">
                <div class="flex flex-col sm:flex-row justify-between gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                        Edit Users
                    </h2>
                    <p> <?php breadcrumb(
                        [
                            'Dashboard' => '/admin/dashboard',
                            'Users' => '/admin/users',
                            'Edit' => null,
                        ]
                    ); ?></p>
                </div>
            </div>

            <form method="POST" action="/admin/users/<?= $user['slug_uuid'] ?>/update"
                class="bg-white p-4 sm:p-6 rounded-2xl border border-neutral-200 relative">

                <div class="flex flex-col gap-4 sm:gap-6">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Fullname</label>
                            <input type="text" name="nama_lengkap"
                                value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required
                                placeholder="Write user fullname..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                placeholder="Write user fullname..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <?php
                        $availableRoles = [
                            'admin' => 'Admin',
                            'manager' => 'Manager',
                            'supervisor' => 'Supervisor',
                        ];

                        $currentRole = $user['role'] ?? '';
                        $currentRoleLabel = $availableRoles[$currentRole] ?? null;
                        ?>

                        <div class="relative z-10 flex flex-col gap-2" data-role-select>
                            <label class="block text-sm font-semibold">Role</label>

                            <button type="button" id="roleTrigger" class="w-full border border-gray-200 rounded-lg cursor-pointer
               py-2 px-3 text-sm text-left flex justify-between items-center
               bg-neutral-50 focus:ring-2 focus:ring-indigo-700 focus:outline-none">

                                <span id="selectedRole"
                                    class="tracking-tight <?= $currentRoleLabel ? 'text-gray-800' : 'text-gray-500' ?>">
                                    <?= $currentRoleLabel
                                        ? htmlspecialchars($currentRoleLabel)
                                        : 'Pilih Role' ?>
                                </span>

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>

                            <input type="hidden" name="role" id="roleInput"
                                value="<?= htmlspecialchars($currentRole) ?>" required>

                            <div id="roleDropdown" class="absolute z-20 mt-2 top-16 w-full bg-white
                                border border-gray-200 rounded-lg shadow-lg hidden">

                                <?php foreach ($availableRoles as $value => $label): ?>
                                    <div class="py-2 px-3 hover:bg-gray-100 cursor-pointer role-option
                        <?= $currentRole === $value ? 'bg-indigo-50' : '' ?>"
                                        data-value="<?= htmlspecialchars($value) ?>"
                                        data-label="<?= htmlspecialchars($label) ?>">

                                        <p
                                            class="text-sm text-gray-800 <?= $currentRole === $value ? 'font-semibold text-indigo-700' : '' ?>">
                                            <?= htmlspecialchars($label) ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Password Fields (hidden by default) -->
                    <div id="passwordFields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Password</label>
                            <input type="password" name="password" id="passwordInput" placeholder="••••••••"
                                autocomplete="new-password"
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Retype Password</label>
                            <input type="password" name="password_confirmation" id="passwordConfirmInput"
                                placeholder="••••••••" autocomplete="new-password"
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="w-full">
                            <button type="button" id="togglePasswordBtn"
                                class="inline-flex items-center gap-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-2 rounded-lg hover:bg-indigo-100 transition-colors">
                                <span id="togglePasswordLabel">Change Password</span>
                            </button>
                        </div>

                        <div class="flex justify-end items-center gap-2">
                            <a href="/admin/users"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none">
                                Cancel
                            </a>
                            <button type="submit"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-neutral-900 bg-neutral-900 text-neutral-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
