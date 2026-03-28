# HRMS - Human Resource Management System

## Overview
This is a Laravel 10-based Human Resource Management System (HRMS) project.  
It includes Employee management, Trainee management, Schedules, Positions, Vacations, and UUID verification.

---

## Installation

1. **Clone the repository:**
```bash
git clone https://github.com/yourusername/HRMS.git
cd HRMS
```
2. **Install PHP dependencies:**
```bash
composer install
```
3. **Install Node dependencies (frontend assets):**
```bash
npm install
npm run dev
```
4. **Configure environment variables:**
```bash
cp .env.example .env
php artisan key:generate
```
5. **Run migrations and seeders:**
```bash
php artisan migrate --seed
```
6. **Serve the application:**
```bash
php artisan serve
```
## API Endpoints
| Method | Endpoint                                                      | Description                       | Authentication |
| ------ | ------------------------------------------------------------- | --------------------------------- | -------------- |
| GET    | /api/employee                                                 | List all employees                | Sanctum auth   |
| POST   | /api/employee                                                 | Create new employee               | Sanctum auth   |
| GET    | /api/employee/{id}                                            | Show employee details             | Sanctum auth   |
| PUT    | /api/employee/{id}                                            | Update employee                   | Sanctum auth   |
| DELETE | /api/employee/{id}                                            | Delete employee                   | Sanctum auth   |
| POST   | /api/employee/{id}/cv/upload                                  | Upload CV                         | Sanctum auth   |
| DELETE | /api/employee/{id}/cv                                         | Delete CV                         | Sanctum auth   |
| POST   | /api/employee/{id}/image/upload                               | Upload profile image              | Sanctum auth   |
| DELETE | /api/employee/{id}/image                                      | Delete profile image              | Sanctum auth   |
| GET    | /api/positions                                                | List all positions                | Sanctum auth   |
| POST   | /api/positions                                                | Create position                   | Sanctum auth   |
| GET    | /api/positions/{id}                                           | Show position details             | Sanctum auth   |
| PUT    | /api/positions/{id}                                           | Update position                   | Sanctum auth   |
| DELETE | /api/positions/{id}                                           | Delete position                   | Sanctum auth   |
| GET    | /api/positions/{id}/employees?status=employee|trainee|trained | List employees by position        | Sanctum auth   |
| GET    | /api/trainee                                                  | List trainees                     | Sanctum auth   |
| GET    | /api/trained                                                  | List trained employees            | Sanctum auth   |
| GET    | /api/trained/{id}                                             | Show trained employee             | Sanctum auth   |
| POST   | /api/hire/{id}                                                | Hire trainee                      | Sanctum auth   |
| DELETE | /api/trainee/{id}/end-training                                | End training                      | Sanctum auth   |
| GET    | /api/trainee/{id}/end-training-pdf                            | Download end training certificate | Sanctum auth   |
| GET    | /api/vacations                                                | List vacations                    | Sanctum auth   |
| POST   | /api/vacations                                                | Create vacation                   | Sanctum auth   |
| PUT    | /api/vacations/{id}                                           | Update vacation                   | Sanctum auth   |
| DELETE | /api/vacations/{id}                                           | Delete vacation                   | Sanctum auth   |
| GET    | /api/vacation-statistics                                      | Show vacation statistics          | Sanctum auth   |
| GET    | /api/schedules                                                | List schedules                    | Sanctum auth   |
| POST   | /api/schedules                                                | Create schedule                   | Sanctum auth   |
| PUT    | /api/schedules/{id}                                           | Update schedule                   | Sanctum auth   |
| DELETE | /api/schedules/{id}                                           | Delete schedule                   | Sanctum auth   |
| GET    | /api/schedules/{id}/employees                                 | List assigned employees           | Sanctum auth   |
| GET    | /api/schedule/statistics                                      | Schedule statistics               | Sanctum auth   |
| GET    | /api/uuid                                                     | Show UUID check page              | Guest          |
| POST   | /api/uuid/check                                               | Validate UUID                     | Guest          |

Note: All API routes (except login/logout/UUID) require authentication via Sanctum.

## Authentication
Login: /api/login (POST with username & password)
Logout: /api/logout (POST)

Admin and Moderator roles with permission checks are included.
