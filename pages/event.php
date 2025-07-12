<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Best Upcoming Events</title>
    <link rel="stylesheet" href="../css/event.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/room.css">
  </head>
  <body>
    <?php include 'include/header.php'; ?>
    <div class="floating-elements"></div>

    <section class="rooms-hero">
      <h1>Our Upcoming Events</h1>
      <p>Discover the best upcoming events at Virunga Homestay. Join us for unforgettable experiences, cultural celebrations, and special gatherings. Stay tuned for more details and book your spot today!</p>
  </section>

      <div class="events-grid" id="eventsGrid">
        <!-- Events will be dynamically populated here -->
      </div>
    </div>
  <?php include 'include/footer.php'; ?>

    <!-- Floating WhatsApp Button -->
    <a
      href="https://wa.me/+250788123456?text=Hello! I'd like to know more about Virunga Homestay"
      class="floating-whatsapp"
    >
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path
          d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.688"
        />
      </svg>
    </a>
    <script src="../js/event.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      fetch('events_api.php')
        .then(res => res.json())
        .then(data => {
          if (data.success && data.events.length > 0) {
            const grid = document.getElementById('eventsGrid');
            const now = new Date();
            grid.innerHTML = data.events.map(event => {
              const eventDate = new Date(event.event_date.replace(' ', 'T'));
              let status = '';
              if (eventDate > now) {
                const diff = eventDate - now;
                if (diff < 1000 * 60 * 60 * 24 * 3) { // less than 3 days
                  status = '<div class="event-status starting-soon">Starting Soon</div>';
                } else {
                  status = '<div class="event-status upcoming">Upcoming Event</div>';
                }
              } else {
                status = '<div class="event-status finished">Finished</div>';
              }
              return `
              <div class="event-card" data-event-date="${event.event_date}">
                <img src="../uploads/events/${event.image}" alt="${event.title}"/>
                ${status}
                <div class="event-content">
                  <h3 class="event-title">${event.title}</h3>
                  <p class="event-date">Date & Time: ${formatEventDate(event.event_date)}</p>
                  <div class="countdown">
                    <div class="countdown-title">Event starts in:</div>
                    <div class="countdown-timer">
                      <div class="countdown-item"><span class="countdown-number days">00</span><span class="countdown-label">Days</span></div>
                      <div class="countdown-item"><span class="countdown-number hours">00</span><span class="countdown-label">Hours</span></div>
                      <div class="countdown-item"><span class="countdown-number minutes">00</span><span class="countdown-label">Minutes</span></div>
                      <div class="countdown-item"><span class="countdown-number seconds">00</span><span class="countdown-label">Seconds</span></div>
                    </div>
                  </div>
                  <p class="event-description">${event.description}</p>
                  <a href="https://wa.me/+250788123456?text=Hi! I'm interested in booking for the ${encodeURIComponent(event.title)} event on ${formatEventDate(event.event_date)}. Please send me more details." class="whatsapp-btn">Book Now</a>
                </div>
              </div>
              `;
            }).join('');
            updateCountdown();
          } else {
            document.getElementById('eventsGrid').innerHTML = '<div class="no-events">No events found.</div>';
          }
        });

      // Helper to format event date
      window.formatEventDate = function(dateString) {
        const date = new Date(dateString.replace(' ', 'T'));
        return date.toLocaleString('en-US', {
          year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true
        });
      };
    });
    </script>
  </body>
</html>
