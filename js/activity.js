document.addEventListener('DOMContentLoaded', () => {
  const page = document.getElementById('activity-page');
  if (!page) return;

  const buttons = Array.from(document.querySelectorAll('.activity-choice'));
  const previewTitle = document.getElementById('activityPreviewTitle');
  const previewBody = document.getElementById('activityPreviewBody');
  const previewTime = document.getElementById('activityPreviewTime');
  const previewGroup = document.getElementById('activityPreviewGroup');
  const previewImage = document.getElementById('activityPreviewImage');

  if (buttons.length && previewTitle && previewBody && previewTime && previewGroup && previewImage) {
    const setActive = (button) => {
      buttons.forEach((btn) => btn.classList.remove('is-active'));
      button.classList.add('is-active');

      const title = button.dataset.title || '';
      const body = button.dataset.body || '';
      const time = button.dataset.time || '';
      const group = button.dataset.group || '';
      const image = button.dataset.image || '';

      previewTitle.textContent = title;
      previewBody.textContent = body;
      previewTime.innerHTML = '<i class="fa-solid fa-clock"></i> ' + time;
      previewGroup.innerHTML = '<i class="fa-solid fa-users"></i> ' + group;

      if (image) previewImage.src = image;
    };

    buttons.forEach((button) => {
      button.addEventListener('click', () => setActive(button));
    });
  }

  const cards = Array.from(document.querySelectorAll('#activities .room-card'));
  const showMoreBtn = document.getElementById('activityShowMore');
  const PAGE_SIZE = 9;
  let visibleLimit = PAGE_SIZE;

  const renderCards = () => {
    cards.forEach((card, index) => {
      card.style.display = index < visibleLimit ? '' : 'none';
    });

    if (showMoreBtn) {
      showMoreBtn.style.display = visibleLimit >= cards.length ? 'none' : '';
    }
  };

  if (showMoreBtn) {
    showMoreBtn.addEventListener('click', () => {
      visibleLimit += PAGE_SIZE;
      renderCards();
    });
  }

  renderCards();
});
