# HRMS Laravel Project

**HRMS** (Human Resource Management System) is a web application built with **Laravel 10**.  
It provides full management for employees, positions, schedules, vacations, and trainees, along with admin and moderator roles. The project includes **web views** and **RESTful API endpoints**.

---

## Features

- Employee management (CRUD)
- Position management with salaries
- Trainee tracking and onboarding
- Vacation management with statistics
- Schedule management and employee assignment
- UUID validation for employees
- Admin & moderator panel with roles and permissions
- PDF export for training completion
- API ready for frontend consumption

---

## Technologies Used

- **Laravel 10**
- **PHP 8+**
- **MySQL**
- **Vite + TailwindCSS** for frontend assets
- **Sanctum** for API authentication
- **DOMPDF** for PDF generation

---

## Installation

1. **Clone the repository:**

```bash
git clone https://github.com/yourusername/HRMS.git
cd HRMS

2. **Install PHP dependencies:**
```bash
composer install

3. **Install Node dependencies (frontend assets):**
```bash
npm install
npm run dev

4. **Configure environment variables:**
```bash
cp .env.example .env
php artisan key:generate

5. **Run migrations and seeders:**
```bash
php artisan migrate --seed

6. **Serve the application:**
```bash
php artisan serve


API Endpoints
Method	Endpoint	Description	Authentication
GET	/api/employee	List all employees	Sanctum auth
POST	/api/employee	Create new employee	Sanctum auth
GET	/api/employee/{id}	Show employee details	Sanctum auth
PUT	/api/employee/{id}	Update employee	Sanctum auth
DELETE	/api/employee/{id}	Delete employee	Sanctum auth
POST	/api/employee/{id}/cv/upload	Upload CV	Sanctum auth
DELETE	/api/employee/{id}/cv	Delete CV	Sanctum auth
POST	/api/employee/{id}/image/upload	Upload profile image	Sanctum auth
DELETE	/api/employee/{id}/image	Delete profile image	Sanctum auth
GET	/api/positions	List all positions	Sanctum auth
POST	/api/positions	Create position	Sanctum auth
GET	/api/positions/{id}	Show position details	Sanctum auth
PUT	/api/positions/{id}	Update position	Sanctum auth
DELETE	/api/positions/{id}	Delete position	Sanctum auth
GET	`/api/positions/{id}/employees?status=employee	trainee	trained`
GET	/api/trainee	List trainees	Sanctum auth
GET	/api/trained	List trained employees	Sanctum auth
GET	/api/trained/{id}	Show trained employee	Sanctum auth
POST	/api/hire/{id}	Hire trainee	Sanctum auth
DELETE	/api/trainee/{id}/end-training	End training	Sanctum auth
GET	/api/trainee/{id}/end-training-pdf	Download end training certificate	Sanctum auth
GET	/api/vacations	List vacations	Sanctum auth
POST	/api/vacations	Create vacation	Sanctum auth
PUT	/api/vacations/{id}	Update vacation	Sanctum auth
DELETE	/api/vacations/{id}	Delete vacation	Sanctum auth
GET	/api/vacation-statistics	Show vacation statistics	Sanctum auth
GET	/api/schedules	List schedules	Sanctum auth
POST	/api/schedules	Create schedule	Sanctum auth
PUT	/api/schedules/{id}	Update schedule	Sanctum auth
DELETE	/api/schedules/{id}	Delete schedule	Sanctum auth
GET	/api/schedules/{id}/employees	List assigned employees	Sanctum auth
GET	/api/schedule/statistics	Schedule statistics	Sanctum auth
GET	/api/uuid	Show UUID check page	Guest
POST	/api/uuid/check	Validate UUID	Guest

Note: All API routes (except login/logout/UUID) require authentication via Sanctum.


Authentication
Login: /api/login (POST with username & password)
Logout: /api/logout (POST)
Admin and Moderator roles with permission checks are included.
Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss.
