// Sample room data for client display
const rooms = [
  {
    name: 'Deluxe Suite',
    type: 'Deluxe Suite',
    price: 120,
    status: 'Available',
    image: '../img/rooms/deluxe.jpg'
  },
  {
    name: 'Standard Room',
    type: 'Standard Room',
    price: 80,
    status: 'Occupied',
    image: '../img/rooms/standard.jpg'
  },
  {
    name: 'Family Room',
    type: 'Family Room',
    price: 150,
    status: 'Available',
    image: '../img/rooms/family.jpg'
  },
  {
    name: 'Single Room',
    type: 'Single Room',
    price: 60,
    status: 'Maintenance',
    image: '../img/rooms/single.jpg'
  }
];

function getStatusClass(status) {
  switch (status) {
    case 'Available': return 'room-status';
    case 'Occupied': return 'room-status occupied';
    case 'Maintenance': return 'room-status maintenance';
    default: return 'room-status';
  }
}

function renderRooms() {
  const grid = document.getElementById('rooms-grid');
  grid.innerHTML = '';
  rooms.forEach(room => {
    const card = document.createElement('div');
    card.className = 'room-card';
    card.innerHTML = `
      <div class="room-image">
        <img src="${room.image}" alt="${room.name}">
      </div>
      <div class="room-content">
        <h2>${room.name}</h2>
        <div class="room-type">${room.type}</div>
        <div class="room-price">$${room.price} / night</div>
        <span class="${getStatusClass(room.status)}">${room.status}</span>
      </div>
    `;
    card.style.opacity = 0;
    grid.appendChild(card);
    setTimeout(() => { card.style.opacity = 1; card.style.transition = 'opacity 0.7s'; }, 100);
  });
}

document.addEventListener('DOMContentLoaded', renderRooms); 