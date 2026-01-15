# Walizone Autotech Enterprise

A professional website for an automotive service garage in Chinsali, Zambia.

## Project Structure

```
WalizoneAutotech/
├── admin/                     # Admin area
│   ├── index.php              # Admin dashboard
│   ├── appointments.php       # Appointments management
│   ├── login.php              # Admin login
│   ├── logout.php             # Admin logout
│   └── .htaccess              # Security for admin directory
├── config/
│   └── db.php                 # Database configuration
├── database/
│   └── setup.sql              # SQL setup script
├── images/                    # Image assets
│   ├── favicon.svg            # Website favicon
│   └── logo.svg               # Website logo
├── services/                  # Service detail pages
│   ├── routine-maintenance.php
│   ├── computer-diagnostics.php
│   ├── engine-transmission-repair.php
│   └── template.php           # Template for new service pages
├── index.php                  # Main homepage
├── services.php               # Services listing page
├── booking.php                # Appointment booking system
├── contact.php                # Contact form handler
├── 404.php                    # Custom error page
├── .htaccess                  # Server configuration
├── manifest.json              # PWA manifest
├── sw.js                      # Service worker for offline capabilities
├── robots.txt                 # Search engine guidance
├── sitemap.xml                # Site structure for search engines
└── README.md                  # Project documentation
```

## Setup Instructions

1. Install XAMPP or similar local server environment
2. Clone this repository to your htdocs folder
3. Create the database using the SQL script in the database folder:
   - Open phpMyAdmin
   - Create a new database named "walizone_autotech"
   - Import the database/setup.sql file
4. Configure the database connection in config/db.php if needed
5. Access the website at http://localhost/WalizoneAutotech

## Features

- Responsive design for all device sizes
- Clean, modern interface with professional styling
- Detailed service pages with pricing and FAQs
- Online appointment booking system
- Contact form with database storage
- Admin dashboard for message and appointment management
- Progressive Web App (PWA) capabilities for offline access
- SEO optimizations (meta tags, sitemap, robots.txt)
- Performance optimizations (caching, compression)
- Custom error pages

## Technologies Used

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- Font Awesome icons
- Google Fonts
- Progressive Web App (PWA) technologies
- SEO best practices

## Admin Access

- URL: http://localhost/WalizoneAutotech/admin/
- Username: admin
- Password: admin123

## Implemented Enhancements

1. **Professional UI/UX Design**
   - Modern, responsive layout with professional color scheme
   - Improved typography with Google Fonts
   - Enhanced visual hierarchy and content organization
   - Custom logo and favicon
   - Consistent styling across all pages

2. **Detailed Service Pages**
   - Individual pages for each service with comprehensive information
   - Pricing details, benefits, and process explanations
   - FAQ sections for common questions
   - Related services recommendations
   - Professional imagery and icons

3. **Online Appointment Booking System**
   - User-friendly booking form
   - Service selection and date/time picker
   - Form validation and database storage
   - Admin interface for managing appointments

4. **Admin Appointment Management**
   - View all appointments in the admin dashboard
   - Filter appointments by status and date
   - Update appointment status (confirm, complete, cancel)
   - Track appointment history

5. **Technical Improvements**
   - Progressive Web App (PWA) implementation for offline access
   - SEO optimizations with proper meta tags
   - Performance enhancements with browser caching
   - Security improvements with .htaccess configurations
   - Custom 404 error page
   - Sitemap and robots.txt for search engines

## Future Enhancements

- Gallery of completed work with before/after images
- Customer testimonials with ratings system
- Staff profiles with expertise areas
- Online payment integration
- SMS notifications for appointments
- Vehicle service history tracking
- Customer loyalty program
- Live chat support

## License

Copyright © 2025 Walizone Autotech Enterprise. All rights reserved.