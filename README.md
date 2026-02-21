# FleetFlow - Fleet & Logistics Management System

A comprehensive Laravel 10 web application for managing fleet operations, vehicles, drivers, trips, maintenance, and fuel logistics.

## Features

### Core Fleet Management
- **Vehicle Management**: Complete CRUD operations for fleet vehicles
- **Driver Management**: Driver profiles with license tracking and risk assessment
- **Trip Management**: End-to-end trip lifecycle from dispatch to completion
- **Maintenance Tracking**: Predictive maintenance alerts and service history
- **Fuel Management**: Fuel logs with efficiency calculations

### Smart Analytics
- **Fuel Efficiency Analysis**: Track km/L performance per vehicle
- **Vehicle ROI Calculation**: Revenue vs operational costs analysis
- **Driver Risk Scoring**: Automated risk assessment based on performance
- **Cost Per KM**: Detailed operational cost tracking
- **Predictive Maintenance**: Alerts based on odometer readings

### Role-Based Access Control
- **Manager**: Full dashboard and system access
- **Dispatcher**: Trip and vehicle management
- **Safety**: Driver compliance and safety monitoring
- **Finance**: Analytics and cost reporting

### Modern UI/UX
- Premium Dribbble-style SaaS dashboard
- Bootstrap 5 responsive design
- Dark sidebar with clean white content area
- Real-time fleet status monitoring
- Interactive charts and KPI cards

## Installation

### Prerequisites
- PHP 8.1+
- MySQL 5.7+ or 8.0+
- Composer
- Node.js (for asset compilation)

### Setup Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd FleetFlow
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
```bash
# Edit .env file with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fleetflow
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Create database**
```sql
CREATE DATABASE fleetflow;
```

6. **Create database tables** (using existing database structure)
The application uses existing database tables. Ensure the following tables exist:
- users
- vehicles
- drivers
- trips
- maintenance_logs
- fuel_logs
- driver_scores
- trip_status_history
- notifications

7. **Seed sample data**
```bash
php artisan db:seed
```

8. **Start the application**
```bash
php artisan serve
```

## Demo Accounts

After running the database seeder, you can login with these accounts:

- **Manager**: manager@fleetflow.com / password
- **Dispatcher**: dispatcher@fleetflow.com / password
- **Safety**: safety@fleetflow.com / password
- **Finance**: finance@fleetflow.com / password

## Key Features

### Dashboard
- Real-time KPI cards (Active Vehicles, Trips Today, Vehicles In Shop, Monthly Costs)
- Trip activity charts (30-day view)
- Driver safety distribution (donut chart)
- Live fleet status table
- Recent activity feed
- Predictive maintenance alerts

### Vehicle Management
- Complete CRUD operations
- Capacity and odometer tracking
- Status management (Available, On Trip, In Shop, Out of Service)
- Maintenance history integration
- Cost analysis (fuel, maintenance, operational)

### Driver Management
- License expiry tracking with 30-day warnings
- Automated risk scoring (SAFE/MODERATE/RISKY)
- Performance statistics
- Trip history
- Compliance monitoring

### Trip Management
- Full lifecycle: Draft → Dispatched → On Trip → Completed → Cancelled
- Cargo capacity validation
- Automatic vehicle/driver status updates
- Trip status history tracking
- Performance metrics (on-time, late arrivals)

### Maintenance Module
- Service type categorization
- Cost tracking
- Odometer-based service scheduling
- Automatic vehicle status updates
- Predictive maintenance alerts (5000km threshold)

### Fuel Management
- Fuel log tracking with cost calculations
- Automatic efficiency calculations (km/L)
- Cost per kilometer analysis
- Fuel consumption trends

### Analytics & Reporting
- Fuel efficiency analysis per vehicle
- Vehicle ROI calculations
- Monthly cost trends
- Top performer rankings
- CSV export functionality

## Smart Features

### Predictive Maintenance
Automated alerts when vehicle odometer exceeds 5000km since last service.

### Driver Risk Assessment
Risk score calculation based on:
- Late trips (2 points)
- Cancelled trips (3 points)
- License warnings (5 points)

Risk levels:
- SAFE (0-5 points) - Green
- MODERATE (6-15 points) - Yellow
- RISKY (16+ points) - Red

### Cost Calculations
- **Fuel Efficiency**: Distance / Liters
- **Vehicle ROI**: (Revenue - Fuel - Maintenance) / Acquisition Cost
- **Cost Per KM**: Total Operational Costs / Odometer Reading

## Technical Architecture

### Backend
- **Framework**: Laravel 10
- **Database**: MySQL
- **Authentication**: Laravel's built-in auth system
- **ORM**: Eloquent

### Frontend
- **CSS Framework**: Bootstrap 5
- **Charts**: Chart.js
- **Icons**: Bootstrap Icons
- **JavaScript**: Vanilla JS with modern ES6+ features

### Database Relationships
- Vehicles → Trips (One to Many)
- Vehicles → Maintenance Logs (One to Many)
- Vehicles → Fuel Logs (One to Many)
- Drivers → Trips (One to Many)
- Trips → Status History (One to Many)

## Security Features
- Role-based access control
- Input validation and sanitization
- CSRF protection
- SQL injection prevention via Eloquent ORM
- Secure password hashing

## Performance Optimizations
- Database query optimization with eager loading
- Efficient pagination
- Optimized asset loading
- Caching strategies for analytics data

## Browser Support
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## License
MIT License

## Support
For support and questions, please contact the development team.
