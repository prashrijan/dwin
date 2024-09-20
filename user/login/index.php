<?php
require_once '../connect.php';  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Real Estate - Home</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <h1>Core Real Estate</h1>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#properties">Properties</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="container">
                <h2>Welcome to Core Real Estate, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h2>
                <p>Discover your dream home with us.</p>
                <a href="#properties" class="btn">View Properties</a>
            </div>
        </section>

        <section id="properties">
            <div class="container">
                <h2>Featured Properties</h2>
                <!-- Add property listings here -->
                <div class="property-grid">
                    <div class="property-card">
                        <img src="../images/property1.jpg" alt="Property 1">
                        <h3>Luxury Villa</h3>
                        <p>$1,200,000</p>
                    </div>
                    <div class="property-card">
                        <img src="../images/property2.jpg" alt="Property 2">
                        <h3>Modern Apartment</h3>
                        <p>$500,000</p>
                    </div>
                    <!-- Add more property cards as needed -->
                </div>
            </div>
        </section>

        <section id="about">
            <div class="container">
                <h2>About Core Real Estate</h2>
                <?php
                // Fetch the "About Us" content from the database
                $aboutQuery = "SELECT content FROM pages WHERE title = 'About Us' LIMIT 1";
                $aboutResult = $conn->query($aboutQuery);
                
                if ($aboutResult && $aboutResult->num_rows > 0) {
                    $aboutContent = $aboutResult->fetch_assoc()['content'];
                    echo "<p>" . htmlspecialchars($aboutContent) . "</p>";
                } else {
                    echo "<p>Information about Core Real Estate is currently unavailable.</p>";
                }
                ?>
            </div>
        </section>

        <section id="contact">
            <div class="container">
                <h2>Contact Us</h2>
                <form>
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Core Real Estate. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>