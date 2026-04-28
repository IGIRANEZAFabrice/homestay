document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('blogGrid');
  if (!grid) return;

  const cards = Array.from(grid.querySelectorAll('.blog-card'));
  const filters = Array.from(document.querySelectorAll('.blog-filter'));
  const search = document.getElementById('blogSearch');
  const empty = document.getElementById('blogEmpty');
  const showMoreBtn = document.getElementById('blogShowMore');

  const PAGE_SIZE = 6;
  let activeFilter = 'all';
  let visibleLimit = PAGE_SIZE;

  const render = () => {
    const term = (search?.value || '').trim().toLowerCase();

    const matched = cards.filter((card) => {
      const category = (card.getAttribute('data-category') || '').toLowerCase();
      const title = (card.getAttribute('data-title') || '').toLowerCase();
      const text = card.textContent.toLowerCase();

      const matchesCategory = activeFilter === 'all' || category === activeFilter;
      const matchesSearch = term === '' || title.includes(term) || text.includes(term);
      return matchesCategory && matchesSearch;
    });

    cards.forEach((card) => card.setAttribute('data-hidden', 'true'));
    matched.forEach((card, idx) => {
      card.setAttribute('data-hidden', idx < visibleLimit ? 'false' : 'true');
    });

    if (empty) empty.hidden = matched.length !== 0;

    if (showMoreBtn) {
      const hasMore = matched.length > visibleLimit;
      showMoreBtn.hidden = !hasMore;
    }
  };

  filters.forEach((btn) => {
    btn.addEventListener('click', () => {
      filters.forEach((el) => el.classList.remove('is-active'));
      btn.classList.add('is-active');
      activeFilter = btn.getAttribute('data-filter') || 'all';
      visibleLimit = PAGE_SIZE;
      render();
    });
  });

  if (search) {
    search.addEventListener('input', () => {
      visibleLimit = PAGE_SIZE;
      render();
    });
  }

  if (showMoreBtn) {
    showMoreBtn.addEventListener('click', () => {
      visibleLimit += PAGE_SIZE;
      render();
    });
  }

  render();
});
