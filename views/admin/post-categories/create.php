<?php
$page_title = 'Create Post Category';

ob_start();

?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center">
                <div class="flex flex-col sm:flex-row justify-between gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                        Add Categories
                    </h2>
                    <p>
                        <?php breadcrumb(
                            [
                                'Dashboard' => '/admin/dashboard',
                                'Post Categories' => '/admin/post-categories',
                                'Create' => null,
                            ]
                        ); ?>
                    </p>
                </div>
            </div>

            <form method="POST" action="/admin/post-categories/"
                class="bg-white p-5 rounded-2xl border border-neutral-200">

                <div class="flex flex-col gap-4 sm:gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold">Title</label>
                        <input type="text" name="title" required
                            placeholder="Write Post Title..."
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold">Description</label>
                        <textarea name="description" rows="7" placeholder="Write Post Description..."
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end items-center gap-2">
                        <a href="/admin/post-categories"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                        <button type="submit"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-neutral-900 bg-neutral-900 text-neutral-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none">
                            Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
