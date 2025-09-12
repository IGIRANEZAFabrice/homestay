document.addEventListener("DOMContentLoaded", function () {
  // Get product data from the DOM
  const getProductData = (id) => {
    const productCard = document.querySelector(`[data-product-id="${id}"]`);
    if (!productCard) return null;

    const nameElement = productCard.querySelector(".product-details h3");
    const priceElement = productCard.querySelector(".price");
    const imageElement = productCard.querySelector(".product-image img");
    const descriptionElement = productCard.querySelector(".product-description");
    const badgeElement = productCard.querySelector(".product-badge");

    if (!nameElement || !priceElement || !imageElement || !descriptionElement) return null;

    return {
      id: id,
      name: nameElement.textContent,
      price: parseFloat(priceElement.textContent.replace("$", "")),
      image: imageElement.src,
      description: descriptionElement.textContent,
      tag: badgeElement?.textContent || "",
    };
  };

  // DOM Elements
  const searchInput = document.getElementById("searchInput");
  const categoryFilter = document.getElementById("categoryFilter");
  const priceFilter = document.getElementById("priceFilter");
  const sortBy = document.getElementById("sortBy");
  const productsGrid = document.querySelector(".products-grid");

  // Filter functionality
  function filterProducts() {
    if (!searchInput || !categoryFilter || !priceFilter || !sortBy || !productsGrid) return;
    
    const searchTerm = searchInput.value.toLowerCase();
    const category = categoryFilter.value;
    const price = priceFilter.value;
    const sort = sortBy.value;

    // Get all product cards
    const productCards = Array.from(productsGrid.children);
    
    // Filter products based on search term
    productCards.forEach(card => {
      const productName = card.querySelector("h3")?.textContent.toLowerCase() || "";
      const shouldShow = productName.includes(searchTerm);
      card.style.display = shouldShow ? "" : "none";
    });
  }

  // Event listeners for filters
  if (searchInput) searchInput.addEventListener("input", filterProducts);
  if (categoryFilter) categoryFilter.addEventListener("change", filterProducts);
  if (priceFilter) priceFilter.addEventListener("change", filterProducts);
  if (sortBy) sortBy.addEventListener("change", filterProducts);

  // Add hover effects to product cards
  const productCards = document.querySelectorAll('.product-card');
  if (productCards && productCards.length > 0) {
    productCards.forEach(card => {
      if (card) {
        card.addEventListener('mouseenter', () => {
          card.classList.add('hover');
        });
        card.addEventListener('mouseleave', () => {
          card.classList.remove('hover');
        });
      }
    });
  }
});
