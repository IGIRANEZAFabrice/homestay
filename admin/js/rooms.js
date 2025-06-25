// DOM Elements
const roomModal = document.getElementById('roomModal');
const openAddRoomModalBtn = document.getElementById('openAddRoomModal');
const closeRoomModalBtn = document.getElementById('closeRoomModal');
const cancelRoomModalBtn = document.getElementById('cancelRoomModal');
const roomForm = document.getElementById('roomForm');
const modalTitle = document.getElementById('modalTitle');
const roomIdInput = document.getElementById('roomId');
const roomNameInput = document.getElementById('roomName');
const roomDescriptionInput = document.getElementById('roomDescription');
const roomImageInput = document.getElementById('roomImage');
const currentImageInput = document.getElementById('currentImage');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');
const imagePreview = document.getElementById('imagePreview');

// No need for renderRooms function as PHP handles the rendering

function openModal(isEdit = false, room = null) {
  roomModal.classList.add('show');
  document.body.style.overflow = 'hidden';
  
  // Reset image preview
  imagePreviewContainer.style.display = 'none';
  imagePreview.src = '';
  currentImageInput.value = '';
  
  if (isEdit && room) {
    modalTitle.textContent = 'Edit Room';
    roomIdInput.value = room.id;
    roomNameInput.value = room.title;
    roomDescriptionInput.value = room.description;
    
    // Set current image and show preview
    if (room.image) {
      currentImageInput.value = room.image;
      imagePreview.src = '../../uploads/rooms/' + room.image;
      imagePreview.onerror = function() {
        this.src = '../../img/placeholder-room.jpg';
      };
      imagePreviewContainer.style.display = 'block';
    }
  } else {
    modalTitle.textContent = 'Add Room';
    roomForm.reset();
    roomIdInput.value = '';
  }
}

function closeModal() {
  roomModal.classList.remove('show');
  document.body.style.overflow = '';
}

openAddRoomModalBtn.addEventListener('click', () => openModal(false));
closeRoomModalBtn.addEventListener('click', closeModal);
cancelRoomModalBtn.addEventListener('click', closeModal);

// Edit room function - called from the PHP-generated HTML
window.editRoom = function(id, title, description, image) {
  openModal(true, { id, title, description, image });
};

// Handle image preview when a new image is selected
roomImageInput.addEventListener('change', function() {
  if (this.files && this.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      imagePreview.src = e.target.result;
      imagePreviewContainer.style.display = 'block';
    };
    reader.readAsDataURL(this.files[0]);
  }
});

// Modal close on outside click
window.addEventListener('click', function(e) {
  if (e.target === roomModal) closeModal();
});

// Show success messages for 3 seconds then fade out
document.addEventListener('DOMContentLoaded', function() {
  const alerts = document.querySelectorAll('.alert');
  if (alerts.length > 0) {
    setTimeout(() => {
      alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.style.display = 'none';
        }, 500);
      });
    }, 3000);
  }
});