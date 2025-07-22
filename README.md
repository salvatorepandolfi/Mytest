# Purchase Cart Service

A RESTful API service built with Symfony 7 for processing purchase cart orders with automatic pricing and VAT calculations.

## Features

- **RESTful API**: Clean REST endpoint for order processing
- **Automatic Pricing**: Calculates item prices and VAT based on product catalog
- **Data Persistence**: Stores orders and products using Doctrine ORM with SQLite
- **Validation**: Comprehensive input validation using Symfony Validator
- **Testing**: Full test coverage with PHPUnit (unit and integration tests)
- **Docker Ready**: Fully containerized with Docker
- **Production Ready**: Optimized for production deployment

## Requirements

- Docker
- Git

## Quick Start

### Using Docker (Recommended)

The service is designed to run in Docker containers as specified in the requirements.

```bash
# Build the Docker image
docker build -t mytest .

# Run build script (install dependencies, create database, run migrations)
docker run -v $(pwd):/mnt -p 9090:9090 -w /mnt mytest ./scripts/build.sh

# Run tests
docker run -v $(pwd):/mnt -p 9090:9090 -w /mnt mytest ./scripts/test.sh

# Start the service
docker run -v $(pwd):/mnt -p 9090:9090 -w /mnt mytest ./scripts/run.sh
```

The service will be available at `http://localhost:9090`.

## API Endpoints

### Create Order
**POST** `/api/orders`

Creates a new order with automatic pricing calculation.

#### Request Format
```json
{
  "order": {
    "items": [
      {
        "product_id": 1,
        "quantity": 1
      },
      {
        "product_id": 2,
        "quantity": 5
      },
      {
        "product_id": 3,
        "quantity": 1
      }
    ]
  }
}
```

#### Response Format
```json
{
  "order_id": 3412433,
  "order_price": "12.50",
  "order_vat": "1.25",
  "items": [
    {
      "product_id": 1,
      "quantity": 1,
      "price": "2.00",
      "vat": "0.20"
    },
    {
      "product_id": 2,
      "quantity": 5,
      "price": "7.50",
      "vat": "0.75"
    },
    {
      "product_id": 3,
      "quantity": 1,
      "price": "3.00",
      "vat": "0.30"
    }
  ]
}
```

### List Products
**GET** `/api/products`

Returns all available products with their pricing information.

### Health Check
**GET** `/health`

Returns service health status.

## Architecture

### Directory Structure
```
/
├── src/
│   ├── Controller/          # REST API controllers
│   ├── Entity/              # Doctrine entities (Product, Order, OrderItem)
│   ├── Repository/          # Data access layer
│   ├── Service/             # Business logic
│   └── DTO/                 # Data Transfer Objects
├── tests/
│   ├── Unit/                # Unit tests
│   └── Integration/         # Integration tests
├── config/                  # Symfony configuration
├── scripts/                 # Docker build/test/run scripts
├── migrations/              # Database migrations
└── public/                  # Web root
```

### Key Components

#### Entities
- **Product**: Stores product information with pricing and VAT rates
- **Order**: Main order entity with calculated totals
- **OrderItem**: Individual line items within an order

#### Services
- **OrderService**: Core business logic for order processing
- **ProductFixturesService**: Loads sample product data

#### DTOs
- **OrderRequest/OrderResponse**: API request/response structures
- **Validation**: Built-in validation using Symfony Validator

## Business Logic

### Pricing Calculation
- Each product has a base price and VAT rate
- Item price = base price × quantity
- Item VAT = item price × VAT rate
- Order total = sum of all item prices
- Order VAT = sum of all item VATs

### VAT Handling
- VAT rates are stored as decimals (e.g., 0.1000 for 10%)
- All monetary calculations use BCMath for precision
- VAT is calculated per item, then summed for order total

### Data Storage
- SQLite database for simplicity and portability
- Doctrine ORM for database abstraction
- Database migrations for schema management

## Testing

The service includes comprehensive test coverage:

### Unit Tests
- Entity behavior and calculations
- Business logic validation
- Edge cases and error handling

### Integration Tests
- Full API endpoint testing
- Database integration
- Error response validation

### Running Tests
```bash
# Run all tests
docker run -v $(pwd):/mnt -p 9090:9090 -w /mnt mytest ./scripts/test.sh

# Or manually with PHPUnit
docker run -v $(pwd):/mnt -w /mnt mytest php bin/phpunit
```

## Configuration

### Environment Variables
- `APP_ENV`: Application environment (prod/dev/test)
- `APP_SECRET`: Application secret key
- `DATABASE_URL`: Database connection string

### Sample Products
The service automatically loads sample products on first request:
1. Sample Product 1 - €2.00 (10% VAT)
2. Sample Product 2 - €1.50 (10% VAT)  
3. Sample Product 3 - €3.00 (10% VAT)
4. Premium Product - €15.99 (22% VAT)
5. Basic Item - €0.99 (10% VAT)

## Development

### Local Development (without Docker)
```bash
# Install dependencies
composer install

# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Start development server
php -S localhost:9090 -t public
```

### Adding New Products
Products can be added programmatically via the ProductRepository or by extending the ProductFixturesService.

## Production Considerations

### Performance
- Optimized autoloader with `--optimize-autoloader`
- Production cache configuration
- Database connection pooling ready

### Security
- Input validation on all endpoints
- SQL injection protection via Doctrine ORM
- XSS protection through proper JSON handling

### Monitoring
- Health check endpoint for load balancers
- Structured error responses
- Comprehensive logging ready

## Testing the API

### Using cURL
```bash
# Create an order
curl -X POST http://localhost:9090/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "order": {
      "items": [
        {"product_id": 1, "quantity": 1},
        {"product_id": 2, "quantity": 5},
        {"product_id": 3, "quantity": 1}
      ]
    }
  }'

# List products
curl http://localhost:9090/api/products

# Health check
curl http://localhost:9090/health
```

## Error Handling

The API returns appropriate HTTP status codes and structured error responses:

- **400 Bad Request**: Invalid input data or JSON format
- **404 Not Found**: Product not found
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server errors

Example error response:
```json
{
  "error": "Validation failed",
  "details": [
    {
      "field": "order.items[0].quantity",
      "message": "This value should be greater than 0."
    }
  ]
}
```
