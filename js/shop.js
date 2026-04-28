/* ═══════════════════════════════════════════════════════════
   shop.js  —  Virunga Homestay Shop
   • Scroll reveal
   • Animated stat counters
   • Category filter + live search
   • Sort
   • Grid / List view toggle
   • 3-D tilt on cards
   • Quick-view modal
   • Cart drawer (add, remove, qty, clear, persist via localStorage)
   • Toast notifications
   • Wishlist toggle
   • Floating FAB cart badge
═══════════════════════════════════════════════════════════ */
(function () {
  "use strict";

  /* ──────────────────────────────────────────────────────────
     1. SCROLL REVEAL
  ────────────────────────────────────────────────────────── */
  const revealObs = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const delay = parseInt(el.dataset.delay || "0", 10);
        setTimeout(() => el.classList.add("is-visible"), delay);
        revealObs.unobserve(el);
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -48px 0px" },
  );

  document
    .querySelectorAll("[data-reveal]")
    .forEach((el) => revealObs.observe(el));

  /* ──────────────────────────────────────────────────────────
     2. STAT COUNTER ANIMATION
  ────────────────────────────────────────────────────────── */
  function countUp(el) {
    const target = parseInt(el.dataset.count, 10);
    const duration = 1600;
    let start = null;
    const step = (ts) => {
      if (!start) start = ts;
      const t = Math.min((ts - start) / duration, 1);
      const v = Math.round(target * (1 - Math.pow(1 - t, 3)));
      el.textContent = v;
      if (t < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  const statObs = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        countUp(entry.target);
        statObs.unobserve(entry.target);
      });
    },
    { threshold: 0.5 },
  );

  document
    .querySelectorAll("[data-count]")
    .forEach((el) => statObs.observe(el));

  /* ──────────────────────────────────────────────────────────
     3. CATEGORY FILTER
  ────────────────────────────────────────────────────────── */
  let activeFilter = "all";
  let searchQuery = "";
  let sortOrder = "default";

  const grid = document.getElementById("spGrid");
  const cards = Array.from(document.querySelectorAll(".sp-card"));
  const emptyState = document.getElementById("spEmpty");
  const resultsCount = document.getElementById("spResultsCount");

  function applyFilters() {
    let visible = 0;
    cards.forEach((card) => {
      const cat = card.dataset.category || "";
      const name = (card.dataset.name || "").toLowerCase();
      const price = parseFloat(card.dataset.price || "0");

      const matchCat = activeFilter === "all" || cat === activeFilter;
      const matchSearch =
        !searchQuery || name.includes(searchQuery.toLowerCase());

      if (matchCat && matchSearch) {
        card.removeAttribute("data-hidden");
        visible++;
      } else {
        card.setAttribute("data-hidden", "");
      }
    });

    /* Sort visible cards */
    const visibleCards = cards.filter((c) => !c.hasAttribute("data-hidden"));
    if (sortOrder === "price-asc") {
      visibleCards.sort(
        (a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price),
      );
    } else if (sortOrder === "price-desc") {
      visibleCards.sort(
        (a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price),
      );
    } else if (sortOrder === "name") {
      visibleCards.sort((a, b) =>
        (a.dataset.name || "").localeCompare(b.dataset.name || ""),
      );
    }
    visibleCards.forEach((c) => grid.appendChild(c));

    /* Empty state */
    emptyState.style.display = visible === 0 ? "block" : "none";
    resultsCount.innerHTML = `Showing <strong>${visible}</strong> product${visible !== 1 ? "s" : ""}`;
  }

  /* Filter buttons */
  document.querySelectorAll(".sp-filter-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document
        .querySelectorAll(".sp-filter-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      activeFilter = btn.dataset.filter;
      applyFilters();
    });
  });

  /* Search */
  const searchInput = document.getElementById("spSearch");
  const searchWrap = searchInput && searchInput.closest(".sp-search-wrap");
  const searchClear = document.getElementById("spSearchClear");

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      searchQuery = searchInput.value.trim();
      searchWrap.classList.toggle("has-value", searchQuery.length > 0);
      applyFilters();
    });
  }
  if (searchClear) {
    searchClear.addEventListener("click", () => {
      searchInput.value = "";
      searchQuery = "";
      searchWrap.classList.remove("has-value");
      applyFilters();
      searchInput.focus();
    });
  }

  /* Sort */
  const sortEl = document.getElementById("spSort");
  if (sortEl) {
    sortEl.addEventListener("change", () => {
      sortOrder = sortEl.value;
      applyFilters();
    });
  }

  /* ──────────────────────────────────────────────────────────
     4. VIEW TOGGLE (grid / list)
  ────────────────────────────────────────────────────────── */
  document.getElementById("viewGrid") &&
    document.getElementById("viewGrid").addEventListener("click", () => {
      grid.classList.remove("list-view");
      document.getElementById("viewGrid").classList.add("active");
      document.getElementById("viewList").classList.remove("active");
    });
  document.getElementById("viewList") &&
    document.getElementById("viewList").addEventListener("click", () => {
      grid.classList.add("list-view");
      document.getElementById("viewList").classList.add("active");
      document.getElementById("viewGrid").classList.remove("active");
    });

  /* ──────────────────────────────────────────────────────────
     5. 3-D CARD TILT
  ────────────────────────────────────────────────────────── */
  cards.forEach((card) => {
    card.addEventListener("mousemove", (e) => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      card.style.transform = `translateY(-7px) scale(1.01) rotateX(${-y * 7}deg) rotateY(${x * 7}deg)`;
      card.style.transition =
        "transform .08s linear, box-shadow .28s, border-color .28s";
    });
    card.addEventListener("mouseleave", () => {
      card.style.transform = "";
      card.style.transition =
        "transform .5s var(--ease-out), box-shadow .28s, border-color .28s";
    });
  });

  /* ──────────────────────────────────────────────────────────
     6. TOAST HELPER
  ────────────────────────────────────────────────────────── */
  const toastEl = document.getElementById("spToast");
  let toastTimer = null;

  function showToast(msg, icon = "fa-circle-check") {
    toastEl.innerHTML = `<i class="fa-solid ${icon}"></i> ${msg}`;
    toastEl.classList.add("is-visible");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove("is-visible"), 2800);
  }

  /* ──────────────────────────────────────────────────────────
     7. CART  (localStorage persistence)
  ────────────────────────────────────────────────────────── */
  let cart = JSON.parse(localStorage.getItem("virungaCart") || "[]");

  function saveCart() {
    localStorage.setItem("virungaCart", JSON.stringify(cart));
  }

  function cartTotal() {
    return cart.reduce((sum, item) => sum + item.price * item.qty, 0);
  }

  function renderCart() {
    const itemsEl = document.getElementById("spCartItems");
    const footerEl = document.getElementById("spCartFooter");
    const totalEl = document.getElementById("spCartTotal");
    const badgeEl = document.getElementById("spCartBadge");
    const fabEl = document.getElementById("spCartFab");

    const count = cart.reduce((s, i) => s + i.qty, 0);

    /* Badge */
    if (badgeEl) {
      badgeEl.textContent = count;
      badgeEl.style.display = count > 0 ? "flex" : "none";
    }

    if (!itemsEl) return;

    /* Empty / footer */
    const emptyCartEl = document.getElementById("spCartEmpty");
    if (emptyCartEl)
      emptyCartEl.style.display = cart.length === 0 ? "flex" : "none";
    if (footerEl) footerEl.style.display = cart.length === 0 ? "none" : "block";
    if (totalEl) totalEl.textContent = `$${cartTotal().toFixed(2)}`;

    /* Remove old dynamic items */
    itemsEl.querySelectorAll(".sp-cart-item").forEach((el) => el.remove());

    cart.forEach((item, idx) => {
      const row = document.createElement("div");
      row.className = "sp-cart-item";
      row.innerHTML = `
        <img src="${item.img}" alt="${item.name}" class="sp-cart-item__img">
        <div class="sp-cart-item__info">
          <div class="sp-cart-item__name">${item.name}</div>
          <div class="sp-cart-item__price">$${(item.price * item.qty).toFixed(2)}</div>
          <div class="sp-cart-item__qty">
            <button data-idx="${idx}" data-action="dec">−</button>
            <span>${item.qty}</span>
            <button data-idx="${idx}" data-action="inc">+</button>
          </div>
        </div>
        <button class="sp-cart-item__remove" data-idx="${idx}" aria-label="Remove">
          <i class="fa-solid fa-xmark"></i>
        </button>`;
      itemsEl.appendChild(row);
    });

    /* Qty / remove events (event delegation on container) */
    itemsEl.querySelectorAll("[data-action]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const i = parseInt(btn.dataset.idx, 10);
        if (btn.dataset.action === "inc") {
          cart[i].qty++;
        } else {
          cart[i].qty--;
          if (cart[i].qty <= 0) cart.splice(i, 1);
        }
        saveCart();
        renderCart();
      });
    });
    itemsEl.querySelectorAll(".sp-cart-item__remove").forEach((btn) => {
      btn.addEventListener("click", () => {
        cart.splice(parseInt(btn.dataset.idx, 10), 1);
        saveCart();
        renderCart();
        showToast("Item removed from cart", "fa-trash");
      });
    });
  }

  function addToCart(id, name, price, img) {
    const existing = cart.find((i) => i.id === id);
    if (existing) {
      existing.qty++;
    } else {
      cart.push({ id, name, price: parseFloat(price), img, qty: 1 });
    }
    saveCart();
    renderCart();
    showToast(`<strong>${name}</strong> added to cart`);

    /* Bounce the FAB */
    const fab = document.getElementById("spCartFab");
    if (fab) {
      fab.animate(
        [
          { transform: "scale(1)" },
          { transform: "scale(1.3)" },
          { transform: "scale(1)" },
        ],
        { duration: 350, easing: "cubic-bezier(.34,1.56,.64,1)" },
      );
    }
  }

  /* Initial render */
  renderCart();

  /* Add to cart — delegated on document */
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".sp-add-to-cart");
    if (!btn) return;
    addToCart(
      btn.dataset.id,
      btn.dataset.name,
      btn.dataset.price,
      btn.dataset.img,
    );
  });

  /* Clear cart */
  document.getElementById("spClearCart") &&
    document.getElementById("spClearCart").addEventListener("click", () => {
      cart = [];
      saveCart();
      renderCart();
      showToast("Cart cleared", "fa-trash");
    });

  /* Open cart drawer */
  function openCart() {
    document.getElementById("spCartDrawer").classList.add("is-open");
    document.getElementById("spCartOverlay").classList.add("is-open");
    document.body.style.overflow = "hidden";
  }
  function closeCart() {
    document.getElementById("spCartDrawer").classList.remove("is-open");
    document.getElementById("spCartOverlay").classList.remove("is-open");
    document.body.style.overflow = "";
  }

  document.getElementById("spCartFab") &&
    document.getElementById("spCartFab").addEventListener("click", openCart);
  document.getElementById("spCartClose") &&
    document.getElementById("spCartClose").addEventListener("click", closeCart);
  document.getElementById("spCartOverlay") &&
    document
      .getElementById("spCartOverlay")
      .addEventListener("click", closeCart);

  /* ──────────────────────────────────────────────────────────
     8. QUICK VIEW MODAL
  ────────────────────────────────────────────────────────── */
  const modalOverlay = document.getElementById("spModalOverlay");
  let modalQty = 1;
  let modalData = {};

  function openModal(data) {
    modalData = data;
    modalQty = 1;

    document.getElementById("spModalImg").src = data.img;
    document.getElementById("spModalImg").alt = data.name;
    document.getElementById("spModalCat").textContent = data.cat;
    document.getElementById("spModalTitle").textContent = data.name;
    document.getElementById("spModalDesc").textContent = data.desc;
    document.getElementById("spModalPrice").textContent =
      `$${parseFloat(data.price).toFixed(2)}`;
    document.getElementById("spQtyVal").textContent = "1";

    modalOverlay.classList.add("is-open");
    modalOverlay.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
  }
  function closeModal() {
    modalOverlay.classList.remove("is-open");
    modalOverlay.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  }

  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".sp-quick-view");
    if (!btn) return;
    openModal({
      id: btn.dataset.id,
      name: btn.dataset.name,
      price: btn.dataset.price,
      cat: btn.dataset.cat,
      img: btn.dataset.img,
      desc: btn.dataset.desc,
    });
  });

  document.getElementById("spModalClose") &&
    document
      .getElementById("spModalClose")
      .addEventListener("click", closeModal);
  modalOverlay &&
    modalOverlay.addEventListener("click", (e) => {
      if (e.target === modalOverlay) closeModal();
    });

  /* Qty stepper in modal */
  document.getElementById("spQtyMinus") &&
    document.getElementById("spQtyMinus").addEventListener("click", () => {
      if (modalQty > 1) {
        modalQty--;
        document.getElementById("spQtyVal").textContent = modalQty;
      }
    });
  document.getElementById("spQtyPlus") &&
    document.getElementById("spQtyPlus").addEventListener("click", () => {
      modalQty++;
      document.getElementById("spQtyVal").textContent = modalQty;
    });

  /* Add from modal */
  document.getElementById("spModalAdd") &&
    document.getElementById("spModalAdd").addEventListener("click", () => {
      for (let i = 0; i < modalQty; i++) {
        addToCart(modalData.id, modalData.name, modalData.price, modalData.img);
      }
      closeModal();
    });

  /* Keyboard close */
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal();
      closeCart();
    }
  });

  /* ──────────────────────────────────────────────────────────
     9. WISHLIST TOGGLE
  ────────────────────────────────────────────────────────── */
  let wishlist = JSON.parse(localStorage.getItem("virungaWishlist") || "[]");

  function syncWishlistUI() {
    document.querySelectorAll(".sp-wishlist").forEach((btn) => {
      const liked = wishlist.includes(btn.dataset.id);
      btn.classList.toggle("is-liked", liked);
      const icon = btn.querySelector("i");
      if (icon) {
        icon.className = liked ? "fa-solid fa-heart" : "fa-regular fa-heart";
        icon.style.color = liked ? "#E0614F" : "";
      }
    });
  }

  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".sp-wishlist");
    if (!btn) return;
    const id = btn.dataset.id;
    if (!id) return;
    const idx = wishlist.indexOf(id);
    if (idx === -1) {
      wishlist.push(id);
      showToast("Added to wishlist", "fa-heart");
    } else {
      wishlist.splice(idx, 1);
      showToast("Removed from wishlist", "fa-heart-crack");
    }
    localStorage.setItem("virungaWishlist", JSON.stringify(wishlist));
    syncWishlistUI();
  });

  syncWishlistUI();

  /* ──────────────────────────────────────────────────────────
     10. LOAD MORE (simulated — reveals hidden cards)
  ────────────────────────────────────────────────────────── */
  document.getElementById("spLoadMoreBtn") &&
    document
      .getElementById("spLoadMoreBtn")
      .addEventListener("click", function () {
        this.innerHTML =
          '<i class="fa-solid fa-circle-notch fa-spin"></i> Loading…';
        setTimeout(() => {
          this.innerHTML =
            '<i class="fa-solid fa-check"></i> All Products Loaded';
          this.disabled = true;
          this.style.opacity = ".5";
        }, 900);
      });
})();
