<?php if ($pagination['total_pages'] > 1): ?>
    <div class="flex justify-center">
        <nav class="inline-flex items-center gap-1 text-sm">

            <!-- Prev -->
            <a href="?page=<?= max(1, $pagination['current_page'] - 1) ?>"
               class="inline-flex items-center justify-center w-9 h-9 rounded-full border
               <?= $pagination['has_prev']
                   ? 'text-gray-700 hover:bg-gray-100 bg-white'
                   : 'text-gray-300 cursor-not-allowed pointer-events-none' ?>">
                &laquo;
            </a>

            <!-- Pages -->
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <a href="?page=<?= $i ?>"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full border
                   <?= $i === $pagination['current_page']
                       ? 'bg-neutral-900 text-white border-neutral-900'
                       : 'text-gray-700 hover:bg-gray-100 bg-white' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <!-- Next -->
            <a href="?page=<?= min($pagination['total_pages'], $pagination['current_page'] + 1) ?>"
               class="inline-flex items-center justify-center w-9 h-9 rounded-full border
               <?= $pagination['has_next']
                   ? 'text-gray-700 hover:bg-gray-100 bg-white'
                   : 'text-gray-300 cursor-not-allowed pointer-events-none' ?>">
                &raquo;
            </a>

        </nav>
    </div>
<?php endif; ?>
