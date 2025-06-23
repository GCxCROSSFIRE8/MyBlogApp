````# 🚀 MyBlogApp

A full-featured, responsive blog app built with PHP, MySQL, and Bootstrap 5.

## 🌟 Features

- ✅ **User authentication**: register, login, logout (session-based)
- 📝 **Post management**: create, edit, and delete posts
- 🌍 **Global post view**: browse posts from all users
- 🔍 **Search**: filter posts by title, content, or author (email)
- 📄 **Pagination**: limited posts per page with easy navigation (5 for view, 4 for dashboard)
- 👤 **User-specific dashboard**: view and manage your own posts
- 🧩 **Responsive UI**: includes navbar, forms, cards, alerts — looks great on mobile and desktop

## 🛠️ Tech Stack

- PHP (with sessions & MySQL)
- MySQL database
- Bootstrap 5 for modern, mobile-first design
- XAMPP (Apache + MySQL)

## 📁 File Overview

myblogapp/
├── db.php               # Database connection
├── navbar.php           # Reusable Bootstrap navbar
├── dashboard.php        # User's post management (+ pagination & search)
├── view.php             # Public view of all posts (+ author, date, pagination, search)
├── log-in.php           # Login form
├── register.php         # Registration form
├── log-out.php          # Logs out user (session destroy)
├── edit.php             # Edit user's post
├── delete.php           # Delete user's post
├── include/
│   ├── css/bootstrap.css      # (optional local Bootstrap)
│   └── js/bootstrap.bundle.js # (optional local Bootstrap JS)
└── README.md            # This file

---

## ⚡ Setup Instructions

1. Clone the project to your web root (e.g., `C:/xampp/htdocs/myblogapp`)
2. Create a MySQL database named `myblogapp`
3. In `db.php`, set your DB credentials (`$servername`, `$username`, `$password`, `$dbname`)
4. Create the tables by running the following SQL commands in your MySQL database:

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email_id VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


````
