# 📦 Inventory & Order Management System (IMS) 🛍️
This project is a multi-tenant backend API for inventory and order management, built using Laravel and containerized with Docker. It securely isolates data, ensuring each shop (tenant) can only access its own products and orders.

# ⚙️ Requirements
Ensure your system meets the following prerequisites before installation:

PHP (v8.1 or higher)

Composer (Package Manager for PHP)

MySQL or PostgreSQL (Database)

Node.js & npm (For frontend asset compilation)

Laravel (v9 or v10 recommended)

Git

# 🚀 Key Features
Multi-Tenancy: Data isolation enforced at the Eloquent model layer using a BelongsToTenant trait and Global Scopes.

Authentication: API token authentication via Laravel Sanctum.

Transactional Integrity: Order placement logic is handled by an OrderService within a database transaction, ensuring stock is decremented only if the order is fully successful.

APIs: Complete CRUD for Products and read/create for Orders.

UI (Basic): Simple administrative UI using Laravel Breeze (Blade/Alpine.js) for quick testing of CRUD and multi-tenancy.

# 💻 Setup and Installation
Follow these steps to get the application running locally via Docker.

## Step 1: Clone and Build
# Clone the repository
```bash
git clone https://github.com/Pooja151295/Inventory-Order-Management.git
cd InventoryManagement
```
# Create the environment file
cp .env.example .env

# Build and run the containers (can take a few minutes the first time)

```bash
docker-compose up -d --build
```
## Step 2: Initialize Laravel
Run database setup and generate the application key inside the laravel_app container.

Bash

# Generate Laravel Application Key
```bash
 docker exec -it laravel_app php artisan key:generate
```
# Run migrations to create tables (users, products, orders, etc.)
```bash
docker exec -it laravel_app php artisan migrate
```
# Seed sample data (2 shops, 2 admins, ~10 products)
```bash
docker exec -it laravel_app php artisan db:seed
```
# Step 3: Compile Assets (Frontend)
Compile the required CSS and JavaScript assets. This should be run on your host machine.

# Install Node dependencies
```bash
 npm install
```
# Compile assets (use 'npm run dev' during development)
```bash
npm run build 
Note: If you do not have Node/npm installed on your host, run the following Docker commands instead:

docker run --rm -v ${PWD}:/app -w /app node:lts-alpine npm install

docker run --rm -v ${PWD}:/app -w /app node:lts-alpine npm run build
```
# 🌐 Access and Credentials
The application can be accessed through a web browser or via Postman.
To use the API through Postman:

Log in using the provided API endpoint

Obtain the authentication token from the login response

In Postman, open the Authorization tab and select Bearer Token

Paste the token into the field to authenticate your requests ( Bearer {token} )

You can now access all available API endpoints

## Component	URL	Sample Credentials
Web UI (Login)	http://localhost:8000/login	Shop A Admin: shopa@example.com / password <br>
API Root	http://localhost:8000/api	Shop B Admin: shopb@example.com / password<br>
Shop A Inventory	http://localhost:8000/products	(After logging in as Shop A)

# 🧪 Testing API Endpoints (Postman)
Use Postman for thorough testing of security and transactional logic. Remember to get a token via /api/login first.

## 1. Multi-Tenancy & CRUD
Method	Endpoint	Goal / Security Check
GET	/api/products	List only products belonging to the authorized shop.<br>
POST	/api/products	Create product (Controller auto-assigns the correct shop_id).<br>
PUT	/api/products/{id}	Update product. Must fail (404) if {id} belongs to the other shop.

## 2. Order Transactional Logic
Method	Endpoint	Goal / Security Check
POST	/api/orders	Place Order. Verify stock decrement on success (201).<br>
POST	/api/orders	Test Failure: Order with insufficient stock. Verify 409 Conflict and database transaction rollback.<br>
GET	/api/orders	View the list of orders for the authenticated shop (Multi-tenancy check).

# 🛑 Tearing Down
To stop and remove all Docker containers, networks, and volumes (optional, use with caution), run:

```bash
docker-compose down -v
```

# 📧 Contact / Maintainer Info
For questions, issues, or contributions, please contact the maintainer:

Maintainer: Pooja Langalia

GitHub Profile: Pooja151295
