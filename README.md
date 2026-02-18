🚀 Project Module – Task & Project Management System
📌 Overview

Project Module is a Laravel-based Project and Task Management System built with role-based access control.
The system allows Admins, Managers, and Employees to collaborate efficiently on projects and tasks.

This project was developed using Laravel Breeze for authentication and includes a scheduled cron job for automatic due date validation.

🛠 Tech Stack

Backend: PHP 8+, Laravel 10+

Authentication: Laravel Breeze

Database: MySQL

Frontend: Blade + Bootstrap

API: RESTful API

Scheduler: Laravel Task Scheduler (Cron Job)

🔐 Authentication Features (Laravel Breeze)

User Registration

Login

Forgot Password

Reset Password

Secure Password Hashing

Session-based Authentication

👥 User Roles & Access Control

The system supports three roles:

🛡 Admin

Full system access

Manage Users (Admin, Manager, Employee)

Create / Edit / Delete Projects

Create / Edit / Delete Tasks

Assign Managers & Employees

View all tasks and reports

👔 Manager

Access assigned projects

Create & manage tasks

Assign tasks to employees

Update task details

View reports

👨‍💻 Employee

View assigned tasks

Update task status

Cannot create or delete projects

📂 Modules
1️⃣ Projects Module

Create Project

Assign Manager

Edit / Update Project

View Project Details

2️⃣ Tasks Module

Create Task

Assign Employee

Set Due Date

Update Task Status

Filter Tasks

⏰ Cron Job – Due Date Validation

A scheduled cron job checks tasks daily.

Logic:

If due_date < today
→ Task is marked as Overdue

Setup Cron Job

Add this line to your server crontab:

* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
🌐 API Endpoints
Get Tasks by Employee ID
GET /api/tasks/{employee_id}
With Filters
GET /api/tasks/{employee_id}?status=completed
GET /api/tasks/{employee_id}?due_date=2026-02-18
GET /api/tasks/{employee_id}?project_id=1
Sample JSON Response
{
  "status": true,
  "data": [
    {
      "id": 1,
      "title": "Design UI",
      "status": "pending",
      "due_date": "2026-02-20"
    }
  ]
}
🔎 Task Filtering Options

Filter by Status

Filter by Due Date

Filter by Project

Filter by Employee

⚙️ Installation Guide
1️⃣ Clone Repository
git clone https://github.com/your-username/project-module.git
cd project-module
2️⃣ Install Dependencies
composer install
npm install
npm run dev
3️⃣ Setup Environment File
cp .env.example .env
php artisan key:generate

Update database credentials inside .env.

4️⃣ Run Migrations
php artisan migrate

(Optional)

php artisan db:seed
5️⃣ Run Application
php artisan serve

Visit:

http://127.0.0.1:8000
🗄 Database Tables

users

projects

tasks


📌 Business Logic Summary

Admin & Manager → Full access to project management

Employee → View assigned tasks and update status

Cron job automatically updates overdue tasks

API supports task retrieval and filtering