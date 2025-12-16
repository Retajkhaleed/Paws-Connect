<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paws Connect | <?php echo htmlspecialchars($announcement['Title']); ?></title>
<link rel="stylesheet" href="StyleCSS.css">
</head>

<body>

<header>
  <div class="logo"><img src="img/logo.png" class="logo-image"><span class="logo-text">Paws Connect</span></div>
    <nav>
      <ul class="nav">
        <li><a href="home_main.php" class="home-btn">Home</a></li> 
        <li><a href="adoptable-cats.php">Adopt Cats</a></li>
        <li><a href="lost-cats.php">Lost Cats</a></li>
        <li><a href="sick.php">Sick Cats</a></li>

        <li class="dropdown">
          <a href="#"> Account <span class="arrow">▲</span> </a>
          <ul class="dropdown-content">
            <li><a href="account.php">Profile</a></li>
            <li><a href="my-announcements.php">My Announcements</a></li>
            <li><a href="saved-announcements.php">Saved Announcements</a></li>
          </ul>
        </li>
        <li><a href="add.html" class="btn">Add Announcement</a></li>
      </ul>
    </nav>
</header>

<section class="SickHero">
  <div class="SickHero-content">
    <h1>Sick Cats Needing Care</h1>
    <p>Give a sick cat hope and a chance to heal</p>
  </div>
</section>

<main class="container">
  <section class="search-section">
    <div class="search-bar">
      <input type="text" class="search-input" placeholder="Search by symptoms..." id="searchInput">
      <button class="search-btn" onclick="filterPosts()">Search</button>
    </div>
    <div class="filters">
            <select class="city-filter" id="cityFilter"> 
                <option value="">All Cities</option>
                <option value="riyadh">Riyadh</option>
                <option value="al-kharj">Al Kharj</option>
                <option value="al-muzahimiyah">Al Muzahimiyah</option>
                <option value="dawadmi">Dawadmi</option>
                <option value="majmaah">Al Majma'ah</option>
                <option value="jeddah">Jeddah</option>
                <option value="mecca">Mecca</option>
                <option value="taif">Taif</option>
                <option value="rabigh">Rabigh</option>
                <option value="khulais">Khulais</option>
                <option value="medina">Medina</option>
                <option value="badr">Badr</option>
                <option value="ulyanah">Ulyanah</option>
                <option value="khaybar">Khaybar</option>
                <option value="dammam">Dammam</option>
                <option value="al-khobar">Al Khobar</option>
                <option value="dhahran">Dhahran</option>
                <option value="jubail">Jubail</option>
                <option value="hafr-al-batin">Hafr Al Batin</option>
                <option value="qatif">Qatif</option>
                <option value="khafji">Khafji</option>
                <option value="buraydah">Buraydah</option>
                <option value="unaizah">Unaizah</option>
                <option value="al-rass">Al Rass</option>
                <option value="hail">Hail</option>
                <option value="baqaa">Baqaa</option>
                <option value="sulayyil">Sulayyil</option>
                <option value="tabuk">Tabuk</option>
                <option value="tayma">Tayma</option>
                <option value="al-wajh">Al Wajh</option>
                <option value="ar-ar">Arar</option>
                <option value="rafha">Rafha</option>
                <option value="turaif">Turaif</option>
                <option value="jazan">Jazan</option>
                <option value="sabya">Sabya</option>
                <option value="abu-aris">Abu Arish</option>
                <option value="najran">Najran</option>
                <option value="shuqra">Shuqra</option>
                <option value="yadamah">Yadamah</option>
                <option value="al-baha">Al Baha</option>
                <option value="baljurashi">Baljurashi</option>
                <option value="ghararah">Ghararah</option>
                <option value="sakurah">Sakakah</option>
                <option value="dumat-al-jandal">Dumat Al Jandal</option>
                <option value="abha">Abha</option>
                <option value="khamis-mushayt">Khamis Mushayt</option>
                <option value="sarat-abidah">Sarat Abidah</option>
            </select>
  </section>

  <section class="posts-section" id="postsContainer">
<?php
$sql = "SELECT a.AnnouncementID, a.Title, a.Description, a.PhotoURL, a.Location, a.ContactPhone, a.Status, 
        s.Symptoms, s.Urgency, s.DateNoticed, s.FoundLocation, s.Needs
        FROM Announcement a
        JOIN SickCat s ON a.AnnouncementID = s.AnnouncementID
        WHERE a.Type='SickCat'
        ORDER BY a.DateCreated DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $photos = !empty($row['PhotoURL']) ? explode(',', $row['PhotoURL']) : [];
        $firstPhoto = !empty($photos[0]) ? trim($photos[0]) : 'img/PawLogo.png';
        if (!file_exists($firstPhoto)) $firstPhoto = 'img/PawLogo.png';

        echo '
<a href="view-announcement.php?id='. $row['AnnouncementID'] .'"
   class="cat-post"
   data-city="'. strtolower($row['Location']) .'"
   data-condition="'. strtolower($row['Symptoms']) .'">

  <div class="cat-card">
    <div class="cat-image">
      <img src="'. htmlspecialchars($firstPhoto) .'" alt="Sick Cat">
    </div>

    <div class="cat-title">
      '. htmlspecialchars($row['Title']) .'
    </div>

    <div class="cat-details">
      <p><strong>Symptoms:</strong> '. htmlspecialchars($row['Symptoms']) .'</p>
      <p><strong>Urgency:</strong> '. htmlspecialchars($row['Urgency']) .'</p>
      <p><strong>Date Noticed:</strong> '. htmlspecialchars($row['DateNoticed']) .'</p>
      <p><strong>Found Location:</strong> '. htmlspecialchars($row['FoundLocation']) .'</p>
      <p><strong>Needs:</strong> '. htmlspecialchars($row['Needs']) .'</p>
      <p><strong>Status:</strong> '. htmlspecialchars($row['Status']) .'</p>
      <p><strong>City:</strong> '. htmlspecialchars($row['Location']) .'</p>
      <p><strong>Contact:</strong> '. htmlspecialchars($row['ContactPhone']) .'</p>
    </div>
  </div>
</a>';
    }
} else {
    echo '<section class="empty-state">
            <div class="empty-content">
                <div class="empty-icon">😿</div>
                <h2>No Sick Cats Reported</h2>
                <p>If you know a sick cat that needs help, please share an announcement.</p>
                <button class="cta-button" onclick="location.href=\'add.html\'">Report Sick Cat</button>
            </div>
          </section>';
}
?>
  </section>
</main>

<footer>
  <div class="footer-content">
    <div class="footer-left">
      <span>Paws Connect 2025 ©</span>
    </div>
    <div class="footer-right">
      <span>Connect With Us</span>
      <div class="social">
        <img src="img/instaLogo.png" alt="Instagram">
        <img src="img/XLogo.png" alt="X App">
        <img src="img/FacebookLogo.png" alt="Facebook">
      </div>
    </div>
  </div>
</footer>

<script>
function filterPosts() {
  const searchText = document.getElementById('searchInput').value.toLowerCase();
  const city = document.getElementById('cityFilter').value.toLowerCase();
  const posts = document.querySelectorAll('.cat-post');

  posts.forEach(post => {
    const postCity = post.dataset.city;
    const postCondition = post.dataset.condition;

    const matchesSearch = postCondition.includes(searchText);
    const matchesCity = city === "" || postCity === city;

    post.style.display = (matchesSearch && matchesCity) ? "flex" : "none"; 
  });
}

document.getElementById('searchInput').addEventListener('keyup', filterPosts);
document.getElementById('cityFilter').addEventListener('change', filterPosts);
</script>

</body>
</html>

