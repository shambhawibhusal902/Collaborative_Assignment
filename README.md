# Collaborative Assignment

A PHP-based web application for managing collaborative assignments with user authentication and database integration.

## Prerequisites

- XAMPP (or similar web server with PHP and MySQL)
- PHP 7.0 or higher
- MySQL 5.7 or higher

## Installation

1. Clone this repository to your XAMPP's htdocs folder:
   ```
   C:\xampp\htdocs\Collaborative_Assignment
   ```

2. Start your XAMPP Apache and MySQL services

3. The database will be automatically created when you first access the application, but you can also initialize it manually by visiting:
   ```
   http://localhost/Collaborative_Assignment/config/init_db.php
   ```

## Project Structure

```
├── config/
│   ├── db_config.php      # Database connection configuration
│   ├── db_operations.php  # Database CRUD operations
│   ├── init_db.php        # Database initialization
│   └── schema.sql        # Database schema
├── css/
│   └── style.css         # Application styles
├── index.php             # Main application entry point
└── README.md            # Project documentation
```

## Features

- User authentication system
- Database connection management
- CRUD operations helper functions
- Responsive design with CSS
- Automatic database initialization

## Default Access

After initialization, you can access the system with these default credentials:
- Username: admin
- Email: admin@example.com
- Password: admin123

## Database Structure

The application uses a MySQL database with the following main table:

### Users Table
- id (INT, Primary Key)
- username (VARCHAR(50), Unique)
- email (VARCHAR(100), Unique)
- password_hash (VARCHAR(255))
- created_at (TIMESTAMP)

## License

This project is part of an educational assignment.