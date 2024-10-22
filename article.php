<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Article Page</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Add your CSS file here -->
</head>
<body>
    <header>
        <h1>News Forum - Articles</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="articles.php">Articles</a>
        </nav>
    </header>

    <main>
        <?php
        // Define the file path to the article text file
        $file = 'article.txt';

        // Check if the file exists
        if (file_exists($file)) {
            // Read the content of the file
            $content = file_get_contents($file);

            // Break content into lines
            $lines = explode("\n", $content);

            // Display the content line by line, formatting as needed
            foreach ($lines as $line) {
                // If the line starts with "Title:", display it as a <h2>
                if (strpos($line, 'Title:') === 0) {
                    $title = substr($line, 7);
                    echo "<title>" . $title . "</title>";
                    
                    echo "<h2>" . $title . "</h2>";
                }
                // If the line starts with "Content:", display it as a <h3>
                elseif (strpos($line, 'Content:') === 0) {
                    echo "<h3>Content</h3>";
                    echo "<p>" . substr($line, 9) . "</p>";
                }
                // For all other lines, display them as paragraphs
                else {
                    echo "<p>" . $line . "</p>";
                }
            }
        } else {
            // If the file does not exist, show an error message
            echo "<p>Sorry, the article could not be found.</p>";
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 News Forum</p>
    </footer>
</body>
</html>
