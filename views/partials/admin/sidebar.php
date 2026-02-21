<aside id="top-bar-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0"
    aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-white border-e border-default">
        <a href="https://flowbite.com" class="flex ms-2 md:me-24">
            <img src="/assets/images/favicon.png" class="w-8 rounded-lg me-3" alt="FlowBite Logo" />
            <span class="self-center text-lg whitespace-nowrap font-bebas tracking-tight">Work Life Balance</span>
        </a>
        <ul class="space-y-2 font-normal mt-9 sm:mt-6 tracking-tight">
            <li>
                <a href="/admin/dashboard"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_exact('/admin/dashboard') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path stroke-width="2"
                            d="M21 3C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H21ZM7 5H4V19H7V5ZM20 5H9V19H20V5Z">
                        </path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>

            <div class="text-gray-400 text-xs pt-3">Master</div>
            <li>
                <a href="/admin/post-categories"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/post-categories') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path stroke-width="2"
                            d="M4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21H14C14.5523 21 15 20.5523 15 20V15.2973L15.9995 19.9996C16.1143 20.5398 16.6454 20.8847 17.1856 20.7699L21.0982 19.9382C21.6384 19.8234 21.9832 19.2924 21.8684 18.7522L18.9576 5.0581C18.8428 4.51788 18.3118 4.17304 17.7716 4.28786L14.9927 4.87853C14.9328 4.38353 14.5112 4 14 4H10C10 3.44772 9.55228 3 9 3H4ZM10 6H13V14H10V6ZM10 19V16H13V19H10ZM8 5V15H5V5H8ZM8 17V19H5V17H8ZM17.3321 16.6496L19.2884 16.2338L19.7042 18.1898L17.7479 18.6057L17.3321 16.6496ZM16.9163 14.6933L15.253 6.86789L17.2092 6.45207L18.8726 14.2775L16.9163 14.6933Z">
                        </path>
                    </svg>
                    <span>Post Categories</span>
                </a>
            </li>

            <li>
                <a href="/admin/genders"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/genders') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400" aria-hidden="true">
                        <path stroke-width="2"
                            d="M3.16113 4.46875C5.58508 2.0448 9.44716 1.9355 12.0008 4.14085C14.5528 1.9355 18.4149 2.0448 20.8388 4.46875C23.2584 6.88836 23.3716 10.741 21.1785 13.2947L13.4142 21.0858C12.6686 21.8313 11.4809 21.8652 10.6952 21.1874L10.5858 21.0858L2.82141 13.2947C0.628282 10.741 0.741522 6.88836 3.16113 4.46875ZM4.57534 5.88296C2.86819 7.59011 2.81942 10.3276 4.42902 12.0937L4.57534 12.2469L12 19.6715L17.3026 14.3675L13.7677 10.8327L12.7071 11.8934C11.5355 13.0649 9.636 13.0649 8.46443 11.8934C7.29286 10.7218 7.29286 8.8223 8.46443 7.65073L10.5656 5.54823C8.85292 4.17713 6.37076 4.23993 4.7286 5.73663L4.57534 5.88296ZM13.0606 8.71139C13.4511 8.32086 14.0843 8.32086 14.4748 8.71139L18.7168 12.9533L19.4246 12.2469C21.1819 10.4896 21.1819 7.64032 19.4246 5.88296C17.7174 4.17581 14.9799 4.12704 13.2139 5.73663L13.0606 5.88296L9.87864 9.06494C9.51601 9.42757 9.49011 9.99942 9.80094 10.3919L9.87864 10.4792C10.2413 10.8418 10.8131 10.8677 11.2056 10.5569L11.2929 10.4792L13.0606 8.71139Z">
                        </path>
                    </svg>
                    <span>Genders</span>
                </a>
            </li>

            <li>
                <a href="/admin/product-categories"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/product-categories') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path stroke-width="2"
                            d="M11 4H21V6H11V4ZM11 8H17V10H11V8ZM11 14H21V16H11V14ZM11 18H17V20H11V18ZM3 4H9V10H3V4ZM5 6V8H7V6H5ZM3 14H9V20H3V14ZM5 16V18H7V16H5Z">
                        </path>
                    </svg>
                    <span>Product Categories</span>
                </a>
            </li>

            <div class="text-gray-400 text-xs pt-3">Data</div>
            <li>
                <a href="/admin/shipping-zones"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/shipping-zones') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400">
                        <path
                            d="M4 5V19H20V5H4ZM3 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3ZM6 7H9V17H6V7ZM10 7H12V17H10V7ZM13 7H14V17H13V7ZM15 7H18V17H15V7Z">
                        </path>
                    </svg>

                    <span>Shipments</span>
                </a>
            </li>

            <li>
                <a href="/admin/products"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/products') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>

                    <span>Products</span>
                </a>
            </li>

            <li>
                <a href="/admin/customers"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/customers') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>

                    <span>Customers</span>
                </a>
            </li>

            <li>
                <a href="/admin/transactions"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/transactions') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>

                    <span>Transactions</span>
                </a>
            </li>

            <div class="text-gray-400 text-xs pt-3">Content</div>
            <li>
                <a href="/admin/posts"
                    class="flex gap-2 items-center px-2 py-2 text-sm text-gray-800 rounded-md group <?= is_active_group('/admin/posts') ? 'bg-black text-white' : 'text-gray-300 hover:bg-black/5' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 transition-all duration-300 group-hover:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
                    </svg>

                    <span>Posts</span>
                </a>
            </li>

        </ul>
    </div>
</aside>