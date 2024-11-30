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
- Docker and Docker Compose installed on your system.

### Installation Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/danamehr/xino-task.git
   cd xino-task
   ```

### Docker Setup
1. Copy the example environment configuration:
   ```bash
   cp .env.example .env
   ```

2. Build and start the Docker containers:
   ```bash
   docker-compose up --build -d
   ```

3. Install dependencies:
   ```bash
   docker exec -it xino-api-app composer install
   ```

4. Generate the application key:
   ```bash
   docker exec -it xino-api-app php artisan key:generate
   ```

5. Run initiate application command:
   ```bash
   docker exec -it xino-api-app php artisan app:init
   ```

---

## Running the Application

### Accessing the Application
- The application will be accessible at `http://127.0.0.1`.

---

## API Documentation

### Base URL
- `http://127.0.0.1/v1`

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
