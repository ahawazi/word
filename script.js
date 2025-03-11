document.addEventListener("DOMContentLoaded", function () {
  // Color options selection
  const colorOptions = document.querySelectorAll(".color-option");
  colorOptions.forEach((option) => {
    option.addEventListener("click", function () {
      // Remove selected class from all options
      colorOptions.forEach(
        (opt) => (opt.style.border = "2px solid transparent")
      );
      // Add selected class to clicked option
      this.style.border = "2px solid #f62255";
    });
  });

  // Product options (checkboxes)
  const productOptions = document.querySelectorAll(".option-item input");
  productOptions.forEach((option) => {
    option.addEventListener("change", function () {
      // You can add price calculation logic here
      console.log("Option changed:", this.id, "checked:", this.checked);
    });
  });

  // Add to cart button
  const addToCartBtn = document.querySelector(".cart-btn");
  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", function () {
      alert("محصول به سبد خرید اضافه شد");
    });
  }

  // Add to cart mini buttons
  const addToCartMiniBtns = document.querySelectorAll(".add-to-cart-mini");
  addToCartMiniBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const productCard = this.closest(".product-card");
      const productName =
        productCard.querySelector(".product-name").textContent;
      alert(`${productName} به سبد خرید اضافه شد`);
    });
  });

  // Add button in table rows
  const addBtns = document.querySelectorAll(".add-btn");
  addBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const row = this.closest(".table-row");
      const productName = row.querySelectorAll(".cell")[1].textContent;
      alert(`${productName} به سبد خرید اضافه شد`);
    });
  });

  // Pagination
  const pageNumbers = document.querySelectorAll(".page-number");
  pageNumbers.forEach((page) => {
    page.addEventListener("click", function (e) {
      e.preventDefault();
      // Remove active class from all pages
      pageNumbers.forEach((p) => p.classList.remove("active"));
      // Add active class to clicked page
      this.classList.add("active");
      // Here you would typically load the content for the selected page
      console.log("Page selected:", this.textContent);
    });
  });

  // Mobile menu toggle (for responsive design)
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

  // Initial call and resize listener
  createMobileMenu();
  window.addEventListener("resize", createMobileMenu);
});
