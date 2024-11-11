<?php
// Start session
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input and sanitize it
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation to ensure fields are not empty
    if (!empty($username) && !empty($email) && !empty($password)) {
        // Check if the users.txt file exists
        $file = 'users.txt';

        // Check if the username or email already exists in users.txt
        $userExists = false;
        if (file_exists($file)) {
            $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($users as $user) {
                list($storedUser, $storedEmail, $storedPass) = explode(',', $user);
                if ($username === $storedUser || $email === $storedEmail) {
                    $userExists = true;
                    break;
                }
            }
        }

        if ($userExists) {
            echo "Username or email already exists. Please try a different one.";
        } else {
            // Append new user data to users.txt (store password as plain text)
            $newUser = $username . ',' . $email . ',' . $password . PHP_EOL;
            file_put_contents($file, $newUser, FILE_APPEND);

            // Log the user in by setting session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            // Redirect to index.php after successful registration
            header("Location: index.php");
            exit();
        }
    } else {
        echo "All fields are required.";
    }
}
?>
