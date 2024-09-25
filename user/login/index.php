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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #a81e35;
            color: white;
            text-align: center;
            padding: 1rem;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 1rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 500px;
            width: 90%;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .hero {
            background-color: #a81e35;
            color: black;
            padding: 2rem 0;
            text-align: center;
        }

        .hero .btn {
            background-color: white;
            color: #a81e35;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }

        .hero .btn:hover {
            background-color: #f4f4f4;
        }

        .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .property-card {
            background-color: white;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .property-card h3 {
            color: #a81e35;
            margin-bottom: 0.5rem;
        }

        .property-card p {
            margin: 0.5rem 0;
        }

        /* Contact Us Page Styles */
        .contact-container {
            max-width: 600px;
            width: 90%;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .contact-container h2 {
            color: #a81e35;
            text-align: center;
            margin-bottom: 1rem;
        }

        .contact-container form {
            display: flex;
            flex-direction: column;
        }

        .contact-container form input,
        .contact-container form textarea {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 100%;
            box-sizing: border-box;
            font-size: 1rem;
        }

        .contact-container form button {
            background-color: #a81e35;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 1rem;
        }

        .contact-container form button:hover {
            background-color: #8a1a2c;
        }

        /* Featured Property Container Styles */
        .featured-property-container {
            max-width: 1200px;
            width: 90%;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .featured-property-container h2 {
            color: #a81e35;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .featured-property-container .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .featured-property-container .property-card {
            background-color: #f9f9f9;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .featured-property-container .property-card h3 {
            color: #333;
            margin-bottom: 0.75rem;
        }

        .featured-property-container .property-card p {
            margin: 0.75rem 0;
            color: #666;
        }

        .featured-property-container .property-card .price {
            color: #a81e35;
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <h1>Core Real Estate</h1>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#properties">Properties</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
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
            <div class="featured-property-container">
                <h2>Featured Properties</h2>
                <div class="property-grid">
                    <?php
                    // Fetch properties from the database
                    $sql = "SELECT * FROM properties";
                    $result = $conn->query($sql); 

                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<div class='property-card'>";
                            echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                            echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                            echo "<p>Price: $" . htmlspecialchars($row["price"]) . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "No properties found.";
                    }
                    ?>
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
            <div class="contact-container">
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

<!-- this is a test -->