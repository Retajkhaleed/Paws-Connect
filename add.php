<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "No data submitted.";
    exit;
}

/* =====================
   CHECK LOGIN
===================== */
if (!isset($_SESSION['UserID'])) {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION['UserID'];

/* =====================
   COMMON DATA
===================== */
$typeInput      = $_POST['type'] ?? '';
$title           = $_POST['title'] ?? '';
$description     = $_POST['description'] ?? '';
$location        = $_POST['location'] ?? '';
$contact_email   = $_POST['contact_email'] ?? '';
$contact_phone   = $_POST['contact_phone'] ?? '';

$typeMap = [
    'adopt' => 'CatAdoption',
    'lost'  => 'LostCat',
    'sick'  => 'SickCat'
];

if (!isset($typeMap[$typeInput])) {
    die("Invalid announcement type.");
}

$type = $typeMap[$typeInput];

/* =====================
   IMAGE UPLOAD
===================== */
$photoURL = '';

if (!empty($_FILES['photos']['name'][0])) {

    $uploadsDir = 'uploads/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    $photoNames = [];

    foreach ($_FILES['photos']['name'] as $key => $name) {

        $tmpName = $_FILES['photos']['tmp_name'][$key];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) continue;

        $newName = uniqid('cat_') . '.' . $ext;

        if (move_uploaded_file($tmpName, $uploadsDir . $newName)) {
            $photoNames[] = $uploadsDir . $newName;
        }
    }

    if (!empty($photoNames)) {
        $photoURL = implode(',', $photoNames);
    }
}

/* =====================
   INSERT ANNOUNCEMENT
===================== */
$stmt = $conn->prepare("
    INSERT INTO Announcement
    (UserID, Title, Description, PhotoURL, Location, ContactEmail, ContactPhone, Type)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssssss",
    $userID,
    $title,
    $description,
    $photoURL,
    $location,
    $contact_email,
    $contact_phone,
    $type
);

$stmt->execute();
$announcementID = $stmt->insert_id;
$stmt->close();

/* =====================
   TYPE SPECIFIC INSERT
===================== */
if ($type === 'CatAdoption') {

    $age        = $_POST['age'] ?? '';
    $gender     = $_POST['gender'] ?? '';
    $vaccinated = $_POST['vaccinated'] ?? '';
    $neutered   = $_POST['neutered'] ?? '';
    $requirements = $_POST['adoption_requirements'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO CatAdoption
        (AnnouncementID, Age, Gender, Vaccinated, Neutered, AdoptionRequirements)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssss",
        $announcementID,
        $age,
        $gender,
        $vaccinated,
        $neutered,
        $requirements
    );

    $stmt->execute();
    $stmt->close();

    header("Location: adoptable-cats.php");
    exit;
}

/* =====================
   LOST CAT
===================== */
if ($type === 'LostCat') {

    $catName     = $_POST['cat_name'] ?? '';
    $dateLost    = $_POST['date_lost'] ?? '';
    $lastSeen    = $_POST['last_seen_location'] ?? '';
    $reward      = $_POST['reward_offered'] ?? 'No';
    $features    = $_POST['distinct_features'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO LostCat
        (AnnouncementID, CatName, DateLost, LastSeenLocation, RewardOffered, DistinctFeatures)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssss",
        $announcementID,
        $catName,
        $dateLost,
        $lastSeen,
        $reward,
        $features
    );

    $stmt->execute();
    $stmt->close();

    header("Location: lost-cats.php");
    exit;
}

/* =====================
   SICK CAT
===================== */
if ($type === 'SickCat') {

    $symptoms     = $_POST['symptoms'] ?? '';
    $urgency      = $_POST['urgency'] ?? '';
    $dateNoticed  = $_POST['date_noticed'] ?? '';
    $foundLoc     = $_POST['found_location'] ?? '';
    $needs        = $_POST['needs'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO SickCat
        (AnnouncementID, Symptoms, Urgency, DateNoticed, FoundLocation, Needs)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssss",
        $announcementID,
        $symptoms,
        $urgency,
        $dateNoticed,
        $foundLoc,
        $needs
    );

    $stmt->execute();
    $stmt->close();

    header("Location: sick.php");
    exit;
}
?>
