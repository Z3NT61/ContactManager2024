<?php
session_start();
include "db.php"; // Include your database connection details

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $username = $_POST['user'];
    $password = $_POST['password'];

    // Check if both fields are filled
    if (empty($username) || empty($password)) {
        echo "Both username and password are required.";
    } else {
        // Prepare and execute the SQL statement to check the username
        $sql = "SELECT * FROM MAINUSERS WHERE Login = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $user['Password'])) {
                // Password is correct, set the session
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['username'] = $user['Login'];

                // Redirect to contacts.html after successful login
                header('Location: contacts.html');
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "Username not found.";
        }
    }
}
?>

