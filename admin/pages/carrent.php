<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Car Rentals - Admin Panel</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <div class="page-header">
            <h1>Manage Car Rentals</h1>
            <button class="btn-primary" onclick="openCarModal()">
                <i class="fas fa-plus"></i> Add New Car
            </button>
        </div>

        <div class="cars-grid" id="carsGrid">
            <!-- Car cards will be dynamically added here -->
        </div>
    </div>

    <!-- Car Modal (Add/Edit) -->
    <div id="carModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Car</h2>
                <button class="close-modal" onclick="closeCarModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="carForm">
                    <div class="form-group">
                        <label for="carName">Car Name</label>
                        <input type="text" id="carName" name="carName" required>
                    </div>
                    <div class="form-group">
                        <label for="carType">Vehicle Type</label>
                        <select id="carType" name="carType" required>
                            <option value="suv">SUV</option>
                            <option value="sedan">Sedan</option>
                            <option value="luxury">Luxury</option>
                            <option value="4x4">4x4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transmission">Transmission</label>
                        <select id="transmission" name="transmission" required>
                            <option value="automatic">Automatic</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fuelType">Fuel Type</label>
                        <select id="fuelType" name="fuelType" required>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="electric">Electric</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price per Day ($)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="carImage">Car Image URL</label>
                        <input type="text" id="carImage" name="carImage" required>
                        <div class="image-preview" id="carImagePreview"></div>
                    </div>
                    <div class="form-group">
                        <label>Features</label>
                        <div class="features-grid">
                            <label class="checkbox-label">
                                <input type="checkbox" name="features" value="ac"> Air Conditioning
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="features" value="audio"> Audio System
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="features" value="wifi"> WiFi
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="features" value="bluetooth"> Bluetooth
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="features" value="climate"> Climate Control
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="features" value="premium"> Premium Audio
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="badge">Badge (Optional)</label>
                        <select id="badge" name="badge">
                            <option value="">No Badge</option>
                            <option value="popular">Popular</option>
                            <option value="new">New</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Car</button>
                        <button type="button" class="btn-secondary" onclick="closeCarModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
                <button class="close-modal" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this car? This action cannot be undone.</p>
                <div class="modal-actions">
                    <button class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                    <button class="btn-danger" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cars = [
            {
                id: 1,
                name: "Toyota Land Cruiser",
                type: "suv",
                transmission: "automatic",
                fuelType: "diesel",
                price: 150,
                image: "../img/cars/landcruiser.jpg",
                features: ["ac", "audio", "wifi"],
                badge: "popular"
            },
            {
                id: 2,
                name: "Toyota Corolla",
                type: "sedan",
                transmission: "automatic",
                fuelType: "petrol",
                price: 80,
                image: "../img/cars/corolla.jpg",
                features: ["ac", "audio", "bluetooth"],
                badge: ""
            }
        ];

        let carToEdit = null;
        let carToDelete = null;

        function renderCars() {
            const grid = document.getElementById('carsGrid');
            grid.innerHTML = '';

            cars.forEach(car => {
                const card = document.createElement('div');
                card.className = 'car-card';
                card.innerHTML = `
                    <div class="car-image">
                        <img src="${car.image}" alt="${car.name}">
                        ${car.badge ? `<div class="car-badge">${car.badge}</div>` : ''}
                    </div>
                    <div class="car-details">
                        <h3>${car.name}</h3>
                        <div class="car-specs">
                            <span><i class="fas fa-car"></i> ${car.type.toUpperCase()}</span>
                            <span><i class="fas fa-cog"></i> ${car.transmission}</span>
                            <span><i class="fas fa-gas-pump"></i> ${car.fuelType}</span>
                        </div>
                        <div class="car-features">
                            ${car.features.map(feature => {
                                const icons = {
                                    ac: 'snowflake',
                                    audio: 'music',
                                    wifi: 'wifi',
                                    bluetooth: 'bluetooth',
                                    climate: 'temperature-low',
                                    premium: 'music'
                                };
                                return `<span><i class="fas fa-${icons[feature]}"></i> ${feature}</span>`;
                            }).join('')}
                        </div>
                        <div class="car-price">
                            <span class="price">$${car.price}</span>
                            <span class="period">/day</span>
                        </div>
                        <div class="car-actions">
                            <button onclick="editCar(${car.id})" class="btn-edit" data-tooltip="Edit Car">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="openDeleteModal(${car.id})" class="btn-delete" data-tooltip="Delete Car">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function openCarModal() {
            document.getElementById('modalTitle').textContent = 'Add New Car';
            document.getElementById('carForm').reset();
            document.getElementById('carImagePreview').innerHTML = '';
            carToEdit = null;
            document.getElementById('carModal').style.display = 'flex';
        }

        function closeCarModal() {
            document.getElementById('carModal').style.display = 'none';
        }

        function editCar(id) {
            const car = cars.find(c => c.id === id);
            if (car) {
                carToEdit = car;
                document.getElementById('modalTitle').textContent = 'Edit Car';
                document.getElementById('carName').value = car.name;
                document.getElementById('carType').value = car.type;
                document.getElementById('transmission').value = car.transmission;
                document.getElementById('fuelType').value = car.fuelType;
                document.getElementById('price').value = car.price;
                document.getElementById('carImage').value = car.image;
                document.getElementById('badge').value = car.badge;
                
                // Reset and check features
                document.querySelectorAll('input[name="features"]').forEach(checkbox => {
                    checkbox.checked = car.features.includes(checkbox.value);
                });

                // Show image preview
                const preview = document.getElementById('carImagePreview');
                preview.innerHTML = `<img src="${car.image}" alt="Preview">`;

                document.getElementById('carModal').style.display = 'flex';
            }
        }

        function openDeleteModal(id) {
            carToDelete = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            carToDelete = null;
        }

        function confirmDelete() {
            if (carToDelete !== null) {
                cars = cars.filter(car => car.id !== carToDelete);
                renderCars();
                closeDeleteModal();
            }
        }

        // Image preview
        document.getElementById('carImage').addEventListener('input', function(e) {
            const imageUrl = e.target.value;
            const preview = document.getElementById('carImagePreview');
            if (imageUrl) {
                preview.innerHTML = `<img src="${imageUrl}" alt="Preview">`;
            } else {
                preview.innerHTML = '';
            }
        });

        // Form submission
        document.getElementById('carForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const features = Array.from(formData.getAll('features'));
            
            const carData = {
                id: carToEdit ? carToEdit.id : Date.now(),
                name: formData.get('carName'),
                type: formData.get('carType'),
                transmission: formData.get('transmission'),
                fuelType: formData.get('fuelType'),
                price: parseFloat(formData.get('price')),
                image: formData.get('carImage'),
                features: features,
                badge: formData.get('badge')
            };

            if (carToEdit) {
                const index = cars.findIndex(c => c.id === carToEdit.id);
                cars[index] = carData;
            } else {
                cars.push(carData);
            }

            renderCars();
            closeCarModal();
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            const carModal = document.getElementById('carModal');
            const deleteModal = document.getElementById('deleteModal');
            if (event.target === carModal) {
                closeCarModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        }

        // Initialize cars on page load
        document.addEventListener('DOMContentLoaded', renderCars);
    </script>
</body>
</html>
