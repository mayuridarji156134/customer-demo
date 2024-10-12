
# Laravel 11 + Vue.js CRUD with Modal, SweetAlert, and Axios

This is a CRUD application built using Laravel 11 and Vue.js, with SweetAlert2 for alert messages, Axios for making HTTP requests, and Tailwind CSS for styling.

## Features
- CRUD functionality for managing customers and contacts
- Modal-based form inputs
- Axios for backend communication
- SweetAlert2 for beautiful alert dialogs
- Tailwind CSS for responsive and modern styling
- Server-side validation with Laravel

## Project Setup

1. Clone the Repository
	git clone -b main https://github.com/mayuridarji156134/customer-demo.git

2. cd customer-demo
3. composer install
4. npm install
4. create .env file 
5. set APP_URL and Database
	APP_URL=http://127.0.0.1:8000

	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=laravel_customer_new
	DB_USERNAME=root
	DB_PASSWORD=

6. php artisan migrate
7. php artisan db:seed --class=CustomerCategorySeeder
8. npm run build
9. php artisan serve

### Technology Stack
	Backend: Laravel 11
	Frontend: Vue.js 3, Tailwind CSS
	HTTP Client: Axios
	Alerts: SweetAlert2
	Build Tool: Vite

### Key Features
	Create, Read, Update, Delete (CRUD): Manage customers and contacts with ease.
	Modal-based UI: Forms for adding/editing customers and contacts are rendered in modals for better user experience.
	Real-time Validation: Server-side validation with Laravel for input integrity.
	Pagination and Filtering: Use search and category filters to narrow down customer results.
	Modern UI: Styled with Tailwind CSS for a sleek, responsive interface.