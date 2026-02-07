function generateUID() {
  return (
    "img_" +
    Date.now().toString(36) +
    "_" +
    Math.random().toString(36).substr(2, 9)
  );
}

// ==============================
// SIDEBAR TOGGLE (ADMIN)
// ==============================
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  if (!sidebar) return;

  sidebar.classList.toggle("-translate-x-full");
}

// ==============================
// FLASH AUTO DISMISS
// ==============================
document.addEventListener("DOMContentLoaded", () => {
  const flash = document.querySelector(".flash");
  if (!flash) return;

  setTimeout(() => {
    flash.classList.add("opacity-0");
    setTimeout(() => flash.remove(), 300);
  }, 3000);
});

// Post Select Dropdown
document.addEventListener("DOMContentLoaded", () => {
  const trigger = document.getElementById("categoryTrigger");
  const dropdown = document.getElementById("categoryDropdown");
  const selected = document.getElementById("selectedCategory");
  const input = document.getElementById("categoryInput");

  if (!trigger || !dropdown || !selected || !input) return;

  trigger.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    dropdown.classList.toggle("hidden");
  });

  dropdown.querySelectorAll(".category-option").forEach((option) => {
    option.addEventListener("click", () => {
      input.value = option.dataset.value; // UUID
      selected.textContent = option.dataset.label; // TITLE

      selected.classList.remove("text-gray-500");
      selected.classList.add("text-gray-800");

      dropdown.classList.add("hidden");
    });
  });

  document.addEventListener("click", () => {
    dropdown.classList.add("hidden");
  });
});

// Product & Gender Dropdown
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("[data-select]").forEach((wrapper) => {
    const trigger = wrapper.querySelector(".select-trigger");
    const label = wrapper.querySelector(".select-label");
    const input = wrapper.querySelector(".select-input");
    const dropdown = wrapper.querySelector(".select-dropdown");

    if (!trigger || !label || !input || !dropdown) return;

    trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("hidden");
    });

    dropdown.querySelectorAll(".select-option").forEach((opt) => {
      opt.addEventListener("click", () => {
        label.textContent = opt.dataset.label;
        label.classList.remove("text-gray-500");
        label.classList.add("text-gray-800");

        input.value = opt.dataset.value;
        dropdown.classList.add("hidden");
      });
    });
  });

  document.addEventListener("click", () => {
    document
      .querySelectorAll(".select-dropdown")
      .forEach((d) => d.classList.add("hidden"));
  });
});

/* =====================================================
 * DROPZONE COMPONENT
 * ===================================================== */
document.addEventListener("DOMContentLoaded", () => {
  const iconDropzone = document.getElementById("dropzone");
  /* =====================================================
   * DROPZONE ICON
   * ===================================================== */

  if (iconDropzone) {
    const input = document.getElementById("iconInput");
    const previewImage = document.getElementById("previewImage");
    const previewWrapper = document.getElementById("previewWrapper");
    const placeholder = document.getElementById("placeholder");
    const loading = document.getElementById("loading");
    const removeBtn = document.getElementById("removeImage");
    const removeFlag = document.getElementById("removeIcon");

    // INIT EXISTING ICON (EDIT MODE)
    const existingIcon = iconDropzone.dataset.existingIcon;
    if (existingIcon) {
      placeholder.classList.add("hidden");
      previewWrapper.classList.remove("hidden");
      previewImage.src = "/storage/icons/" + existingIcon;
      removeFlag.value = "0";
    }

    iconDropzone.addEventListener("click", () => input.click());

    iconDropzone.addEventListener("dragover", (e) => {
      e.preventDefault();
      iconDropzone.classList.add("border-gray-500");
    });

    iconDropzone.addEventListener("dragleave", () => {
      iconDropzone.classList.remove("border-gray-500");
    });

    iconDropzone.addEventListener("drop", (e) => {
      e.preventDefault();
      iconDropzone.classList.remove("border-gray-500");
      handleIconFile(e.dataTransfer.files[0]);
    });

    input.addEventListener("change", () => {
      handleIconFile(input.files[0]);
    });

    function handleIconFile(file) {
      if (!file || !file.type.startsWith("image/")) return;

      if (file.size > 1024 * 1024) {
        alert("Max image size 1MB");
        input.value = "";
        return;
      }

      removeFlag.value = "0";
      loading.classList.remove("hidden");
      placeholder.classList.add("hidden");

      const reader = new FileReader();
      reader.onload = (e) => {
        previewImage.src = e.target.result;
        previewWrapper.classList.remove("hidden");
        loading.classList.add("hidden");
      };
      reader.readAsDataURL(file);
    }

    removeBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      previewImage.src = "";
      previewWrapper.classList.add("hidden");
      placeholder.classList.remove("hidden");
      input.value = "";
      removeFlag.value = "1";
    });
  }

  /* =====================================================
   * DROPZONE BANNER
   * ===================================================== */
  const bannerDropzone = document.getElementById("banner-dropzone");

  if (bannerDropzone) {
    const input = bannerDropzone.querySelector("#bannerInput");
    const previewImage = bannerDropzone.querySelector("#previewImage");
    const previewWrapper = bannerDropzone.querySelector("#previewWrapper");
    const placeholder = bannerDropzone.querySelector("#placeholder");
    const loading = bannerDropzone.querySelector("#loading");
    const removeBtn = bannerDropzone.querySelector("#removeImage");
    const removeFlag = bannerDropzone.querySelector("#removeBanner");

    // INIT EXISTING BANNER (EDIT MODE)
    const existingBanner = bannerDropzone.dataset.existingBanner;
    if (existingBanner) {
      placeholder.classList.add("hidden");
      previewWrapper.classList.remove("hidden");
      previewImage.src = "/storage/banners/" + existingBanner;
      removeFlag.value = "0";
    }

    bannerDropzone.addEventListener("click", () => input.click());

    bannerDropzone.addEventListener("dragover", (e) => {
      e.preventDefault();
      bannerDropzone.classList.add("border-gray-500");
    });

    bannerDropzone.addEventListener("dragleave", () => {
      bannerDropzone.classList.remove("border-gray-500");
    });

    bannerDropzone.addEventListener("drop", (e) => {
      e.preventDefault();
      bannerDropzone.classList.remove("border-gray-500");
      handleBannerFile(e.dataTransfer.files[0]);
    });

    input.addEventListener("change", () => {
      handleBannerFile(input.files[0]);
    });

    function handleBannerFile(file) {
      if (!file || !file.type.startsWith("image/")) return;

      if (file.size > 1024 * 1024) {
        alert("Max image size 1MB");
        input.value = "";
        return;
      }

      removeFlag.value = "0";
      loading.classList.remove("hidden");
      placeholder.classList.add("hidden");

      const reader = new FileReader();
      reader.onload = (e) => {
        previewImage.src = e.target.result;
        previewWrapper.classList.remove("hidden");
        loading.classList.add("hidden");
      };
      reader.readAsDataURL(file);
    }

    removeBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      previewImage.src = "";
      previewWrapper.classList.add("hidden");
      placeholder.classList.remove("hidden");
      input.value = "";
      removeFlag.value = "1";
    });
  }
});

/* =====================================================
 * DROPZONE IMAGES PRODUCT
 * ===================================================== */
document.addEventListener("DOMContentLoaded", () => {
  const dropzone = document.getElementById("imagesDropzone");
  if (!dropzone) return;

  const input = dropzone.querySelector("[data-images-input]");
  const preview = dropzone.querySelector(".images-preview");
  const placeholder = dropzone.querySelector(".images-placeholder");
  const uploadBtn = dropzone.querySelector("[data-upload-btn]");
  const container = dropzone.querySelector("[data-images-container]");

  /* =====================================================
   * MODE DETECTION
   * ===================================================== */
  const isEditMode =
    document.querySelectorAll("[data-existing-image]").length > 0;
  console.log(`MODE: ${isEditMode ? "EDIT" : "CREATE"}`);

  /* =====================================================
   * STATE (Hybrid untuk CREATE & EDIT)
   * ===================================================== */
  const state = {
    existingImages: [], // Hanya dipakai di EDIT mode
    newFiles: [], // Dipakai di kedua mode
    removedImages: [], // Hanya dipakai di EDIT mode
  };

  /* =====================================================
   * INIT STATE (Detect existing images)
   * ===================================================== */
  function initState() {
    if (isEditMode) {
      const existingElements = preview.querySelectorAll(
        "[data-existing-image]",
      );
      state.existingImages = [...existingElements].map(
        (el) => el.dataset.existingImage,
      );
      console.log("Existing images found:", state.existingImages.length);
    }
  }

  /* =====================================================
   * HAS IMAGES
   * ===================================================== */
  function hasImages() {
    if (isEditMode) {
      // EDIT mode: check existing (minus removed) + new files
      const activeExisting = state.existingImages.filter(
        (img) => !state.removedImages.includes(img),
      );
      return activeExisting.length > 0 || state.newFiles.length > 0;
    } else {
      // CREATE mode: check only new files
      return state.newFiles.length > 0;
    }
  }

  /* =====================================================
   * UPDATE VISIBILITY
   * ===================================================== */
  function updateVisibility() {
    if (hasImages()) {
      preview.classList.remove("hidden");
      placeholder.classList.add("hidden");
    } else {
      preview.classList.add("hidden");
      placeholder.classList.remove("hidden");
    }
  }

  /* =====================================================
   * SYNC INPUT FILES
   * ===================================================== */
  function syncInputFiles() {
    const dataTransfer = new DataTransfer();
    state.newFiles.forEach((item) => {
      dataTransfer.items.add(item.file);
    });
    input.files = dataTransfer.files;
  }

  /* =====================================================
   * RENDER NEW PREVIEW
   * ===================================================== */
  function renderPreview(file, uid) {
    const reader = new FileReader();

    reader.onload = (e) => {
      const wrapper = document.createElement("div");
      wrapper.className = "relative group";
      wrapper.dataset.newId = uid;

      const img = document.createElement("img");
      img.src = e.target.result;
      img.className =
        "w-24 h-24 object-cover rounded-lg border border-gray-200";

      const removeBtn = document.createElement("button");
      removeBtn.type = "button";
      removeBtn.textContent = "✕";
      removeBtn.className =
        "absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full";

      removeBtn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();

        state.newFiles = state.newFiles.filter((item) => item.id !== uid);

        wrapper.remove();
        syncInputFiles();
        updateVisibility();
      });

      wrapper.appendChild(img);
      wrapper.appendChild(removeBtn);

      container.appendChild(wrapper);
    };

    reader.readAsDataURL(file);
  }

  /* =====================================================
   * HANDLE FILES
   * ===================================================== */
  function handleFiles(files) {
    const validFiles = [...files].filter((file) => {
      if (!file.type.startsWith("image/")) {
        alert(`${file.name} bukan file gambar`);
        return false;
      }

      if (file.size > 2 * 1024 * 1024) {
        alert(`${file.name} terlalu besar (max 2MB)`);
        return false;
      }

      if (state.existingImages.includes(file.name)) {
        alert(`${file.name} sudah ada`);
        return false;
      }

      if (
        state.newFiles.some(
          (item) =>
            item.file.name === file.name && item.file.size === file.size,
        )
      ) {
        alert(`${file.name} sudah ditambahkan`);
        return false;
      }

      return true;
    });

    validFiles.forEach((file) => {
      const uid = generateUID();

      state.newFiles.push({
        id: uid,
        file: file,
      });

      renderPreview(file, uid);
    });

    syncInputFiles();
    updateVisibility();
  }

  /* =====================================================
   * CLICK HANDLERS
   * ===================================================== */
  placeholder.addEventListener("click", (e) => {
    e.stopPropagation();
    input.click();
  });

  if (uploadBtn) {
    uploadBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      input.click();
    });
  }

  /* =====================================================
   * INPUT CHANGE
   * ===================================================== */
  input.addEventListener("change", (e) => {
    if (e.target.files.length > 0) {
      handleFiles(e.target.files);
    }
  });

  /* =====================================================
   * DRAG & DROP
   * ===================================================== */
  dropzone.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropzone.classList.add("border-blue-500", "bg-blue-50");
  });

  dropzone.addEventListener("dragleave", (e) => {
    e.preventDefault();
    dropzone.classList.remove("border-blue-500", "bg-blue-50");
  });

  dropzone.addEventListener("drop", (e) => {
    e.preventDefault();
    dropzone.classList.remove("border-blue-500", "bg-blue-50");

    if (e.dataTransfer.files.length > 0) {
      handleFiles(e.dataTransfer.files);
    }
  });

  /* =====================================================
   * REMOVE EXISTING IMAGE (EDIT MODE ONLY)
   * ===================================================== */
  if (isEditMode) {
    preview.querySelectorAll("[data-remove-existing]").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();

        const filename = btn.dataset.removeExisting;
        const wrapper = btn.closest("[data-existing-image]");

        // Update state
        state.removedImages.push(filename);

        // Remove hidden input
        const hidden = wrapper.querySelector(
          `input[name="existing_images[]"][value="${filename}"]`,
        );
        if (hidden) hidden.remove();

        // Create remove marker
        const removed = document.createElement("input");
        removed.type = "hidden";
        removed.name = "remove_images[]";
        removed.value = filename;
        dropzone.closest("form").appendChild(removed);

        // Remove from DOM
        wrapper.remove();

        // Update visibility
        updateVisibility();
      });
    });
  }

  /* =====================================================
   * INIT
   * ===================================================== */
  initState();
  updateVisibility();
});

// ==============================
// GLOBAL FUNCTIONS (WAJIB GLOBAL)
// ==============================
window.openLogoutModal = function (event) {
  event.preventDefault();

  const modal = document.getElementById("logoutModal");
  if (!modal) {
    console.warn("Logout modal not found");
    return;
  }

  modal.classList.remove("hidden");
  modal.classList.add("flex");
};

window.closeLogoutModal = function () {
  const modal = document.getElementById("logoutModal");
  if (!modal) return;

  modal.classList.add("hidden");
  modal.classList.remove("flex");
};

function openLogoutModal(event) {
  event.preventDefault();

  const modal = document.getElementById("logoutModal");
  if (!modal) return;

  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeLogoutModal() {
  const modal = document.getElementById("logoutModal");
  if (!modal) return;

  modal.classList.add("hidden");
  modal.classList.remove("flex");
}
