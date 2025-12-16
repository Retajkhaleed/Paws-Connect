<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Paws Connect – Lost Cats</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="StyleCSS.css">
</head>
<body>
<header>
    <div class="logo">
      <img src="img/PawLogo.png" alt="Paws Connect Logo" class="logo-image">
      <span class="logo-text">Paws Connect</span>
    </div>

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
            <li><a href="my-announcements.html">My Announcements</a></li>
            <li><a href="saved-announcements.html">Saved Announcements</a></li>
          </ul>
        </li>

        <li><a href="add.html" class="btn">Add Announcement</a></li>
      </ul>
    </nav>
  </header>

  
<section class="LostHero">
  <div class="LostHero-content">
    <h1>Lost Cats Near You</h1>
    <p>Help reunite lost cats with their families</p>
  </div>
</section>

<main class="container">

  <!-- Search -->
  <section class="search-section">
    <div class="search-bar">
      <input type="text" class="search-input" placeholder="Search by name, city, features..." id="searchInput">
      <button class="search-btn" onclick="filterPosts()">Search</button>
    </div>

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
    <button class="post-btn" onclick="location.href='add.html'">Report Lost Cat</button>
  </section>

  <!-- Posts -->
  <section class="posts-section" id="postsContainer">

<?php
$sql = "
SELECT 
    a.AnnouncementID,
    a.Title,
    a.Description,
    a.PhotoURL,
    a.Location,
    a.ContactPhone,
    u.Username,
    l.CatName,
    l.DateLost,
    l.LastSeenLocation,
    l.RewardOffered,
    l.DistinctFeatures
FROM Announcement a
JOIN LostCat l ON a.AnnouncementID = l.AnnouncementID
JOIN Users u ON a.UserID = u.UserID
WHERE a.Type='LostCat'
ORDER BY a.DateCreated DESC
";
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
   data-name="'. strtolower($row['CatName']) .'">

  <div class="cat-card">
    <div class="cat-image">
      <img src="'. htmlspecialchars($firstPhoto) .'" alt="Cat Photo">
    </div>

    <div class="cat-title">
      '. htmlspecialchars($row['Title']) .'
    </div>

    <div class="cat-details">
      <p><strong>Name:</strong> '. htmlspecialchars($row['CatName']) .'</p>
      <p><strong>Date Lost:</strong> '. htmlspecialchars($row['DateLost']) .'</p>
      <p><strong>Last Seen:</strong> '. htmlspecialchars($row['LastSeenLocation']) .'</p>
      <p><strong>Reward:</strong> '. htmlspecialchars($row['RewardOffered']) .'</p>
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
                <h2>No Lost Cats Reported</h2>
                <p>There are currently no lost cats reported. Be the first to help!</p>
                <button class="cta-button" onclick="location.href=\'add.html\'">Report a Lost Cat</button>
            </div>
          </section>';
}
?>


  </section>
</main>

<script>
function filterPosts() {
  const searchText = document.getElementById('searchInput').value.toLowerCase();
  const city = document.getElementById('cityFilter').value.toLowerCase();
  const posts = document.querySelectorAll('.cat-post');

  posts.forEach(post => {
    const postCity = post.dataset.city;
    const postName = post.dataset.name;

    const matchesSearch = postName.includes(searchText) || postCity.includes(searchText);
    const matchesCity = city === "" || postCity === city;

    post.style.display = (matchesSearch && matchesCity) ? "flex" : "none";
  });
}
</script>


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

</body>
</html>


