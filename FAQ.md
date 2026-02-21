# FleetFlow FAQ

## General Questions

### What is FleetFlow?
FleetFlow is a comprehensive Laravel 10 web application for managing fleet operations, vehicles, drivers, trips, maintenance, and fuel logistics. It provides real-time monitoring, analytics, and automated alerts for efficient fleet management.

### What are the system requirements?
- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache/Nginx)
- Composer
- Node.js (for asset compilation)
- Minimum 2GB RAM and 20GB storage space

### Is FleetFlow open source?
Yes, FleetFlow is released under the MIT License. You can use, modify, and distribute it freely.

## Installation & Setup

### How do I install FleetFlow?
1. Clone the repository
2. Run `composer install`
3. Configure your `.env` file
4. Set up the database
5. Run `php artisan db:seed`
6. Run `npm run build`
7. Start the application with `php artisan serve`

### Do I need to create database migrations?
No, FleetFlow uses existing database tables. Ensure the following tables exist: users, vehicles, drivers, trips, maintenance_logs, fuel_logs, driver_scores, trip_status_history, notifications.

### Can I use SQLite instead of MySQL?
While MySQL is recommended for production, you can use SQLite for development by modifying your `.env` file.

## User Management

### What user roles are available?
- **Manager**: Full system access including dashboard, vehicles, drivers, trips, maintenance, fuel, and analytics
- **Dispatcher**: Access to vehicles, trips, and trip management features
- **Safety**: Access to driver management and compliance monitoring
- **Finance**: Access to analytics and cost reporting

### How do I change a user's role?
Update the `role` field in the `users` table or use the database seeder to create users with different roles.

### Can I add custom user roles?
Currently, FleetFlow supports the four predefined roles. Custom roles would require code modifications.

## Vehicle Management

### How does predictive maintenance work?
FleetFlow automatically alerts when a vehicle's odometer exceeds 5000km since the last service. This helps prevent breakdowns and optimize maintenance scheduling.

### What vehicle statuses are available?
- **Available**: Vehicle is ready for assignment
- **On Trip**: Vehicle is currently on a trip
- **In Shop**: Vehicle is undergoing maintenance
- **Out of Service**: Vehicle is temporarily unavailable

### Can I track vehicle location?
FleetFlow currently tracks vehicle status and trip information. GPS tracking integration is planned for future releases.

## Driver Management

### How is driver risk score calculated?
Risk score = (Late Trips × 2) + (Cancelled Trips × 3) + (License Warning × 5)
- **SAFE** (0-5 points): Green badge
- **MODERATE** (6-15 points): Yellow badge
- **RISKY** (16+ points): Red badge

### What happens when a driver's license expires?
Drivers with expired licenses cannot be assigned to trips. The system shows warnings 30 days before expiration.

### Can drivers have multiple active trips?
No, drivers can only be assigned to one trip at a time to ensure safety and compliance.

## Trip Management

### What is the trip lifecycle?
1. **Draft**: Trip is created but not yet dispatched
2. **Dispatched**: Trip is assigned and vehicle/driver status updated
3. **On Trip**: Trip is in progress
4. **Completed**: Trip is finished with performance metrics
5. **Cancelled**: Trip is cancelled before completion

### How does cargo weight validation work?
The system prevents trip creation if cargo weight exceeds the vehicle's maximum capacity. This ensures safety and compliance.

### Can trips be modified after dispatch?
Only draft trips can be modified. Once dispatched, trips can only be completed or cancelled.

## Maintenance Management

### How are maintenance alerts generated?
The system automatically creates alerts when:
- Vehicle odometer exceeds 5000km since last service
- Manual maintenance records are added
- Scheduled maintenance is due

### What maintenance types are supported?
- Oil Change
- Tire Rotation
- Brake Service
- Engine Service
- Transmission
- Electrical
- Inspection
- Other (custom types)

### Does maintenance affect vehicle availability?
Yes, when a maintenance record is added, the vehicle status automatically changes to "In Shop" and becomes unavailable for trips.

## Fuel Management

### How is fuel efficiency calculated?
Fuel efficiency is calculated as: Total Distance ÷ Total Fuel Consumption (km/L)

### Can I track fuel costs?
Yes, FleetFlow tracks fuel costs per liter, total costs, and provides cost per kilometer analysis.

### What fuel data is tracked?
- Liters filled
- Cost per liter
- Total cost
- Odometer reading
- Fuel date
- Efficiency calculations

## Analytics & Reporting

### What analytics are available?
- Fuel efficiency analysis per vehicle
- Vehicle ROI calculations
- Monthly cost trends
- Driver performance metrics
- Top performer rankings
- Export capabilities

### Can I export data?
Yes, you can export analytics data to CSV format from the analytics page.

### How often is dashboard data updated?
The dashboard can be refreshed manually or automatically every 30 seconds. Real-time updates are available via AJAX.

## Security

### How is user authentication handled?
FleetFlow uses Laravel's built-in authentication system with:
- Secure password hashing
- CSRF protection
- Session management
- Role-based access control

### Is my data secure?
Yes, FleetFlow implements multiple security measures including input validation, SQL injection prevention, and secure session management.

### Can I use LDAP/Active Directory?
Currently, FleetFlow uses its own authentication system. LDAP integration is planned for future releases.

## Performance

### How can I optimize performance?
- Use Redis for caching
- Optimize database queries
- Enable query caching
- Use CDN for static assets
- Monitor memory usage

### What are the server requirements for production?
- Minimum 2GB RAM
- 20GB storage space
- PHP 8.1+
- MySQL 5.7+
- SSL certificate (recommended)

### Can I handle large fleets?
Yes, FleetFlow is designed to scale. For very large fleets, consider load balancing and database optimization.

## Troubleshooting

### I'm getting a 500 error. What should I do?
1. Check file permissions
2. Verify `.env` configuration
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure database connection is working

### Why can't I see the dashboard?
- Ensure you're logged in
- Check your user role permissions
- Verify the route is accessible
- Clear browser cache

### How do I clear caches?
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Why are assets not loading?
- Run `npm run build`
- Check file permissions in public directory
- Verify asset URLs
- Clear browser cache

## Customization

### Can I customize the UI?
Yes, the UI uses Bootstrap 5 and custom CSS. You can modify the styles in `resources/css/app.css`.

### How do I add new features?
1. Create a new branch
2. Follow the coding standards
3. Write tests
4. Submit a pull request

### Can I integrate with third-party APIs?
Yes, FleetFlow supports API integration. Check the documentation for specific integration guidelines.

## Support

### Where can I get help?
- GitHub Issues: Bug reports and feature requests
- GitHub Discussions: General questions
- Documentation: Comprehensive guides and API docs
- Email: support@fleetflow.com

### How do I report bugs?
1. Check existing issues
2. Create a new issue with detailed information
3. Include steps to reproduce
4. Add screenshots if applicable

### Is commercial support available?
Yes, commercial support packages are available. Contact sales@fleetflow.com for more information.

## Mobile Access

### Is there a mobile app?
Currently, FleetFlow is web-based and mobile-responsive. A native mobile app is planned for future releases.

### Can I use FleetFlow on tablets?
Yes, FleetFlow is fully responsive and works well on tablets and mobile devices.

## Data Management

### Can I import existing data?
Yes, you can import data through database migrations or custom import scripts. Contact support for assistance.

### How do I backup my data?
Use the provided backup scripts or configure automated backups through your hosting provider.

### Can I export all my data?
Yes, you can export data through the analytics page or directly from the database.

## Integration

### Does FleetFlow integrate with GPS systems?
GPS integration is planned for future releases. Currently, location tracking is manual.

### Can I integrate with accounting systems?
Yes, FleetFlow provides API endpoints for integration with external systems.

### Is there an API available?
Yes, FleetFlow includes RESTful API endpoints for data access and integration.

## Updates & Maintenance

### How do I update FleetFlow?
1. Backup your data
2. Download the latest version
3. Update dependencies
4. Run migrations if needed
5. Clear caches

### Are updates free?
Yes, updates are free for all users under the MIT license.

### How often are updates released?
Updates are released regularly with bug fixes and new features. Major releases are announced in advance.

## Licensing

### What does the MIT license allow?
The MIT license allows you to use, modify, and distribute the software freely, including for commercial purposes.

### Do I need to pay for FleetFlow?
No, FleetFlow is free and open source.

### Can I remove the copyright notice?
No, the copyright notice must remain intact as per the MIT license terms.

---

## Still Have Questions?

If you have questions not covered in this FAQ, please:
1. Check the documentation
2. Search existing GitHub issues
3. Create a new issue
4. Contact our support team

We're here to help you get the most out of FleetFlow!
