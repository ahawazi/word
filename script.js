document.addEventListener("DOMContentLoaded", function () {
  // انتخاب رنگ محصول
  const colorOptions = document.querySelectorAll(".color-option input");
  colorOptions.forEach((option) => {
    option.addEventListener("change", function () {
      // نمایش تیک برای آیتم انتخاب شده
      const colorCircles = document.querySelectorAll(".color-circle");
      colorCircles.forEach((circle) => {
        circle.querySelector("i").style.display = "none";
      });

      if (this.checked) {
        const selectedCircle = this.nextElementSibling;
        selectedCircle.querySelector("i").style.display = "block";
      }
    });
  });

  // تصاویر بندانگشتی گالری
  const thumbnails = document.querySelectorAll(".thumbnail");
  const mainImage = document.querySelector(".main-image img");

  thumbnails.forEach((thumb) => {
    thumb.addEventListener("click", function () {
      // حذف کلاس active از همه
      thumbnails.forEach((t) => t.classList.remove("active"));

      // اضافه کردن کلاس active به آیتم کلیک شده
      this.classList.add("active");

      // تغییر تصویر اصلی
      const imgSrc = this.querySelector("img").src;
      mainImage.src = imgSrc;
    });
  });

  // دکمه‌های اکشن محصول (علاقه‌مندی، اشتراک‌گذاری و...)
  const actionButtons = document.querySelectorAll(
    ".wishlist-share button, .action-btn"
  );
  actionButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      // افکت کلیک
      this.classList.add("clicked");
      setTimeout(() => {
        this.classList.remove("clicked");
      }, 200);

      // می‌توان اینجا عملکرد خاصی برای هر دکمه اضافه کرد
      console.log("Action button clicked:", this.className);
    });
  });

  // دکمه افزودن به سبد خرید
  const addToCartBtn = document.querySelector(".cart-btn");
  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", function () {
      showNotification("محصول به سبد خرید اضافه شد");
    });
  }

  // دکمه‌های افزودن به سبد خرید کوچک
  const addToCartMiniBtns = document.querySelectorAll(".add-to-cart-mini");
  addToCartMiniBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const productCard = this.closest(".product-card");
      const productName =
        productCard.querySelector(".product-name").textContent;
      showNotification(`${productName} به سبد خرید اضافه شد`);
    });
  });

  // دکمه‌های اضافه کردن در جدول
  const addBtns = document.querySelectorAll(".add-btn");
  addBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const row = this.closest(".table-row");
      const productName = row.querySelectorAll(".cell")[1].textContent;
      showNotification(`${productName} به سبد خرید اضافه شد`);
    });
  });

  // پاجیناسیون
  const pageNumbers = document.querySelectorAll(".page-number");
  pageNumbers.forEach((page) => {
    page.addEventListener("click", function (e) {
      e.preventDefault();
      // حذف کلاس active از همه صفحات
      pageNumbers.forEach((p) => p.classList.remove("active"));
      // اضافه کردن کلاس active به صفحه کلیک شده
      this.classList.add("active");
      // معمولا اینجا محتوای صفحه انتخاب شده بارگذاری می‌شود
      console.log("صفحه انتخاب شده:", this.textContent);
    });
  });

  // دکمه افزودن لوازم جانبی
  const addAccessoryBtn = document.querySelector(".add-accessory");
  if (addAccessoryBtn) {
    addAccessoryBtn.addEventListener("click", function () {
      this.textContent = "افزوده شد ✓";
      this.style.backgroundColor = "var(--success-color)";
      this.style.color = "white";
      showNotification("لوازم جانبی به سبد خرید اضافه شد");
    });
  }

  // تب‌های محصول
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      // حذف کلاس active از همه تب‌ها
      tabButtons.forEach((b) => b.classList.remove("active"));

      // اضافه کردن کلاس active به تب کلیک شده
      this.classList.add("active");

      // نمایش محتوای مربوط به تب
      const tabId = this.getAttribute("data-tab");
      tabContents.forEach((content) => {
        content.style.display = "none";
      });

      if (document.getElementById(tabId)) {
        document.getElementById(tabId).style.display = "block";
      }
    });
  });

  // فرم خبرنامه
  const newsletterForm = document.querySelector(".newsletter-form");
  if (newsletterForm) {
    newsletterForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const input = this.querySelector("input");
      if (input.value.trim() !== "") {
        showNotification("ایمیل شما با موفقیت ثبت شد");
        input.value = "";
      } else {
        showNotification("لطفا ایمیل خود را وارد کنید", "error");
      }
    });
  }

  // دکمه برگشت به بالا
  const backToTop = document.querySelector(".back-to-top");
  if (backToTop) {
    backToTop.addEventListener("click", function (e) {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  // منوی موبایل
  const createMobileMenu = () => {
    if (window.innerWidth <= 768) {
      const navMenu = document.querySelector(".main-nav ul");
      if (navMenu && !document.querySelector(".mobile-menu-toggle")) {
        const menuToggle = document.createElement("button");
        menuToggle.className = "mobile-menu-toggle";
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';

        const headerTop = document.querySelector(".header-top");
        headerTop.appendChild(menuToggle);

        navMenu.style.display = "none";
        navMenu.style.flexDirection = "column";

        menuToggle.addEventListener("click", function () {
          if (navMenu.style.display === "none") {
            navMenu.style.display = "flex";
          } else {
            navMenu.style.display = "none";
          }
        });
      }
    }
  };

  // اجرای اولیه و اضافه کردن لیسنر برای تغییر سایز
  createMobileMenu();
  window.addEventListener("resize", createMobileMenu);

  // نمایش اعلان
  function showNotification(message, type = "success") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // نمایش اعلان با انیمیشن
    setTimeout(() => {
      notification.classList.add("show");
    }, 10);

    // حذف اعلان بعد از چند ثانیه
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        document.body.removeChild(notification);
      }, 300);
    }, 3000);
  }

  // ایجاد استایل برای اعلان
  const notificationStyle = document.createElement("style");
  notificationStyle.innerHTML = `
        .notification {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: var(--success-color);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .notification.error {
            background-color: #e53e3e;
        }
        
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .clicked {
            transform: scale(0.95);
        }
    `;
  document.head.appendChild(notificationStyle);
});
