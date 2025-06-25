<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/event-admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>Manage Events</h1>
            <button class="btn btn-primary" onclick="openAddEventModal()">
                <i class="fas fa-plus"></i> Add New Event
            </button>
        </div>

        <div class="events-grid" id="eventsGrid">
            <!-- Events will be dynamically populated here -->
        </div>
    </div>

    <!-- Add/Edit Event Modal -->
    <div class="modal" id="eventModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Event</h2>
                <button class="close-btn" onclick="closeEventModal()">&times;</button>
            </div>
            <form id="eventForm" onsubmit="handleEventSubmit(event)">
                <input type="hidden" id="eventId">
                <div class="form-group">
                    <label for="eventTitle">Event Title</label>
                    <input type="text" id="eventTitle" required>
                </div>
                <div class="form-group">
                    <label for="eventDate">Date & Time</label>
                    <input type="datetime-local" id="eventDate" required>
                </div>
                <div class="form-group">
                    <label for="eventImage">Event Image</label>
                    <input type="file" id="eventImage" accept="image/*" required>
                    <div class="image-preview" id="imagePreview"></div>
                </div>
                <div class="form-group">
                    <label for="eventDescription">Description</label>
                    <textarea id="eventDescription" rows="4" required></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Event</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEventModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p>Are you sure you want to delete this event? This action cannot be undone.</p>
            <div class="form-actions">
                <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // --- AJAX CRUD for Events ---
        let events = [];
        let eventToDelete = null;

        function fetchEvents() {
            fetch('events_api.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        events = data.events;
                        renderEvents();
                    }
                });
        }

        function renderEvents() {
            const eventsGrid = document.getElementById('eventsGrid');
            if (!events || events.length === 0) {
                eventsGrid.innerHTML = '<div class="no-events">No events found.</div>';
                return;
            }
            eventsGrid.innerHTML = events.map(event => `
                <div class="event-card">
                    <img src="../../${event.image}" alt="${event.title}" class="event-image" onerror="this.onerror=null;this.src='../../img/placeholder-room.jpg';">
                    <div class="event-content">
                        <h3>${event.title}</h3>
                        <p class="event-date">Date & Time: ${formatDate(event.event_date)}</p>
                        <p class="event-description">${event.description}</p>
                        <div class="event-actions">
                            <button class="btn btn-edit" onclick="editEvent(${event.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-delete" onclick="deleteEvent(${event.id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
        }

        function openAddEventModal() {
            document.getElementById('modalTitle').textContent = 'Add New Event';
            document.getElementById('eventForm').reset();
            document.getElementById('eventId').value = '';
            document.getElementById('imagePreview').style.backgroundImage = 'none';
            document.getElementById('eventModal').style.display = 'block';
        }

        function editEvent(id) {
            const event = events.find(e => e.id === id);
            if (event) {
                document.getElementById('modalTitle').textContent = 'Edit Event';
                document.getElementById('eventId').value = event.id;
                document.getElementById('eventTitle').value = event.title;
                document.getElementById('eventDate').value = event.event_date.replace(' ', 'T');
                document.getElementById('eventImage').value = '';
                document.getElementById('eventDescription').value = event.description;
                document.getElementById('imagePreview').style.backgroundImage = `url(${event.image})`;
                document.getElementById('eventModal').style.display = 'block';
            }
        }

        function closeEventModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        function handleEventSubmit(event) {
            event.preventDefault();
            const eventId = document.getElementById('eventId').value;
            const formData = new FormData();
            formData.append('action', eventId ? 'edit' : 'add');
            formData.append('id', eventId);
            formData.append('title', document.getElementById('eventTitle').value);
            formData.append('event_date', document.getElementById('eventDate').value);
            formData.append('description', document.getElementById('eventDescription').value);
            formData.append('location', ''); // Add location field if needed
            const imageInput = document.getElementById('eventImage');
            if (imageInput.files && imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            } else if (eventId) {
                // For edit, allow keeping the old image if not changed
                formData.append('keep_image', '1');
            }
            fetch('events_api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    fetchEvents();
                    closeEventModal();
                } else {
                    alert('Failed to save event.');
                }
            });
        }

        function deleteEvent(id) {
            eventToDelete = id;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function confirmDelete() {
            if (eventToDelete) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', eventToDelete);
                fetch('events_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        fetchEvents();
                        closeDeleteModal();
                    } else {
                        alert('Failed to delete event.');
                    }
                });
            }
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            eventToDelete = null;
        }

        document.getElementById('eventForm').onsubmit = handleEventSubmit;

        document.getElementById('eventImage').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.style.backgroundImage = 'none';
            }
        });

        fetchEvents();
    </script>
</body>
</html>
