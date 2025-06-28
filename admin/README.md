# Virunga Homestay Admin Dashboard

A comprehensive, professional admin dashboard system for managing the Virunga Homestay website content and operations.

## 🌟 Features

### Core Functionality
- **Secure Authentication System** - JWT-based authentication with session management
- **Professional Dashboard** - Real-time statistics, recent activities, and quick actions
- **Content Management** - Full CRUD operations for all content types
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile devices
- **Offline Support** - Automatic request queuing when offline with sync when online
- **Professional UX** - Tooltips, confirmations, loading states, and error handling

### Content Management Modules
- **Activities Management** - Manage tourism activities with images and pricing
- **Blog Management** - Create and manage blog posts with rich text editing
- **Car Rental Management** - Manage rental vehicles with features and pricing
- **Events Management** - Create and manage events with date/time controls
- **Rooms Management** - Manage accommodation rooms with descriptions and images
- **Services Management** - Manage additional services offered
- **Reviews Management** - Moderate customer reviews and ratings
- **Hero Images Management** - Manage homepage carousel images
- **Homepage About Management** - Edit homepage about section content
- **Contact Messages** - View and respond to customer inquiries

### Advanced Features
- **Real-time Connectivity Monitoring** - Shows online/offline status
- **Auto-save Functionality** - Prevents data loss with automatic draft saving
- **Professional Tooltips** - Context-sensitive help throughout the interface
- **Confirmation Modals** - Prevents accidental deletions and actions
- **Toast Notifications** - Non-intrusive success/error messages
- **Data Tables** - Sortable, filterable tables with pagination
- **Image Upload** - Drag-and-drop image upload with preview
- **Form Validation** - Client-side and server-side validation
- **Activity Logging** - Complete audit trail of all admin actions
- **Security Features** - Rate limiting, CSRF protection, and secure sessions

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation

1. **Database Setup**
   ```sql
   -- Run the existing homestay.sql first
   -- Then run the admin tables
   SOURCE admin/backend/database/admin_tables.sql;
   ```

2. **Configuration**
   - Update database connection in `include/connection.php`
   - Change JWT secret key in `admin/backend/api/utils/auth_utils.php`
   - Update API base URLs in JavaScript files if needed

3. **Default Admin Account**
   - Username: `admin`
   - Password: `admin123` (CHANGE IMMEDIATELY IN PRODUCTION!)

4. **File Permissions**
   ```bash
   chmod 755 admin/
   chmod 644 admin/assets/
   chmod 755 admin/backend/
   ```

### First Login
1. Navigate to `/admin/`
2. Login with default credentials
3. Change the default password immediately
4. Create additional admin users as needed

## 📁 Project Structure

```
admin/
├── index.html                 # Login page
├── dashboard.html            # Main dashboard
├── assets/
│   ├── css/
│   │   ├── style.css         # Core styles and variables
│   │   ├── components.css    # Component-specific styles
│   │   ├── modals.css        # Modal and toast styles
│   │   ├── tooltips.css      # Tooltip styles
│   │   └── responsive.css    # Responsive design
│   └── js/
│       ├── utils.js          # Utility functions
│       ├── connectivity.js   # Offline/online handling
│       ├── tooltips.js       # Tooltip management
│       ├── modals.js         # Modal and toast management
│       ├── main.js           # Core application logic
│       └── dashboard.js      # Dashboard functionality
├── pages/
│   ├── activities/           # Activities management
│   ├── blogs/               # Blog management
│   ├── cars/                # Car rental management
│   ├── events/              # Events management
│   ├── rooms/               # Rooms management
│   ├── services/            # Services management
│   ├── reviews/             # Reviews management
│   ├── hero-images/         # Hero images management
│   ├── homepage/            # Homepage content management
│   └── messages/            # Contact messages
└── backend/
    ├── api/
    │   ├── auth/            # Authentication endpoints
    │   ├── dashboard/       # Dashboard data endpoints
    │   ├── activities/      # Activities API endpoints
    │   └── utils/           # Utility functions
    └── database/
        └── admin_tables.sql # Admin database schema
```

## 🔧 API Endpoints

### Authentication
- `POST /admin/backend/api/auth/login.php` - User login
- `POST /admin/backend/api/auth/logout.php` - User logout
- `POST /admin/backend/api/auth/verify.php` - Token verification

### Dashboard
- `GET /admin/backend/api/dashboard/stats.php` - Dashboard statistics
- `GET /admin/backend/api/dashboard/recent-activities.php` - Recent activities

### Content Management (Example: Activities)
- `GET /admin/backend/api/activities/list.php` - List activities
- `GET /admin/backend/api/activities/get.php?id={id}` - Get single activity
- `POST /admin/backend/api/activities/create.php` - Create activity
- `POST /admin/backend/api/activities/update.php` - Update activity
- `POST /admin/backend/api/activities/delete.php` - Delete activity

## 🎨 Customization

### Styling
The dashboard uses CSS custom properties (variables) for easy theming:

```css
:root {
    --primary: #6366f1;        /* Primary brand color */
    --secondary: #64748b;      /* Secondary color */
    --success: #10b981;        /* Success color */
    --warning: #f59e0b;        /* Warning color */
    --danger: #ef4444;         /* Danger color */
    /* ... more variables */
}
```

### Adding New Content Types
1. Create new page directory in `pages/`
2. Copy structure from `pages/activities/`
3. Create corresponding API endpoints
4. Add navigation menu item
5. Update permissions if needed

## 🔒 Security Features

- **JWT Authentication** - Secure token-based authentication
- **Session Management** - Server-side session tracking
- **Rate Limiting** - Prevents brute force attacks
- **CSRF Protection** - Cross-site request forgery protection
- **Input Sanitization** - All inputs are sanitized and validated
- **Activity Logging** - Complete audit trail
- **Role-based Access** - Granular permission system

## 📱 Responsive Design

The dashboard is fully responsive and works on:
- Desktop computers (1200px+)
- Tablets (768px - 1199px)
- Mobile phones (< 768px)

Key responsive features:
- Collapsible sidebar
- Mobile-friendly navigation
- Touch-optimized controls
- Responsive data tables
- Mobile-first CSS approach

## 🌐 Browser Support

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## 🚀 Performance Features

- **Lazy Loading** - Images and content loaded on demand
- **Caching** - Intelligent caching strategies
- **Minification** - CSS and JS optimization
- **CDN Integration** - Font Awesome and Chart.js from CDN
- **Efficient Queries** - Optimized database queries
- **Pagination** - Large datasets handled efficiently

## 🔧 Development

### Adding New Features
1. Create feature branch
2. Add necessary database tables/columns
3. Create API endpoints
4. Build frontend interface
5. Add tests
6. Update documentation

### Code Style
- Use ES6+ JavaScript features
- Follow BEM CSS methodology
- Use semantic HTML5 elements
- Comment complex logic
- Use consistent naming conventions

## 📊 Monitoring

The dashboard includes built-in monitoring:
- **Activity Logs** - All user actions logged
- **Security Logs** - Security events tracked
- **API Logs** - All API requests logged
- **Error Logging** - Comprehensive error tracking

## 🆘 Troubleshooting

### Common Issues

1. **Login Issues**
   - Check database connection
   - Verify admin_users table exists
   - Check JWT secret key configuration

2. **API Errors**
   - Check file permissions
   - Verify database connection
   - Check error logs

3. **Styling Issues**
   - Clear browser cache
   - Check CSS file paths
   - Verify responsive breakpoints

### Support
For technical support or questions:
1. Check the documentation
2. Review error logs
3. Contact the development team

## 📄 License

This admin dashboard is part of the Virunga Homestay project and is proprietary software.

---

**Note**: This is a professional admin dashboard system. Always use HTTPS in production and follow security best practices.
