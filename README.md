# 🧾 Calva Pharma Inventory System

A **modern inventory management system** built for **Calva Pharma Trading**, designed to streamline product tracking, supplier management, and sales reporting.

---

## 🚀 Tech Stack

- **Frontend:** [Vue.js](https://vuejs.org/)
- **Backend:** [Laravel](https://laravel.com/)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/)
- **Database:** MySQL

---

## 🧩 Features

- 🏷️ Inventory management (add, update, and track product quantities)  
- 👥 Supplier and distributor management  
- 💰 Point of Sale (POS) module  
- 🔐 User roles and access control  
- 📊 Sales and transaction history  
- 🔍 Advanced filtering, search, and export options  

---

## ⚙️ Installation

### 🔧 Backend (Laravel)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
