<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Paws Connect – Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="StyleCSS.css">
</head>

<body>
  <header>
    <div class="logo">
      <img src="PawLogo.png" alt="Paws Connect Logo" class="logo-image">
      <span class="logo-text">Paws Connect</span>
    </div>

    <nav>
      <ul class="nav">
        <li><a href="home_main.php" class="home-btn">Home</a></li> 
        <li><a href="adoptable-cats.html">Adopt Cats</a></li>
        <li><a href="lost-cats.html">Lost Cats</a></li>
        <li><a href="sick.html">Sick Cats</a></li>
        <li><a href="login.html">Log In</a></li> 


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


  <section class="About">
    <div class="About-content">
      <h1>Find Your Furever Friend!</h1>
      <p>
        Because every cat deserves love, safety, and a second chance.<br>
        Be part of Paws Connect — a community that reunites lost cats <br>
        finds loving homes, and supports cats in need of medical care.
      </p>
      <div class="hero-buttons">
        <?php
        if (isset($_SESSION["UserID"])) {
            // Logged In: Keep the area empty, as requested.
        } else {
            // Logged Out: Show Log In and Sign Up buttons.
            echo '<a href="login.html" class="btn-hero login">Log In</a>';
            echo '<a href="signup.html" class="btn-hero signup">Sign Up</a>';
        }
        ?>
      </div>
    </div>
  </section>


  <main>
    <section class="home-cards">
      <a href="adoptable-cats.html" class="card">
        <img src="adoptable-cat.png" alt="Adopt cats" class="card-img">
        <h3>Adopt Cats</h3>
        <p>Browse Adoptables</p>
      </a>

      <a href="lost-cats.html" class="card">
        <img src="LostCat.jpg" alt="Lost cats" class="card-img">
        <h3>Lost Cats</h3>
        <p>Search Lost Cats</p>
      </a>

      <a href="sick.html" class="card">
        <img src="SickCat.jpg" alt="Sick cats" class="card-img">
        <h3>Sick Cats</h3>
        <p>Support & Report</p>
      </a>
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
          <img src="instaLogo.png" alt="Instagram">
          <img src="XLogo.png" alt="X App">
          <img src="FacebookLogo.png" alt="Facebook">
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
