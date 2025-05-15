CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    source VARCHAR(100),
    date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password)
VALUES 
('john_doe', 'john@example.com', 'hashed_password_1'),
('jane_smith', 'jane@example.com', 'hashed_password_2');

INSERT INTO incomes (user_id, amount, source, date)
VALUES 
(1, 3500.00, 'Salary', '2025-04-01'),
(2, 150.00, 'Freelance Writing', '2025-04-05');

INSERT INTO expenses (user_id, amount, category, description, date)
VALUES 
(1, 100.50, 'Groceries', 'Weekly groceries', '2025-04-03'),
(2, 75.00, 'Transport', 'Bus and train tickets', '2025-04-04');

INSERT INTO budgets (user_id, category, amount, description, date)
VALUES 
(1, 'Groceries', 400.00, 'Vegetables', '2025-04-03'),
(2, 'Transport', 150.00, 'bus', '2025-04-04');
