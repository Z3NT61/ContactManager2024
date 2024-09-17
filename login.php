<?php
session_start();
include "db.php"; // Include your database connection details

$loginResult = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $username = $_POST['user'];
    $password = $_POST['password'];

    // Check if both fields are filled
    if (empty($username) || empty($password)) {
        $loginResult = "Both username and password are required.";
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

                // Redirect to a dashboard or contacts page
                header('Location: contacts.html');
                exit();
            } else {
                $loginResult = "LOGGED IN!!!!!";
            }
        } else {
            $loginResult = "Username not found.";
        }
    }
}
?>

