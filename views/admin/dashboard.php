<?php
$page_title = 'Dashboard';

ob_start();
?>
<div class="max-w-full mx-auto mb-4 sm:mb-6">
    <div class="flex flex-col gap-4">

        <div class="grid gap-3 sm:flex md:justify-between md:items-center">
            <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center">
                <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                    Dashboard
                </h2>
                <p> <?php breadcrumb(['Dashboard' => null]); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 sm:gap-6">
            <div class="flex flex-col bg-white border border-card-line shadow-2xs rounded-xl">
                <div class="p-4 md:p-6">
                    <div class="flex items-center gap-x-2">
                        <p class="text-xs uppercase text-muted-foreground-1">
                            Total users
                        </p>
                        <div class="hs-tooltip">
                            <div class="hs-tooltip-toggle">
                                <svg class="shrink-0 size-4 text-muted-foreground-1" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                    <path d="M12 17h.01" />
                                </svg>
                                <span
                                    class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-tooltip border border-tooltip-line text-xs font-medium text-tooltip-foreground rounded-md shadow-2xs"
                                    role="tooltip">
                                    The number of daily users
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-1 flex items-center gap-x-2">
                        <h3 class="text-xl sm:text-2xl font-medium text-foreground">
                            72,540
                        </h3>
                        <span class="flex items-center gap-x-1 text-green-600">
                            <svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                                <polyline points="16 7 22 7 22 13" />
                            </svg>
                            <span class="inline-block text-sm">
                                1.7%
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card -->
            <div class="flex flex-col bg-white border border-card-line shadow-2xs rounded-xl">
                <div class="p-4 md:p-6">
                    <div class="flex items-center gap-x-2">
                        <p class="text-xs uppercase text-muted-foreground-1">
                            Sessions
                        </p>
                    </div>

                    <div class="mt-1 flex items-center gap-x-2">
                        <h3 class="text-xl sm:text-2xl font-medium text-foreground">
                            29.4%
                        </h3>
                    </div>
                </div>
            </div>
            <!-- End Card -->

            <!-- Card -->
            <div class="flex flex-col bg-white border border-card-line shadow-2xs rounded-xl">
                <div class="p-4 md:p-6">
                    <div class="flex items-center gap-x-2">
                        <p class="text-xs uppercase text-muted-foreground-1">
                            Avg. Click Rate
                        </p>
                    </div>

                    <div class="mt-1 flex items-center gap-x-2">
                        <h3 class="text-xl sm:text-2xl font-medium text-foreground">
                            56.8%
                        </h3>
                        <span class="flex items-center gap-x-1 text-red-600">
                            <svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 17 13.5 8.5 8.5 13.5 2 7" />
                                <polyline points="16 17 22 17 22 11" />
                            </svg>
                            <span class="inline-block text-sm">
                                1.7%
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <!-- End Card -->

            <!-- Card -->
            <div class="flex flex-col bg-white border border-card-line shadow-2xs rounded-xl">
                <div class="p-4 md:p-6">
                    <div class="flex items-center gap-x-2">
                        <p class="text-xs uppercase text-muted-foreground-1">
                            Pageviews
                        </p>
                    </div>

                    <div class="mt-1 flex items-center gap-x-2">
                        <h3 class="text-xl sm:text-2xl font-medium text-foreground">
                            92,913
                        </h3>
                    </div>
                </div>
            </div>
            <!-- End Card -->
        </div>

    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
