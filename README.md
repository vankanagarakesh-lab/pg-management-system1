# PG Management System

A comprehensive, full-stack **Paying Guest (PG) & Hostel Management System** built with **Laravel**, **Tailwind CSS / Blade**, and **MySQL**. Designed to streamline day-to-day operations for PG owners, administrative staff, maintenance workers, and resident students.

---

## 🌟 Key Features

### 👑 Admin Portal
- **PG Buildings & Room Management**: Create and manage multiple PG properties, configure rooms, set bed capacities, and dynamic rent structures.
- **Resident (Student) Lifecycle**: Complete approval workflow for new registration requests, resident status tracking (Active, Pending, Rejected, Revoked), and student profiling.
- **Billing & Finance Engine**: Automated monthly rent/due generation, payment reconciliation, manual cash/online invoice approvals, and customizable payment configurations (UPI IDs, bank details).
- **Staff & Task Delegation**: Add staff members across departments (Housekeeping, Maintenance, Kitchen), assign room cleaning or maintenance tasks, and monitor completion status.
- **Complaint Resolution System**: Centralized dashboard to view complaints raised by residents, assign them to dedicated staff, and track resolution timelines.
- **Inventory & Asset Control**: Maintain stock counts for room fixtures, kitchen supplies, and utilities with adjustment logs and low stock notifications.
- **Food & Catering Management**: Define weekly meal schedules, track resident dietary preferences, and respond to meal feedback.
- **Notice Board & CMS**: Broadcast announcements across student and staff dashboards, manage public landing page content, and resolve prospective visitor inquiries.

### 🎓 Resident (Student) Portal
- **Dashboard & Dues**: View current room details, rent due breakdown, payment history, and download digital invoices.
- **Online & Offline Payment Submission**: Submit payment receipts/transaction IDs for admin reconciliation.
- **Complaint Submission & Verification**: Lodge issues regarding plumbing, electrical, hygiene, or Wi-Fi, with multi-stage verification (In Progress, Resolved, Student Verified).
- **Food Preferences & Feedback**: Select daily meal attendance (Breakfast, Lunch, Dinner), specify veg/non-veg preferences, and rate meal quality.
- **Announcements**: Instant access to PG rules, notices, and important updates.

### 🧹 Staff Portal
- **Housekeeping & Maintenance Checklists**: View daily assigned room and common area cleaning tasks with interactive toggle switches.
- **Issue Reporting**: Directly log maintenance faults discovered during daily routines.
- **Daily Work Reports**: Submit shift summaries and daily task completion reports to management.
- **Inventory & Kitchen Adjustments**: Kitchen/maintenance staff can update stock usage in real-time.

### 🌐 Public Landing Page & Inquiry Portal
- Interactive showcase of PG amenities, pricing, room availability, and photo gallery.
- Embedded visitor inquiry form connected to the admin dashboard.

---

## 🛠️ Technology Stack

- **Backend**: PHP 8.2+ / Laravel 12.x
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js / Vanilla JS, FontAwesome / Lucide icons
- **Database**: MySQL / MariaDB (Supports SQLite for local testing)
- **Asset Bundling**: Vite
- **Development Tooling**: Composer, Artisan, NPM

---

## 🚀 Quick Start Guide

### Prerequisites
- PHP `>= 8.2`
- Composer `>= 2.0`
- Node.js `>= 18.0` & NPM
- MySQL / MariaDB (or XAMPP)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/vankanagarakesh-lab/pg-management-system.git
   cd pg-management-system
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

4. **Configure Environment File**
   Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
   Update your database credentials in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pg_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Database Migrations & Seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Build Frontend Assets**
   ```bash
   npm run build
   ```

8. **Start Local Development Server**
   ```bash
   php artisan serve
   ```
   Or launch concurrently with logs and Vite using Composer:
   ```bash
   composer dev
   ```

---

## 📂 Project Architecture

```
pg-management-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php    # Admin management, billing, tasks, PG setup
│   │   │   ├── AuthController.php     # Multi-role authentication & user profiles
│   │   │   ├── LandingController.php  # Public landing page & visitor inquiries
│   │   │   ├── StaffController.php    # Staff checklists, maintenance & daily reporting
│   │   │   └── StudentController.php  # Resident payments, complaints & food choices
│   └── Models/                        # Eloquent ORM models (PgBuilding, Room, Payment, Complaint, etc.)
├── database/
│   ├── migrations/                    # Database table schemas
│   └── seeders/                       # Initial seed data
├── resources/
│   ├── views/                         # Blade templates for Admin, Student, Staff & Public views
│   ├── css/                           # Custom CSS and Tailwind configurations
│   └── js/                            # Interactive UI logic
├── routes/
│   └── web.php                        # Application routing matrix
└── public/                            # Static assets and upload directories
```

---

## 🔒 User Roles & Access Matrix

| Role | Access Level | Primary Dashboard Route |
| :--- | :--- | :--- |
| **Admin** | Full System Access | `/admin` |
| **Student (Resident)** | Resident Portal | `/student` |
| **Staff** | Task & Maintenance Portal | `/staff` |

---

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).
