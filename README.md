# 📰 MyBlogApp

A role-based, full-featured blogging platform built with **PHP**, **MySQL**, and **Bootstrap 5**.

> 🔒 Includes admin/editor/user roles, secure CRUD operations, pagination, search, and access control.

---

## 🌟 Features

- ✅ **User Authentication**

  - Secure register/login/logout system
  - Role stored in session (`admin`, `editor`, `user`)

- 🧩 **Role-Based Access**

  - 🧑‍💼 **Admins** can edit/delete any post, access all posts
  - ✍️ **Editors** can create and edit their own posts
  - 👤 **Users** can create/edit their own posts but **cannot delete**

- 📝 **Post Management**

  - Create, edit, delete (based on role)
  - Admin can delete any post and see all content
  - Flash messages for success/error on all actions

- 🌍 **Public & Private Views**

  - `view.php` shows all published posts for all users
  - `dashboard.php` shows posts created by the logged-in user

- 🔍 **Search + Pagination**

  - Search posts by title/content in both public and dashboard views
  - 5 posts per page in `view.php`, 4 in `dashboard.php`

- 🎛️ **Admin Panel (`admin.php`)**

  - View/edit/delete any post from any user
  - Access controlled via role
  - Shows message `Edited by Admin` if modified via admin

- 🎨 **Responsive UI**
  - Clean, mobile-first design using Bootstrap 5
  - Includes reusable navbar and alert-based feedback

---

## 📁 File Overview

myblogapp/
├── db.php # Database connection
├── navbar.php # Role-aware Bootstrap navbar
├── dashboard.php # Role-sensitive user dashboard
├── view.php # Public blog feed for all users
├── admin.php # Admin-only view/edit/delete panel
├── log-in.php # Login page
├── register.php # User registration
├── log-out.php # Session logout
├── edit.php # Edit post (admin/user)
├── delete.php # Secure delete with permissions
└── README.md # This file

---

## 🛠️ Tech Stack

- **PHP 8+** (with sessions and PDO)
- **MySQL** (tested with MariaDB & MySQL 8)
- **Bootstrap 5.3+**
- Runs locally on **XAMPP**, **MAMP**, or **LAMP**

---

## ⚙️ Setup Instructions

1. **Clone this repo** to your web root (e.g. `htdocs/myblogapp` for XAMPP)
2. **Create a MySQL database** called `myblogapp`
3. In `db.php`, configure your database credentials:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "myblogapp";
   ```
   Import the following SQL schema into your DB:

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
email VARCHAR(255) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
role ENUM('admin', 'editor', 'user') NOT NULL DEFAULT 'user',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE posts (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
title VARCHAR(255) NOT NULL,
content TEXT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NULL DEFAULT NULL,
deleted_at TIMESTAMP NULL DEFAULT NULL,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
