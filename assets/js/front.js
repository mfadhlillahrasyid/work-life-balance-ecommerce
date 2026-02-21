/**
 * SHOP FILTER - CLEAN VERSION (NO DUPLICATES)
 *
 * Features:
 * - Sort floating select (desktop + mobile)
 * - Live search (hybrid client + server)
 * - Smart redirect (gender/category → path)
 * - Mobile modal drawer
 * - Product variant selector
 * - Product image gallery
 * - Add to cart
 * - Cart quantity update
 */

document.addEventListener("DOMContentLoaded", () => {
  // ==========================================
  // 1. MOBILE FILTER MODAL
  // ==========================================
  const mobileFilterTrigger = document.querySelector(".mobile-filter-trigger");
  const mobileFilterDrawer = document.querySelector(".mobile-filter-drawer");
  const mobileFilterClose = document.querySelector(".mobile-filter-close");
  const mobileFilterBackdrop = document.querySelector(
    ".mobile-filter-backdrop",
  );

  if (mobileFilterTrigger && mobileFilterDrawer) {
    // Open modal
    mobileFilterTrigger.addEventListener("click", () => {
      mobileFilterDrawer.classList.remove("translate-y-full");
      mobileFilterBackdrop?.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    });

    // Close modal
    const closeModal = () => {
      mobileFilterDrawer.classList.add("translate-y-full");
      mobileFilterBackdrop?.classList.add("hidden");
      document.body.style.overflow = "";
    };

    mobileFilterClose?.addEventListener("click", closeModal);
    mobileFilterBackdrop?.addEventListener("click", closeModal);

    // Close on ESC key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeModal();
    });
  }

  // ==========================================
  // 2. SORT FLOATING SELECT (Desktop + Mobile)
  // ==========================================
  function initSortSelect(wrapper) {
    if (!wrapper) return;

    const trigger = wrapper.querySelector(".sort-trigger");
    const dropdown = wrapper.querySelector(".sort-dropdown");
    const label = wrapper.querySelector(".sort-label");
    const input = wrapper.querySelector(".sort-input");

    if (!trigger || !dropdown) return;

    // Toggle dropdown
    trigger.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();

      // Close other dropdowns
      document.querySelectorAll(".sort-dropdown").forEach((d) => {
        if (d !== dropdown) d.classList.add("hidden");
      });

      dropdown.classList.toggle("hidden");
    });

    // Select option
    dropdown.querySelectorAll(".sort-option").forEach((option) => {
      option.addEventListener("click", (e) => {
        e.stopPropagation();

        const value = option.dataset.value;
        const labelText = option.dataset.label;

        label.textContent = labelText;
        label.classList.remove("text-neutral-500");
        label.classList.add("text-neutral-800");
        input.value = value;
        dropdown.classList.add("hidden");

        // Auto-submit form
        const form = wrapper.closest("form");
        if (form) form.submit();
      });
    });

    // Click outside to close
    document.addEventListener("click", (e) => {
      if (!wrapper.contains(e.target)) {
        dropdown.classList.add("hidden");
      }
    });
  }

  // Initialize all sort selects (desktop + mobile)
  document.querySelectorAll(".sort-select-wrapper").forEach(initSortSelect);

  // ==========================================
  // 3. LIVE SEARCH (Hybrid: Client + Server)
  // ==========================================
  let searchTimeout;

  document.querySelectorAll(".live-search-input").forEach((searchInput) => {
    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase().trim();

      // Clear previous timeout
      clearTimeout(searchTimeout);

      // CLIENT-SIDE: Instant filter (for UX)
      document.querySelectorAll("[data-product-card]").forEach((card) => {
        const title = card.dataset.title || "";
        const price = card.dataset.price || "";

        const match = title.includes(query) || price.includes(query);
        card.style.display = match ? "" : "none";
      });

      // SERVER-SIDE: Redirect after 800ms (for accuracy)
      searchTimeout = setTimeout(() => {
        if (query.length >= 3 || query.length === 0) {
          const form = this.closest("form");
          if (form) form.submit();
        }
      }, 800);
    });
  });

  // ==========================================
  // 4. SMART REDIRECT (Gender/Category → Path)
  // ==========================================

  // Gender redirect
  document.querySelectorAll(".filter-gender").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const label = this.closest("label");
      const genderSlug = label.dataset.redirectValue;

      // Count selected genders
      const checkedGenders = document.querySelectorAll(
        ".filter-gender:checked",
      );

      // If exactly 1 gender → redirect to /shop/{gender}
      if (checkedGenders.length === 1) {
        const selectedGender = checkedGenders[0].value;

        // Get other active filters
        const form = this.closest("form");
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
          if (key !== "gender[]") {
            params.append(key, value);
          }
        }

        const queryString = params.toString();
        const newUrl = queryString
          ? `/shop/${selectedGender}?${queryString}`
          : `/shop/${selectedGender}`;

        window.location.href = newUrl;
      }
    });
  });

  // Category redirect
  document.querySelectorAll(".filter-category").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const label = this.closest("label");
      const categorySlug = label.dataset.redirectValue;

      // Count selected categories
      const checkedCategories = document.querySelectorAll(
        ".filter-category:checked",
      );

      // If exactly 1 category → redirect to /shop/{gender}/{category}
      if (checkedCategories.length === 1) {
        const selectedCategory = checkedCategories[0].value;

        // Extract current gender from URL
        const currentPath = window.location.pathname;
        const pathMatch = currentPath.match(/^\/shop\/([a-z0-9\-]+)/);
        const currentGender = pathMatch ? pathMatch[1] : null;

        // Get other active filters
        const form = this.closest("form");
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
          if (key !== "category[]" && key !== "gender[]") {
            params.append(key, value);
          }
        }

        // Build new URL
        let newUrl;
        if (currentGender) {
          // At /shop/{gender} → go to /shop/{gender}/{category}
          const queryString = params.toString();
          newUrl = queryString
            ? `/shop/${currentGender}/${selectedCategory}?${queryString}`
            : `/shop/${currentGender}/${selectedCategory}`;
        } else {
          // At /shop → stay with query param
          params.append("category[]", selectedCategory);
          newUrl = `/shop?${params.toString()}`;
        }

        window.location.href = newUrl;
      }
    });
  });

  // ==========================================
  // 5. PRODUCT VARIANT SELECTOR (Detail Page)
  // ==========================================
  function setupVariant({ selector, inputId, dataKey }) {
    const buttons = document.querySelectorAll(selector);
    const hiddenInput = document.getElementById(inputId);

    if (!buttons.length || !hiddenInput) return;

    buttons.forEach((btn) => {
      btn.addEventListener("click", () => {
        buttons.forEach((b) => {
          b.classList.remove("bg-black", "text-white", "border-black");
        });

        btn.classList.add("bg-black", "text-white", "border-black");
        hiddenInput.value = btn.dataset[dataKey];
      });
    });
  }

  setupVariant({
    selector: ".variant-color",
    inputId: "selectedColor",
    dataKey: "color",
  });

  setupVariant({
    selector: ".variant-size",
    inputId: "selectedSize",
    dataKey: "size",
  });

  // ==========================================
  // 6. PRODUCT IMAGE GALLERY (Detail Page)
  // ==========================================
  const mainImage = document.getElementById("mainProductImage");
  const thumbnails = document.querySelectorAll("#thumbnailContainer img");
  const container = document.getElementById("thumbnailContainer");

  if (mainImage && container) {
    thumbnails.forEach((thumb) => {
      thumb.addEventListener("click", () => {
        mainImage.src = thumb.dataset.full;

        thumbnails.forEach((t) => t.classList.remove("ring-2", "ring-black"));
        thumb.classList.add("ring-2", "ring-black");
      });
    });

    document
      .querySelector("[data-thumb-prev]")
      ?.addEventListener("click", () => {
        container.scrollBy({ left: -200, behavior: "smooth" });
      });

    document
      .querySelector("[data-thumb-next]")
      ?.addEventListener("click", () => {
        container.scrollBy({ left: 200, behavior: "smooth" });
      });
  }

  // ==========================================
  // 7. ADD TO CART (Detail Page)
  // ==========================================
  document
    .getElementById("addToCartBtn")
    ?.addEventListener("click", async () => {
      const slugUuid = document.getElementById("productSlugUuid")?.value;
      const color = document.getElementById("selectedColor")?.value;
      const size = document.getElementById("selectedSize")?.value;

      if (!color || !size) {
        alert("Pilih warna dan size terlebih dahulu");
        return;
      }

      const res = await fetch("/cart/add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          slug_uuid: slugUuid,
          color,
          size,
          qty: 1,
        }),
      });

      const data = await res.json();

      if (!data.success) {
        alert(data.message);
        return;
      }

      const badge = document.getElementById("cartBadge");
      if (badge) {
        badge.textContent = data.total_qty;
        badge.classList.remove("hidden");
      }

      alert("Produk berhasil ditambahkan ke keranjang");
    });

  // ==========================================
  // 8. CART QUANTITY UPDATE (Cart Page)
  // ==========================================

  // Debounce utility
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Update cart function
  async function updateCartQty(itemId, qty) {
    const lineEl = document.querySelector(`[data-line-subtotal="${itemId}"]`);
    if (lineEl) lineEl.classList.add("opacity-50", "pointer-events-none");

    const res = await fetch("/cart/update", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ item_id: itemId, qty: qty }),
    });

    const data = await res.json();

    if (lineEl) lineEl.classList.remove("opacity-50", "pointer-events-none");

    if (data.success) {
      if (data.item_subtotal && lineEl) {
        lineEl.textContent =
          "IDR " + data.item_subtotal.toLocaleString("id-ID");
      }

      const subtotalEl = document.getElementById("cartSubtotal");
      if (subtotalEl) {
        subtotalEl.textContent = "Rp " + data.subtotal.toLocaleString("id-ID");
      }

      const totalItemsEl = document.getElementById("totalItemsCount");
      if (totalItemsEl) {
        totalItemsEl.textContent = data.total_qty;
      }
    } else {
      alert("Failed to update cart");
    }
  }

  const debouncedUpdate = debounce((itemId, qty) => {
    updateCartQty(itemId, qty);
  }, 500);

  // Plus/Minus buttons
  document.addEventListener("click", async (e) => {
    // Plus button
    if (e.target.classList.contains("qty-plus")) {
      const button = e.target;
      const id = button.dataset.itemId;
      const qtyContainer = button.closest(".inline-flex");
      const input = qtyContainer.querySelector(".cart-qty");

      let qty = parseInt(input.value);
      qty++;
      input.value = qty;

      debouncedUpdate(id, qty);
    }

    // Minus button
    if (e.target.classList.contains("qty-minus")) {
      const button = e.target;
      const id = button.dataset.itemId;
      const qtyContainer = button.closest(".inline-flex");
      const input = qtyContainer.querySelector(".cart-qty");

      let qty = parseInt(input.value);
      if (qty <= 1) return;

      qty--;
      input.value = qty;

      debouncedUpdate(id, qty);
    }
  });

  // ==========================================
  // 9. REMOVE CART ITEM (Cart Page)
  // ==========================================
  document.querySelectorAll(".cart-remove").forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (!confirm("Remove item?")) return;

      const itemId = btn.dataset.itemId;
      const row = btn.closest("tr");

      row.classList.add("opacity-50", "pointer-events-none");

      const res = await fetch("/cart/remove", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ item_id: itemId }),
      });

      const data = await res.json();

      if (data.success) {
        row.remove();

        const subtotalEl = document.getElementById("cartSubtotal");
        if (subtotalEl) {
          subtotalEl.textContent =
            "Rp " + data.subtotal.toLocaleString("id-ID");
        }

        const totalItemsEl = document.getElementById("totalItemsCount");
        if (totalItemsEl) {
          totalItemsEl.textContent = data.total_qty;
        }

        if (data.total_qty === 0) {
          setTimeout(() => location.reload(), 300);
        }
      } else {
        row.classList.remove("opacity-50", "pointer-events-none");
        alert("Failed to remove item: " + (data.message || "Unknown error"));
      }
    });
  });
});

/**
 * HEADER FUNCTIONALITY
 *
 * - Algolia-style instant search with keyboard navigation
 * - Mobile sidebar menu
 * - Smooth animations
 * - Dropdown Navigation
 */

document.addEventListener("DOMContentLoaded", () => {
  // ==========================================
  // ALGOLIA-STYLE SEARCH MODAL
  // ==========================================

  // Get ALL search triggers (desktop, mobile, sidebar)
  const searchTriggerDesktop = document.getElementById("searchTriggerDesktop");
  const searchTriggerMobile = document.getElementById("searchTriggerMobile");
  const searchTriggerSidebar = document.getElementById("searchTriggerSidebar");

  const searchModal = document.getElementById("searchModal");
  const searchBackdrop = document.getElementById("searchBackdrop");
  const searchInput = document.getElementById("searchInput");
  const searchClose = document.getElementById("searchClose");
  const searchResults = document.getElementById("searchResults");
  const searchPanel = document.getElementById("searchPanel");

  let searchTimeout;
  let selectedIndex = -1;
  let searchData = [];

  // Open search modal
  function openSearch() {
    searchModal.classList.remove("hidden");
    document.body.style.overflow = "hidden";

    // Focus input after animation
    setTimeout(() => {
      searchInput.focus();
    }, 100);
  }

  // Close search modal
  function closeSearch() {
    searchModal.classList.add("hidden");
    document.body.style.overflow = "";
    searchInput.value = "";
    searchResults.innerHTML = "";
    selectedIndex = -1;
  }

  // Attach click event to ALL search triggers
  searchTriggerDesktop?.addEventListener("click", openSearch);
  searchTriggerMobile?.addEventListener("click", openSearch);
  searchTriggerSidebar?.addEventListener("click", openSearch);

  // Close triggers
  searchBackdrop?.addEventListener("click", closeSearch);
  searchClose?.addEventListener("click", closeSearch);

  // CMD/CTRL + K to open search
  document.addEventListener("keydown", (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === "k") {
      e.preventDefault();
      openSearch();
    }

    // ESC to close
    if (e.key === "Escape" && !searchModal.classList.contains("hidden")) {
      closeSearch();
    }
  });

  // Instant search (debounced)
  searchInput?.addEventListener("input", function () {
    const query = this.value.trim();

    clearTimeout(searchTimeout);

    if (query.length < 2) {
      searchResults.innerHTML = "";
      return;
    }

    // Show loading state
    searchResults.innerHTML = `
      <div class="p-4 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-indigo-600"></div>
        <p class="mt-2 text-sm text-gray-500">Searching...</p>
      </div>
    `;

    // Debounced search
    searchTimeout = setTimeout(async () => {
      try {
        const response = await fetch(
          `/api/search?q=${encodeURIComponent(query)}`,
        );
        const data = await response.json();

        if (data.success && data.products && data.products.length > 0) {
          searchData = data.products;
          renderSearchResults(searchData);
        } else {
          searchResults.innerHTML = `
            <div class="p-8 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="mt-2 text-sm text-gray-900 font-medium">No products found</p>
              <p class="mt-1 text-xs text-gray-500">Try a different search term</p>
            </div>
          `;
        }
      } catch (error) {
        searchResults.innerHTML = `
          <div class="p-8 text-center">
            <p class="text-sm text-red-600">Error loading results. Please try again.</p>
          </div>
        `;
      }
    }, 300);
  });

  // Render search results
  function renderSearchResults(products) {
    selectedIndex = -1;

    const html = products
      .map(
        (product, index) => `
      <a 
        href="/shop/${product.gender_slug}/${product.category_slug}/${product.slug_uuid}"
        class="search-result flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors ${index === selectedIndex ? "bg-gray-50" : ""}"
        data-index="${index}"
      >
        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
          ${
            product.thumbnail
              ? `<img src="/storage/products/${product.thumbnail}" alt="${product.title}" class="w-full h-full object-cover">`
              : `<div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>`
          }
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-gray-900 truncate">${product.title}</p>
          <p class="text-xs text-gray-500 capitalize">${product.category_slug} • ${product.gender_slug}</p>
          <p class="text-sm font-medium text-indigo-600 mt-1">Rp ${Number(product.price_from).toLocaleString("id-ID")}</p>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    `,
      )
      .join("");

    searchResults.innerHTML = html;
  }

  // Keyboard navigation
  searchInput?.addEventListener("keydown", (e) => {
    const results = document.querySelectorAll(".search-result");

    if (e.key === "ArrowDown") {
      e.preventDefault();
      selectedIndex = Math.min(selectedIndex + 1, results.length - 1);
      updateSelection(results);
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      selectedIndex = Math.max(selectedIndex - 1, -1);
      updateSelection(results);
    } else if (e.key === "Enter" && selectedIndex >= 0) {
      e.preventDefault();
      results[selectedIndex]?.click();
    }
  });

  // Update selection highlight
  function updateSelection(results) {
    results.forEach((result, index) => {
      if (index === selectedIndex) {
        result.classList.add("bg-gray-50");
        result.scrollIntoView({ block: "nearest", behavior: "smooth" });
      } else {
        result.classList.remove("bg-gray-50");
      }
    });
  }

  // ==========================================
  // SHOP DROPDOWN (Desktop)
  // ==========================================

  const button = document.getElementById("shopMenuButton");
  const dropdown = document.getElementById("shopDropdown");
  const icon = document.getElementById("shopMenuIcon");
  const wrapper = document.getElementById("shopMenuWrapper");

  if (button && dropdown && icon && wrapper) {
    let isOpen = false;

    const openMenu = () => {
      dropdown.classList.remove("opacity-0", "invisible");
      dropdown.classList.add("opacity-100", "visible");
      icon.classList.add("rotate-180");
      isOpen = true;
    };

    const closeMenu = () => {
      dropdown.classList.remove("opacity-100", "visible");
      dropdown.classList.add("opacity-0", "invisible");
      icon.classList.remove("rotate-180");
      isOpen = false;
    };

    // Toggle on button click
    button.addEventListener("click", (e) => {
      e.stopPropagation();
      isOpen ? closeMenu() : openMenu();
    });

    // Close when clicking outside
    document.addEventListener("click", (e) => {
      if (!wrapper.contains(e.target)) {
        closeMenu();
      }
    });

    // Close on ESC
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && isOpen) {
        closeMenu();
      }
    });
  }
});
