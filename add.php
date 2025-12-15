<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['UserID'])) {
        header("Location: login.html");
        exit();
    }
    $userID = $_SESSION['UserID']; 

    $typeInput = $_POST['type'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';

    $typeMap = [
        'adopt' => 'CatAdoption',
        'lost' => 'LostCat',
        'sick' => 'SickCat'
    ];

    if (!isset($typeMap[$typeInput])) {
        die("Invalid announcement type.");
    }
    $type = $typeMap[$typeInput];

    // رفع الصور
    $photoURL = '';
    if (!empty($_FILES['photos']['name'][0])) {
        $uploadsDir = 'uploads/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);

        $photoNames = [];
        foreach ($_FILES['photos']['name'] as $key => $name) {
            $tmpName = $_FILES['photos']['tmp_name'][$key];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
            if (!in_array($ext, $allowed)) continue; // تجاهل الملفات غير المدعومة

            $newName = uniqid('cat_') . ".$ext";
            if (move_uploaded_file($tmpName, $uploadsDir . $newName)) {
                $photoNames[] = $uploadsDir . $newName;
            }
        }
        if (!empty($photoNames)) {
            $photoURL = implode(',', $photoNames);
        }
    }

    $stmt = $conn->prepare("INSERT INTO Announcement 
        (UserID, Title, Description, PhotoURL, Location, ContactEmail, ContactPhone, Type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $userID, $title, $description, $photoURL, $location, $contact_email, $contact_phone, $type);
    $stmt->execute();
    $announcementID = $stmt->insert_id;
    $stmt->close();

    switch ($type) {
        case 'CatAdoption':
            $age = $_POST['age'] ?? '';
            $gender = $_POST['gender'] ?? '';
            $vaccinated = $_POST['vaccinated'] ?? '';
            $neutered = $_POST['neutered'] ?? '';
            $adoption_req = $_POST['adoption_requirements'] ?? '';

            $stmt = $conn->prepare("INSERT INTO CatAdoption (AnnouncementID, Age, Gender, Vaccinated, Neutered, AdoptionRequirements) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $announcementID, $age, $gender, $vaccinated, $neutered, $adoption_req);
            $stmt->execute();
            $stmt->close();

            header("Location: adoptable-cats.php");
            exit();

        case 'LostCat':
            $catName = $_POST['cat_name'] ?? '';
            $dateLost = $_POST['date_lost'] ?? '';
            $lastSeen = $_POST['last_seen_location'] ?? '';
            $reward = $_POST['reward_offered'] ?? '';
            $features = $_POST['distinct_features'] ?? '';

            $stmt = $conn->prepare("INSERT INTO LostCat (AnnouncementID, CatName, DateLost, LastSeenLocation, RewardOffered, DistinctFeatures) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $announcementID, $catName, $dateLost, $lastSeen, $reward, $features);
            $stmt->execute();
            $stmt->close();

            header("Location: lost-cats.php");
            exit();

        case 'SickCat':
            $symptoms = $_POST['symptoms'] ?? '';
            $urgency = $_POST['urgency'] ?? '';
            $dateNoticed = $_POST['date_noticed'] ?? '';
            $foundLocation = $_POST['found_location'] ?? '';
            $needs = $_POST['needs'] ?? '';

            $stmt = $conn->prepare("INSERT INTO SickCat (AnnouncementID, Symptoms, Urgency, DateNoticed, FoundLocation, Needs) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $announcementID, $symptoms, $urgency, $dateNoticed, $foundLocation, $needs);
            $stmt->execute();
            $stmt->close();

            header("Location: sick.php");
            exit();
    }

} else {
    echo "No data submitted.";
}
?>


