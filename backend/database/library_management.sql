-- Create tables
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    genre VARCHAR(100),
    total_copies INT NOT NULL,
    available_copies INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('borrower', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    user_id INT,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    fine DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample data
-- Books
INSERT INTO books (title, author, isbn, genre, total_copies, available_copies) VALUES
('To Kill a Mockingbird', 'Harper Lee', '9780446310789', 'Fiction', 5, 5),
('1984', 'George Orwell', '9780451524935', 'Science Fiction', 3, 3),
('Pride and Prejudice', 'Jane Austen', '9780141439518', 'Romance', 4, 4),
('The Catcher in the Rye', 'J.D. Salinger', '9780316769174', 'Fiction', 3, 3),
('The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Fiction', 4, 4);

-- Users
INSERT INTO users (name, email, password, role) VALUES
('John Doe', 'john@example.com', '$2y$10$FW8nowUsrYoyeCH4CbKO4.m8BKvgTW0es3AtoO/WVpcjWYL16t2b.', 'borrower'),
('Jane Smith', 'jane@example.com', '$2y$10$OaoLMTVTi5Fmb2wOD8TfqeMp.LK7U1K..KqQsssSzQmw2t/WLukbi', 'borrower'),
('Admin User', 'admin@example.com', '$2y$10$m4kHW2/Yldni3wRatg1EAOhBVltXwMIuqI3dIgMaxQJ6yR/2McNla', 'admin');

-- Borrowings
INSERT INTO borrowings (book_id, user_id, borrow_date, due_date, return_date) VALUES
(1, 1, '2023-05-01', '2023-05-15', '2023-05-14'),
(2, 2, '2023-05-05', '2023-05-19', NULL),
(3, 1, '2023-05-10', '2023-05-24', NULL);

-- Update available copies for borrowed books
UPDATE books SET available_copies = available_copies - 1 WHERE id IN (2, 3);

-- Create a view for overdue books
CREATE VIEW overdue_books AS
SELECT b.id AS borrowing_id, bk.title, u.name AS borrower_name, b.borrow_date, b.due_date,
       DATEDIFF(CURDATE(), b.due_date) AS days_overdue,
       GREATEST(0, DATEDIFF(CURDATE(), b.due_date)) * 0.50 AS fine
FROM borrowings b
JOIN books bk ON b.book_id = bk.id
JOIN users u ON b.user_id = u.id
WHERE b.return_date IS NULL AND b.due_date < CURDATE();

-- Create a stored procedure to calculate fines
DELIMITER //
CREATE PROCEDURE calculate_fines()
BEGIN
    UPDATE borrowings
    SET fine = GREATEST(0, DATEDIFF(IFNULL(return_date, CURDATE()), due_date)) * 0.50
    WHERE return_date IS NULL OR return_date > due_date;
END //
DELIMITER ;

-- Create a trigger to update available copies when a book is borrowed or returned
DELIMITER //
CREATE TRIGGER after_borrowing_insert
AFTER INSERT ON borrowings
FOR EACH ROW
BEGIN
    UPDATE books SET available_copies = available_copies - 1
    WHERE id = NEW.book_id;
END //

CREATE TRIGGER after_borrowing_update
AFTER UPDATE ON borrowings
FOR EACH ROW
BEGIN
    IF OLD.return_date IS NULL AND NEW.return_date IS NOT NULL THEN
        UPDATE books SET available_copies = available_copies + 1
        WHERE id = NEW.book_id;
    END IF;
END //
DELIMITER ;