# Subscription System (Xino Digital)

## Overview
This project is a modular Laravel application that implements a subscription system. Users can subscribe to different plans, which grant access to specific sections. The application includes modules for handling subscriptions, payments, invoices, and user management.

---

## Features
- Modular architecture with separate services for **Subscriptions**, **Payments**, **Invoices**, and **Users**.
- Mocked payment service for initial subscription and renewals.
- Webhook handling for subscription renewals.
- Invoices are generated for successful payments.
- API-first design with route versioning (`v1`, `v2`).
- Database migrations, service providers, and route auto-registration for each module.
- Redis and MariaDB integration for caching and database management.
- Dockerized setup for quick deployment.

---

## Technologies Used
- **PHP**: 8.2
- **Laravel**: 11.x
- **MariaDB**: 10.6
- **Redis**: 6.x
- **Docker**: Containerized application setup.
- **GitHub Actions**: CI/CD pipeline for automated testing.

---

## Getting Started

### Prerequisites
- Docker installed on your system.
- Docker Compose `v2.x` or higher installed on your system.

### Installation Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/danamehr/xino-task.git && cd xino-task
   ```
2. Checkout to develop branch:
   ```bash
   git checkout origin/develop
   ```

### Docker Setup
1. Copy the example environment configuration:
   ```bash
   cp .env.example .env
   ```

2. Change working directory to docker-compose.yml directory:
   ```bash
   cd deploy/composer
   ```

3. Build and start the Docker containers:
   ```bash
   docker compose up --build -d
   ```
    or
    
   ```bash
   docker-compose up --build -d
   ```

4. Use the following command to see the names of your containers on your device:
   ```bash
   docker ps
   ```
   
5. Check the container name for `xino/api` image at the last column of the table. It should be something like `xino-api-app-1`. If it's different, copy and replace it with the `xino-api-app-1` in the following commands before running them.


6. Install dependencies:
   ```bash
   docker exec -it xino-api-app-1 composer install
   ```

7. Generate the application key:
   ```bash
   docker exec -it xino-api-app-1 php artisan key:generate
   ```

8. Run initiate application command:
   ```bash
   docker exec -it xino-api-app-1 php artisan app:init
   ```

9. Copy the access token provided by the `app:init` command, and go to the variables tab on the postman collection and replace it with the `token` field's value, then save the changes. 

---

## Running the Application

### Accessing the Application
- The application will be accessible at `http://127.0.0.1:8000`.

---

## API Documentation

### Base URL
- `http://127.0.0.1:8000/v1`

### Endpoints
For detailed API testing, import the provided **Postman Collection** located at `./docs/Xino-Task.postman_collection.json`.

---

## Modules
Each module is self-contained and follows the modular architecture principles. Below is a summary of the modules:

1. **User Module**:
    - Contains user-related data like model, migrations, and factory.

2. **Subscription Module**:
    - Handles plans, user subscriptions, and access control to sections.

3. **Payment Module**:
    - Mocked service for handling payments and webhook triggers.

4. **Invoice Module**:
    - Generates and manages invoices for subscription payments.

5. **Shared Module**:
    - Provides shared utilities used across all modules.

---

## Docker Compose Services
1. **`app`**: The Laravel application running PHP.
2. **`mariadb`**: MariaDB as the database service.
3. **`redis`**: Redis for caching.

---
