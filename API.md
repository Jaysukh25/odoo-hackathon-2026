# FleetFlow API Documentation

## Overview

FleetFlow provides a RESTful API for accessing fleet management data programmatically. This API allows integration with external systems, mobile applications, and custom dashboards.

## Base URL

```
Production: https://your-domain.com/api
Development: http://localhost:8000/api
```

## Authentication

### API Authentication
FleetFlow uses Laravel Sanctum for API authentication. You'll need an API token to access protected endpoints.

### Getting API Token
1. Login to FleetFlow web interface
2. Go to Profile → API Tokens
3. Generate new token
4. Copy token for API requests

### Using API Token
Include the token in the Authorization header:
```http
Authorization: Bearer your-api-token-here
```

## Rate Limiting

API requests are limited to prevent abuse:
- **60 requests per minute** per IP address
- **1000 requests per hour** per authenticated user

Rate limit headers are included in responses:
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1640995200
```

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {
        // Response data
    },
    "message": "Operation completed successfully"
}
```

### Error Response
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid.",
        "errors": {
            "field": ["Error message"]
        }
    }
}
```

## Endpoints

### Authentication

#### Login
```http
POST /api/auth/login
```

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "api-token-here",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "manager"
        }
    }
}
```

#### Logout
```http
POST /api/auth/logout
```

**Headers:**
```http
Authorization: Bearer your-api-token-here
```

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Dashboard

#### Get Dashboard Data
```http
GET /api/dashboard
```

**Headers:**
```http
Authorization: Bearer your-api-token-here
```

**Response:**
```json
{
    "success": true,
    "data": {
        "activeVehicles": 15,
        "tripsToday": 8,
        "vehiclesInShop": 3,
        "monthlyOperationalCost": 15420.50,
        "tripActivity": [
            {
                "date": "2024-02-20",
                "count": 12
            }
        ],
        "driverSafetyData": {
            "SAFE": 8,
            "MODERATE": 4,
            "RISKY": 2
        },
        "liveFleet": [
            {
                "id": 1,
                "model": "Ford F-150",
                "license_plate": "ABC-123",
                "driver": "John Doe",
                "status": "available",
                "load": 0,
                "destination": "N/A"
            }
        ],
        "recentActivity": [
            {
                "type": "trip_completed",
                "message": "Trip completed by John Doe to Boston",
                "time": "2024-02-20T14:30:00Z",
                "icon": "check-circle",
                "color": "success"
            }
        ],
        "maintenanceAlerts": [
            {
                "id": 1,
                "license_plate": "XYZ-789",
                "model": "Chevrolet Silverado",
                "odometer": 52000,
                "lastServiceOdometer": 47000
            }
        ]
    }
}
```

### Vehicles

#### Get All Vehicles
```http
GET /api/vehicles
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 10)
- `status`: Filter by status (available, on_trip, in_shop, out_of_service)
- `search`: Search vehicles by model or license plate

**Response:**
```json
{
    "success": true,
    "data": {
        "vehicles": [
            {
                "id": 1,
                "model": "Ford F-150",
                "license_plate": "ABC-123",
                "max_capacity": 1000.00,
                "odometer": 45000.00,
                "status": "available",
                "out_of_service": false,
                "total_fuel_cost": 1250.50,
                "total_maintenance_cost": 450.00,
                "total_operational_cost": 1700.50,
                "average_fuel_efficiency": 8.5,
                "created_at": "2024-01-15T10:00:00Z",
                "updated_at": "2024-02-20T14:30:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total": 25,
            "last_page": 3
        }
    }
}
```

#### Get Single Vehicle
```http
GET /api/vehicles/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "model": "Ford F-150",
        "license_plate": "ABC-123",
        "max_capacity": 1000.00,
        "odometer": 45000.00,
        "status": "available",
        "out_of_service": false,
        "trips": [
            {
                "id": 1,
                "origin": "New York",
                "destination": "Boston",
                "status": "completed",
                "cargo_weight": 800.00,
                "distance": 350.00
            }
        ],
        "maintenance_logs": [
            {
                "id": 1,
                "type": "Oil Change",
                "description": "Regular oil change",
                "cost": 75.00,
                "performed_at": "2024-01-15T10:00:00Z"
            }
        ],
        "fuel_logs": [
            {
                "id": 1,
                "liters": 50.00,
                "cost_per_liter": 1.45,
                "cost": 72.50,
                "fuel_date": "2024-02-20T08:00:00Z"
            }
        ]
    }
}
```

#### Create Vehicle
```http
POST /api/vehicles
```

**Request Body:**
```json
{
    "model": "Ford F-150",
    "license_plate": "XYZ-789",
    "max_capacity": 1200.00,
    "odometer": 50000.00,
    "status": "available",
    "out_of_service": false
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "model": "Ford F-150",
        "license_plate": "XYZ-789",
        "max_capacity": 1200.00,
        "odometer": 50000.00,
        "status": "available",
        "out_of_service": false,
        "created_at": "2024-02-20T15:00:00Z",
        "updated_at": "2024-02-20T15:00:00Z"
    },
    "message": "Vehicle created successfully"
}
```

#### Update Vehicle
```http
PUT /api/vehicles/{id}
```

**Request Body:**
```json
{
    "model": "Ford F-150",
    "license_plate": "XYZ-789",
    "max_capacity": 1200.00,
    "odometer": 52000.00,
    "status": "in_shop",
    "out_of_service": false
}
```

#### Delete Vehicle
```http
DELETE /api/vehicles/{id}
```

**Response:**
```json
{
    "success": true,
    "message": "Vehicle deleted successfully"
}
```

#### Toggle Vehicle Status
```http
POST /api/vehicles/{id}/toggle-status
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "out_of_service": true
    },
    "message": "Vehicle status updated successfully"
}
```

### Drivers

#### Get All Drivers
```http
GET /api/drivers
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 10)
- `status`: Filter by status (available, on_duty, off_duty)
- `risk_level`: Filter by risk level (SAFE, MODERATE, RISKY)
- `search`: Search drivers by name or license number

**Response:**
```json
{
    "success": true,
    "data": {
        "drivers": [
            {
                "id": 1,
                "name": "John Doe",
                "license_number": "DL123456",
                "license_expiry": "2024-12-31",
                "phone": "+1-555-0101",
                "status": "available",
                "license_expiring_soon": false,
                "license_expired": false,
                "risk_score": 2.5,
                "risk_level": "SAFE",
                "risk_color": "success",
                "trips_count": 15,
                "completed_trips": 12,
                "cancelled_trips": 1,
                "late_arrivals": 1,
                "total_distance": 5250.00,
                "current_trip": null,
                "created_at": "2024-01-15T10:00:00Z",
                "updated_at": "2024-02-20T14:30:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total": 12,
            "last_page": 2
        }
    }
}
```

#### Get Single Driver
```http
GET /api/drivers/{id}
```

#### Create Driver
```http
POST /api/drivers
```

**Request Body:**
```json
{
    "name": "Jane Smith",
    "license_number": "DL789012",
    "license_expiry": "2025-06-30",
    "phone": "+1-555-0102",
    "status": "available"
}
```

#### Update Driver
```http
PUT /api/drivers/{id}
```

#### Delete Driver
```http
DELETE /api/drivers/{id}
```

### Trips

#### Get All Trips
```http
GET /api/trips
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 10)
- `status`: Filter by status (draft, dispatched, on_trip, completed, cancelled)
- `vehicle_id`: Filter by vehicle ID
- `driver_id`: Filter by driver ID
- `date_from`: Filter trips from date
- `date_to`: Filter trips to date

**Response:**
```json
{
    "success": true,
    "data": {
        "trips": [
            {
                "id": 1,
                "vehicle_id": 1,
                "driver_id": 1,
                "origin": "New York",
                "destination": "Boston",
                "cargo_weight": 800.00,
                "distance": 350.00,
                "estimated_duration": 270,
                "status": "completed",
                "started_at": "2024-02-20T08:00:00Z",
                "completed_at": "2024-02-20T14:30:00Z",
                "arrived_late": false,
                "cargo_within_capacity": true,
                "vehicle": {
                    "id": 1,
                    "model": "Ford F-150",
                    "license_plate": "ABC-123"
                },
                "driver": {
                    "id": 1,
                    "name": "John Doe",
                    "phone": "+1-555-0101"
                },
                "status_history": [
                    {
                        "old_status": "draft",
                        "new_status": "dispatched",
                        "changed_at": "2024-02-20T08:00:00Z"
                    }
                ],
                "created_at": "2024-02-20T07:30:00Z",
                "updated_at": "2024-02-20T14:30:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total": 45,
            "last_page": 5
        }
    }
}
```

#### Create Trip
```http
POST /api/trips
```

**Request Body:**
```json
{
    "vehicle_id": 1,
    "driver_id": 1,
    "origin": "New York",
    "destination": "Boston",
    "cargo_weight": 800.00,
    "distance": 350.00,
    "estimated_duration": 270
}
```

#### Dispatch Trip
```http
POST /api/trips/{id}/dispatch
```

#### Complete Trip
```http
POST /api/trips/{id}/complete
```

#### Cancel Trip
```http
POST /api/trips/{id}/cancel
```

### Maintenance

#### Get All Maintenance Logs
```http
GET /api/maintenance
```

#### Create Maintenance Log
```http
POST /api/maintenance
```

**Request Body:**
```json
{
    "vehicle_id": 1,
    "type": "Oil Change",
    "description": "Regular oil change and filter replacement",
    "cost": 75.00,
    "odometer_at_service": 46000.00,
    "performed_at": "2024-02-20T10:00:00Z"
}
```

### Fuel

#### Get All Fuel Logs
```http
GET /api/fuel
```

#### Create Fuel Log
```http
POST /api/fuel
```

**Request Body:**
```json
{
    "vehicle_id": 1,
    "liters": 50.00,
    "cost_per_liter": 1.45,
    "cost": 72.50,
    "odometer": 46050.00,
    "fuel_date": "2024-02-20T08:00:00Z"
}
```

### Analytics

#### Get Analytics Data
```http
GET /api/analytics
```

**Response:**
```json
{
    "success": true,
    "data": {
        "fuelEfficiency": [
            {
                "vehicle": "ABC-123",
                "model": "Ford F-150",
                "distance": 5250.00,
                "fuel": 617.65,
                "efficiency": 8.50
            }
        ],
        "vehicleROI": [
            {
                "vehicle": "ABC-123",
                "model": "Ford F-150",
                "revenue": 10500.00,
                "fuel_cost": 896.50,
                "maintenance_cost": 450.00,
                "total_costs": 1346.50,
                "roi_percentage": 18.5
            }
        ],
        "monthlyCosts": [
            {
                "month": "Feb 2024",
                "fuel_cost": 2450.00,
                "maintenance_cost": 1200.00,
                "total_cost": 3650.00
            }
        ],
        "topPerformers": [
            {
                "vehicle": "ABC-123",
                "completed_trips": 12,
                "total_distance": 5250.00,
                "avg_distance_per_trip": 437.50
            }
        ]
    }
}
```

#### Export Analytics
```http
GET /api/analytics/export
```

**Query Parameters:**
- `format`: Export format (csv, xlsx)
- `type`: Data type (fuel_efficiency, roi, costs, performers)

### Notifications

#### Get User Notifications
```http
GET /api/notifications
```

**Query Parameters:**
- `read`: Filter by read status (true, false)
- `type`: Filter by type (maintenance, license, trip, fuel, safety, cost)
- `limit`: Number of notifications (default: 20)

#### Mark Notification as Read
```http
POST /api/notifications/{id}/read
```

#### Mark All Notifications as Read
```http
POST /api/notifications/read-all
```

## Webhooks

### Configure Webhooks
Webhooks allow you to receive real-time notifications when events occur in FleetFlow.

#### Supported Events
- `trip.created`: New trip created
- `trip.dispatched`: Trip dispatched
- `trip.completed`: Trip completed
- `trip.cancelled`: Trip cancelled
- `maintenance.created`: Maintenance log created
- `fuel.created`: Fuel log created
- `vehicle.status_changed`: Vehicle status updated
- `driver.license_expiring`: Driver license expiring

#### Webhook Configuration
```json
{
    "url": "https://your-webhook-endpoint.com/webhook",
    "events": ["trip.created", "trip.completed"],
    "secret": "your-webhook-secret"
}
```

#### Webhook Payload
```json
{
    "event": "trip.completed",
    "data": {
        "trip_id": 1,
        "vehicle_id": 1,
        "driver_id": 1,
        "status": "completed",
        "completed_at": "2024-02-20T14:30:00Z"
    },
    "timestamp": "2024-02-20T14:30:00Z"
}
```

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

## SDKs and Libraries

### JavaScript/Node.js
```javascript
// Installation
npm install fleetflow-api

// Usage
const FleetFlowAPI = require('fleetflow-api');
const client = new FleetFlowAPI('your-api-token');

const vehicles = await client.vehicles.list();
const vehicle = await client.vehicles.get(1);
```

### Python
```python
# Installation
pip install fleetflow-python

# Usage
from fleetflow import FleetFlowClient

client = FleetFlowClient(api_token='your-api-token')
vehicles = client.vehicles.list()
vehicle = client.vehicles.get(1)
```

### PHP
```php
// Installation
composer require fleetflow/php

// Usage
use FleetFlow\Client;

$client = new Client('your-api-token');
$vehicles = $client->vehicles()->list();
$vehicle = $client->vehicles()->get(1);
```

## Rate Limiting

### Limits
- **60 requests per minute** per IP address
- **1000 requests per hour** per authenticated user

### Headers
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1640995200
```

### Handling Rate Limits
```javascript
try {
    const response = await fetch('/api/vehicles');
} catch (error) {
    if (error.status === 429) {
        // Wait for reset time
        const resetTime = error.headers.get('X-RateLimit-Reset');
        setTimeout(() => {
            // Retry request
        }, (resetTime * 1000) - Date.now());
    }
}
```

## Best Practices

### Authentication
- Store API tokens securely
- Use HTTPS for all API requests
- Rotate tokens regularly
- Implement token expiration

### Error Handling
- Always check response status
- Handle rate limits gracefully
- Implement retry logic with exponential backoff
- Log errors for debugging

### Performance
- Use pagination for large datasets
- Cache frequently accessed data
- Use appropriate HTTP methods
- Minimize payload sizes

### Security
- Validate all input data
- Sanitize output data
- Implement proper access controls
- Monitor for unusual activity

## Testing

### Testing API Endpoints
```bash
# Test with curl
curl -H "Authorization: Bearer your-token" \
     -H "Content-Type: application/json" \
     https://your-domain.com/api/vehicles

# Test with Postman
# Import FleetFlow Postman collection
```

### Automated Testing
```javascript
// Example test using Jest
describe('Vehicles API', () => {
    test('should get all vehicles', async () => {
        const response = await fetch('/api/vehicles', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        expect(response.status).toBe(200);
        const data = await response.json();
        expect(data.success).toBe(true);
    });
});
```

## Support

### Documentation Updates
- API documentation is updated with each release
- Check version-specific documentation
- Subscribe to API change notifications

### Getting Help
- API documentation: https://docs.fleetflow.com/api
- GitHub Issues: https://github.com/fleetflow/issues
- Support: api-support@fleetflow.com
- Community: https://community.fleetflow.com

### Reporting Issues
When reporting API issues, include:
- API endpoint and method
- Request headers and body
- Response status and body
- Error messages
- Steps to reproduce

---

## API Versioning

FleetFlow API uses semantic versioning:
- v1.0.0: Current stable version
- v1.1.0: Next planned release
- Breaking changes require major version increment

### Version Headers
```http
API-Version: 1.0.0
```

### Backward Compatibility
- Minor versions are backward compatible
- Patch versions are backward compatible
- Major versions may include breaking changes

---

**Happy coding with FleetFlow API! 🚛**
