CREATE SCHEMA IF NOT EXISTS PawsConnect;
use PawsConnect;

CREATE TABLE IF NOT EXISTS User (

);

CREATE TABLE IF NOT EXISTS Profile (

);

CREATE TABLE IF NOT EXISTS Announcement (

);

CREATE TABLE IF NOT EXISTS SavedAnnouncement (

);

CREATE TABLE IF NOT EXISTS LostCat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    last_seen_location VARCHAR(100) NOT NULL,
    photo_url VARCHAR(255),
    region VARCHAR(50),
    city VARCHAR(50),
    status ENUM('Lost', 'Found') DEFAULT 'Lost',
    announcer_name VARCHAR(100),
    contact_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS CatAdoption (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    photo_url VARCHAR(255),
    description TEXT,
    age VARCHAR(20),
    region VARCHAR(50),
    city VARCHAR(50),
    status ENUM('Available', 'Adopted') DEFAULT 'Available',
    announcer_name VARCHAR(100),
    contact_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS SickCat (
SickCatID INT AUTO_INCREMENT PRIMARY KEY, 
AnnouncementID INT NOT NULL UNIQUE, 
ConditionDescription TEXT NOT NULL, 
TreatmentAmount DECIMAL(10, 2), 
Location VARCHAR(300), 
PhotoURL VARCHAR(300), 
Status ENUM('Active', 'Recovered') DEFAULT 'Active', 
FOREIGN KEY (AnnouncementID) REFERENCES Announcement(AnnouncementID)
ON DELETE CASCADE 
ON UPDATE CASCADE 
);

CREATE TABLE IF NOT EXISTS Comment (

);
