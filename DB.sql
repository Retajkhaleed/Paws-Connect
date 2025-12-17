CREATE SCHEMA IF NOT EXISTS paws_connect;
USE paws_connect;

CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    PhoneNumber VARCHAR(20) NOT NULL UNIQUE,
    DateRegistered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Profile (
    ProfileID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL UNIQUE,
    ProfilePhotoURL VARCHAR(255),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Announcement (
    AnnouncementID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Title VARCHAR(150) NOT NULL,
    Description TEXT NOT NULL,
    PhotoURL VARCHAR(255),
    Location VARCHAR(150) NOT NULL,
    ContactEmail VARCHAR(150) NOT NULL,
    ContactPhone VARCHAR(20),
    Type ENUM('LostCat', 'CatAdoption', 'SickCat') NOT NULL,
    Status ENUM('Active', 'Found', 'Adopted', 'Recovered') DEFAULT 'Active',
    DateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS SavedAnnouncement (
    SavedID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    AnnouncementID INT NOT NULL,
    DateSaved TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (UserID, AnnouncementID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (AnnouncementID) REFERENCES Announcement(AnnouncementID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS LostCat (
    LostCatID INT AUTO_INCREMENT PRIMARY KEY,
    AnnouncementID INT UNIQUE,
    CatName VARCHAR(100),
    DateLost DATE NOT NULL,
    LastSeenLocation VARCHAR(150) NOT NULL,
    RewardOffered ENUM('Yes','No'),
    DistinctFeatures VARCHAR(255),
    FOREIGN KEY (AnnouncementID) REFERENCES Announcement(AnnouncementID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS CatAdoption (
    AdoptionID INT AUTO_INCREMENT PRIMARY KEY,
    AnnouncementID INT UNIQUE,
    Age VARCHAR(50),
    Gender ENUM('Male','Female'),
    Vaccinated ENUM('Yes','No'),
    Neutered ENUM('Yes','No'),
    AdoptionRequirements VARCHAR(255),
    AdoptionStatus ENUM('Available','Adopted') DEFAULT 'Available',
    FOREIGN KEY (AnnouncementID) REFERENCES Announcement(AnnouncementID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS SickCat (
    SickCatID INT AUTO_INCREMENT PRIMARY KEY,
    AnnouncementID INT NOT NULL UNIQUE,
    Symptoms VARCHAR(255) NOT NULL,
    Urgency ENUM('Low','Moderate','Critical') NOT NULL,
    DateNoticed DATE NOT NULL,
    FoundLocation VARCHAR(150) NOT NULL,
    Needs VARCHAR(255),
    Status ENUM('Active','Recovered') DEFAULT 'Active',
    FOREIGN KEY (AnnouncementID) REFERENCES Announcement(AnnouncementID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
CREATE TABLE IF NOT EXISTS Comment (
    CommentID INT AUTO_INCREMENT PRIMARY KEY,
    AnnouncementID INT NOT NULL,
    UserID INT NOT NULL,
    CommentText TEXT NOT NULL,
    DatePosted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (AnnouncementID) REFERENCES Announcement(AnnouncementID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
