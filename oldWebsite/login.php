<?php
// Start session
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input and sanitize it
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Define regex patterns
    $usernamePattern = "/^[a-zA-Z0-9_]{3,15}$/"; // Username: 3-15 characters, letters, numbers, underscores
    $passwordPattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/"; // Password: min 8 chars, 1 uppercase, 1 lowercase, 1 digit

    // Validate username with regex
    if (!preg_match($usernamePattern, $username)) {
        echo "Invalid username. It must be 3-15 characters long and can only contain letters, numbers, and underscores.<br>";
    }

    // Validate password with regex
    if (!preg_match($passwordPattern, $password)) {
        echo "Invalid password. It must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.<br>";
    }

    // Basic validation to ensure fields are not empty
    if (!empty($username) && !empty($password)) {
        // Path to the users.txt file
        $file = 'users.txt';

        // Check if the file exists
        if (file_exists($file)) {
            // Read the users.txt file line by line
            $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $loginSuccess = false;

            foreach ($users as $user) {
                // Each line in users.txt is in the format: username,email,password
                list($storedUser, $storedEmail, $storedPass) = explode(',', $user);

                // Check if the username matches and password matches the plain text password
                if ($username === $storedUser && $password === $storedPass) {
                    // Set session variables
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;

                    // Redirect to index.php
                    $loginSuccess = true;
                    header("Location: index.php");
                    exit();
                }
            }

            // If login failed, show an error message
            if (!$loginSuccess) {
                echo "Invalid username or password.";
            }
        } else {
            echo "No user records found. Please register first.";
        }
    } else {
        echo "Please enter both username and password.";
    }
}
?>
