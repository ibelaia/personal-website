/* =====================================================
   PERSONAL WEBSITE — Ibe Laia
   Fitur:
   - dark mode toggle (di dalam dropdown)
   - reveal on scroll
   - hamburger menu (mobile — untuk daftar halaman)
   - dropdown menu-trigger (titik tiga: Login, Download CV, Theme, Language)
   - translate ID/EN — kamus terjemahan diambil dari file lang/en.json & lang/id.json
   - active nav link sesuai halaman aktif
   - filter gallery/blog
   - validasi & pengiriman form contact (fetch ke contact.php)
   ===================================================== */

/* ---------- SISTEM TERJEMAHAN (JSON) ---------- */
async function loadTranslations(lang) {
  try {
    // Parameter timestamp (?v=...) mencegah cache lama mengunci teks
    const res = await fetch(`lang/${lang}.json?v=${Date.now()}`);
    if (!res.ok) throw new Error(`Gagal memuat lang/${lang}.json (status ${res.status})`);
    return await res.json();
  } catch (err) {
    console.error("Translation load error:", err);
    return {};
  }
}

async function applyTranslations(lang) {
  const dict = await loadTranslations(lang);
  document.querySelectorAll("[data-i18n]").forEach(function (el) {
    const key = el.getAttribute("data-i18n");
    if (dict[key] !== undefined && dict[key] !== "") {
      if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
        el.value = dict[key];
      } else {
        el.textContent = dict[key];
      }
    }
  });
  document.documentElement.setAttribute("lang", lang);
}

document.addEventListener("DOMContentLoaded", async function () {

  /* ---------- TRANSLATE ID/EN (list dengan checkmark) ---------- */
  const langOptions = document.querySelectorAll(".lang-option");
  let currentLang = localStorage.getItem("lang") || "en";

  function updateLangSelection() {
    langOptions.forEach(function (btn) {
      btn.classList.toggle("selected", btn.getAttribute("data-lang") === currentLang);
    });
  }

  // Terapkan bahasa awal saat halaman selesai dimuat
  await applyTranslations(currentLang);
  updateLangSelection();

  // Event listener tombol pilihan bahasa: langsung perbarui teks seketika
  langOptions.forEach(function (btn) {
    btn.addEventListener("click", async function () {
      currentLang = btn.getAttribute("data-lang");
      localStorage.setItem("lang", currentLang);
      await applyTranslations(currentLang);
      updateLangSelection();
    });
  });

  /* ---------- THEME: White / Dark (list dengan checkmark) ---------- */
  const themeOptions = document.querySelectorAll(".theme-option");
  const savedTheme = localStorage.getItem("theme") || "light";

  function applyTheme(themeName) {
    document.body.classList.toggle("dark", themeName === "dark");
    themeOptions.forEach(function (btn) {
      btn.classList.toggle("selected", btn.getAttribute("data-theme") === themeName);
    });
  }

  applyTheme(savedTheme);

  themeOptions.forEach(function (btn) {
    btn.addEventListener("click", function () {
      const themeName = btn.getAttribute("data-theme");
      localStorage.setItem("theme", themeName);
      applyTheme(themeName);
    });
  });

  /* ---------- DROPDOWN PANEL (titik tiga: Login/Download/Theme/Language) ---------- */
  const menuTrigger = document.getElementById("menuTrigger");
  const dropdownPanel = document.getElementById("dropdownPanel");

  if (menuTrigger && dropdownPanel) {
    menuTrigger.addEventListener("click", function (e) {
      e.stopPropagation();
      const isOpen = dropdownPanel.classList.toggle("open");
      menuTrigger.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    document.addEventListener("click", function (e) {
      if (!dropdownPanel.contains(e.target) && e.target !== menuTrigger) {
        dropdownPanel.classList.remove("open");
        menuTrigger.setAttribute("aria-expanded", "false");
      }
    });
  }

  /* ---------- REVEAL ON SCROLL ---------- */
  const reveals = document.querySelectorAll(".reveal");

  function handleReveal() {
    const windowHeight = window.innerHeight;
    reveals.forEach(function (el) {
      const elementTop = el.getBoundingClientRect().top;
      if (elementTop < windowHeight - 100) {
        el.classList.add("active");
      }
    });
  }
  window.addEventListener("scroll", handleReveal);
  handleReveal();

  /* ---------- HAMBURGER MENU (daftar halaman, mobile) ---------- */
  const hamburger = document.getElementById("hamburger");
  const menu = document.querySelector(".menu");

  if (hamburger && menu) {
    hamburger.addEventListener("click", function () {
      menu.classList.toggle("open");
      hamburger.setAttribute(
        "aria-expanded",
        menu.classList.contains("open") ? "true" : "false"
      );
    });

    menu.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        menu.classList.remove("open");
      });
    });
  }

  /* ---------- ACTIVE NAV LINK ---------- */
  const currentPage = window.location.pathname.split("/").pop() || "index.php";
  document.querySelectorAll(".menu a").forEach(function (link) {
    const linkPage = link.getAttribute("href");
    if (linkPage === currentPage) {
      link.classList.add("active");
    }
  });

  /* ---------- GALLERY / BLOG FILTER ---------- */
  const filterButtons = document.querySelectorAll(".filter-bar button");
  const filterItems = document.querySelectorAll("[data-category]");

  if (filterButtons.length && filterItems.length) {
    filterButtons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        filterButtons.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");

        const category = btn.getAttribute("data-filter");
        filterItems.forEach(function (item) {
          const show = category === "all" || item.getAttribute("data-category") === category;
          item.style.display = show ? "" : "none";
        });
      });
    });
  }

  /* ---------- CONTACT FORM ---------- */
  const contactForm = document.querySelector(".contact-form");

  if (contactForm) {
    const statusBox = document.querySelector(".form-status");

    contactForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(contactForm);
      const submitBtn = contactForm.querySelector("button[type='submit']");
      submitBtn.disabled = true;
      submitBtn.textContent = "Sending...";

      try {
        const response = await fetch("contact.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();

        if (statusBox) {
          statusBox.textContent = result.message;
          statusBox.className = "form-status " + (result.success ? "success" : "error");
        }

        if (result.success) {
          contactForm.reset();
        }
      } catch (err) {
        if (statusBox) {
          statusBox.textContent = "A network error occurred. Please try again.";
          statusBox.className = "form-status error";
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = "Send Message";
      }
    });
  }

});