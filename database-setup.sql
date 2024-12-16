-- Create Movies Database
CREATE DATABASE movie_database;
USE movie_database;

-- Create Movies Table
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    genre VARCHAR(100),
    release_year INT,
    rating DECIMAL(3,1),
    popularity INT DEFAULT 0,
    trailer_url VARCHAR(500),
    poster_url VARCHAR(500)
);

-- Insert Sample Movie Data
INSERT INTO movies (title, description, genre, release_year, rating, popularity, trailer_url, poster_url) VALUES 
('Inception', 'A mind-bending thriller about dream infiltration', 'Sci-Fi', 2010, 8.8, 95, 'https://youtube.com/inception_trailer', 'inception_poster.jpg'),
('The Avengers', 'Superhero team assembles to save the world', 'Action', 2012, 8.0, 90, 'https://youtube.com/avengers_trailer', 'avengers_poster.jpg'),
('Parasite', 'A dark comedy about class differences', 'Drama', 2019, 8.6, 85, 'https://youtube.com/parasite_trailer', 'parasite_poster.jpg');
-- Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add a column to track user roles (optional)
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user';

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$2dBQHljXY8GezaweqGQwduhssiELHTWbz1D3HNqZvoscpG.rNTVCK', '2024-11-20 02:16:56', 'admin'),
(11, 'user', 'user@gmail.com', '$2y$10$n4zi2vjDL4AiBX82yAny..xsUjHp.9r0yQWg7SuNpIN4osuiTfxcm', '2024-12-16 13:06:46', 'user');