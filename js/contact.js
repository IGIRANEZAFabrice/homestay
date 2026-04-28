document.addEventListener("DOMContentLoaded", () => {
  // Scroll reveal
  const revealEls = document.querySelectorAll(".reveal");
  if (revealEls.length) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -40px 0px" },
    );
    revealEls.forEach((el) => observer.observe(el));
  }

  // Select floating label
  document.querySelectorAll("select").forEach((sel) => {
    sel.addEventListener("change", () => {
      sel.classList.toggle("has-value", sel.value !== "");
    });
  });

  // Date placeholder styling
  document.querySelectorAll('input[type="date"]').forEach((input) => {
    input.addEventListener("focus", () => {
      if (!input.value) input.classList.add("has-value-date");
    });
    input.addEventListener("blur", () => {
      if (!input.value) input.classList.remove("has-value-date");
    });
  });

  // Form validation + success swap
  const form = document.getElementById("contactForm");
  const formSuccess = document.getElementById("formSuccess");

  const validate = () => {
    let ok = true;
    const required = [
      { id: "fname", check: (v) => v.trim().length > 1 },
      { id: "lname", check: (v) => v.trim().length > 1 },
      { id: "email", check: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) },
      { id: "subject", check: (v) => v !== "" },
      { id: "message", check: (v) => v.trim().length > 5 },
    ];
    required.forEach(({ id, check }) => {
      const el = document.getElementById(id);
      if (!el) return;
      const grp = el.closest(".form-group");
      if (!check(el.value)) {
        grp?.classList.add("error");
        ok = false;
      } else {
        grp?.classList.remove("error");
      }
    });

    const consent = document.getElementById("consent");
    if (consent) {
      if (!consent.checked) {
        ok = false;
        const wrap = consent.closest(".checkbox-group");
        if (wrap) wrap.style.outline = "1.5px solid #E05A4A";
      } else {
        const wrap = consent.closest(".checkbox-group");
        if (wrap) wrap.style.outline = "none";
      }
    }
    return ok;
  };

  if (form) {
    ["fname", "lname", "email", "subject", "message"].forEach((id) => {
      const el = document.getElementById(id);
      el?.addEventListener("blur", validate);
      el?.addEventListener("input", () => {
        el.closest(".form-group")?.classList.remove("error");
      });
    });

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!validate()) return;

      const btn = form.querySelector(".btn-submit");
      if (btn instanceof HTMLButtonElement) {
        btn.classList.add("btn-loading");
        btn.disabled = true;
      }

      const formData = new FormData(form);
      const name = (formData.get('fname') || '') + ' ' + (formData.get('lname') || '');
      formData.set('name', name.trim());
      formData.set('source', 'Contact Form: ' + (formData.get('subject') || 'General'));

      // Auto-resolve API path (assuming /homestayV2/ is base)
      const apiPath = window.location.pathname.includes('/homestayV2/') ? '/homestayV2/api/send_mail.php' : '/api/send_mail.php';

      fetch(apiPath, {
          method: 'POST',
          body: formData
      }).then(r => r.json()).then(data => {
          if(data.status === 'success') {
              form.style.display = "none";
              if (formSuccess) formSuccess.style.display = "flex";
          } else {
              alert("Error: " + data.message);
              if (btn instanceof HTMLButtonElement) {
                  btn.classList.remove("btn-loading");
                  btn.disabled = false;
              }
          }
      }).catch(err => {
          alert("Network error. Please try WhatsApp directly.");
          if (btn instanceof HTMLButtonElement) {
              btn.classList.remove("btn-loading");
              btn.disabled = false;
          }
      });
    });
  }

  // FAQ accordion
  document.querySelectorAll(".faq-q").forEach((btn) => {
    btn.addEventListener("click", () => {
      const item = btn.closest(".faq-item");
      const isOpen = item?.classList.contains("open");
      document.querySelectorAll(".faq-item.open").forEach((i) => i.classList.remove("open"));
      if (!isOpen) item?.classList.add("open");
    });
  });
});
