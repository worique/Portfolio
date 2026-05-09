-- Portfolio Database Export
-- Compatible with MySQL 5.7+/8.0+

CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- Drop existing tables for clean re-import
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    technologies VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    github_link VARCHAR(255) NOT NULL,
    demo_link VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Admin user
-- Username: bensaidissou
-- Password: b7ae8522158
INSERT INTO users (username, email, password) VALUES
('bensaidissou', 'ilyes.bensaid@outlook.com', '$2y$12$soamEtrFRj9meiRBxg2/KeGeQiBr4aGQeITDs2bWI4C6yM5lzXwFS');

-- Sample projects
INSERT INTO projects (title, description, technologies, image_url, github_link, demo_link) VALUES
(
    'DevTrack Task Management Platform',
    'A full-stack task management platform with role-based dashboards, real-time updates, and productivity analytics for teams.',
    'PHP, MySQL, JavaScript, Bootstrap, REST API',
    'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80',
    'https://github.com/bensaidissou/devtrack',
    'https://devtrack-demo.example.com'
),
(
    'E-Commerce Storefront & Admin',
    'An end-to-end e-commerce web application featuring product catalog management, secure checkout simulation, and order tracking.',
    'PHP, MySQL, HTML5, CSS3, JavaScript',
    'https://images.unsplash.com/photo-1557821552-17105176677c?auto=format&fit=crop&w=1200&q=80',
    'https://github.com/bensaidissou/ecommerce-platform',
    'https://ecommerce-demo.example.com'
),
(
    'Portfolio CMS with Authentication',
    'A custom content management portfolio with admin authentication, project CRUD operations, and contact message handling.',
    'PHP, PDO, MySQL, AJAX, CSS Grid',
    'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
    'https://github.com/bensaidissou/portfolio-cms',
    'https://portfolio-cms-demo.example.com'
),
(
    'Real-Time Chat Application',
    'A responsive chat interface with real-time communication powered by Node.js socket services and modern front-end architecture.',
    'Node.js, Express, Socket.IO, React, MongoDB',
    'https://i.ytimg.com/vi/gbocZlm71nE/sddefault.jpg',
    'https://github.com/bensaidissou/realtime-chat-app',
    'https://chat-app-demo.example.com'
);

-- Optional sample contact messages
INSERT INTO contacts (name, email, subject, message) VALUES
('Sarah Johnson', 'sarah.johnson@example.com', 'Freelance Collaboration', 'Hi Ilyes, I love your portfolio and would like to discuss a collaboration project.'),
('Ahmed Karim', 'ahmed.karim@example.com', 'Job Opportunity', 'We are hiring a full-stack developer and your profile looks like a great match.');
