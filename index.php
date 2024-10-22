<?php
// Start session to manage login state
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Forum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <!-- <a href="index.php" class="home-button">Home</a> -->
        <h1>Welcome to the News Forum</h1>
        <!-- Display logout button if user is logged in -->
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <a href="logout.php" class="logout-button">Logout</a>
        <?php endif; ?>
        <nav>
            <!-- Display login/register links if user is not logged in -->
            <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
                <a href="login.html">Login</a>
                <a href="register.html">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <section id="#articlesHolder">
            <h2>Latest News</h2>
            <?php
                $fileName = "articlesDatabase.txt";
                
                if (file_exists($fileName)) {
                    $content = file_get_contents($fileName);
                
                    $lines = explode("\n", $content);
    
                    for ($i = 0; $i < count($lines); $i += 4) {
                        $article = "<article>";

                        $title = substr($lines[$i], 7);
                        $text = substr($lines[$i + 1],6);
                        $author = substr($lines[$i + 2],8);

                        $article .= "<h3>" . $title ."</h3>";
                        $article .= "<p>". $text ."</p>";
                        $article .= "<p class=\"article__author\">". $author ."</p>";

                        $article .= "</article>";

                        echo $article;
                    }
                }
            ?>
            <!-- mqsto za novinite -->
        </section>
    </main>
    <footer>
        <p>&copy; 2024 News Forum</p>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
