# HM CARS

**The Right Car. The Right Connection.**

HM CARS is a Kenyan car brokerage marketplace connecting vehicle buyers with individual vehicle owners and dealerships.

## Main Features

- Customer registration and login
- Vehicle listings and details
- Buyer inquiries and follow-up management
- Seller vehicle submissions
- Admin dashboard and staff management
- Vehicle promotions and marketing
- Vehicle ownership guide
- Centralized HM CARS contact channels

## Technology Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Blade
- HTML, CSS and JavaScript
- Vite

## Local Setup

Install dependencies:

```bash
composer install
npm install
```

## Production Deployment

Laravel cannot start without a valid application encryption key. Add the following as environment variables in the hosting platform, then redeploy:

```dotenv
APP_NAME="HM CARS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
APP_KEY=base64:REPLACE_WITH_A_GENERATED_32_BYTE_KEY

DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-database-password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local
```

Generate a key locally with:

```powershell
php -r "echo 'base64:'.base64_encode(random_bytes(32));"
```

Store that generated value as `APP_KEY` in deployment environment settings. Do not commit `.env` or the key to source control. Each environment should use one stable key; changing it invalidates encrypted data and existing sessions.

After setting variables, redeploy the container. Use `/healthz` as the health-check path only if the application defines that route; otherwise the root route can remain the health check.
