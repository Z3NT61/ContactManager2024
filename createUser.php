<?php
include "db.php"; // Include your database connection details

$registerResult = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $user = $_POST['loginName'];
    $pass = $_POST['loginPassword'];

    // Basic input validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($user) || empty($pass)) {
        $registerResult = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registerResult = "Invalid email format.";
    } else {
        // Check if the username or email already exists in the database
        $sql = "SELECT * FROM MAINUSERS WHERE Login = '$user' OR Email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $registerResult = "Username or email already exists.";
        } else {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $sql = "INSERT INTO MAINUSERS (FirstName, LastName, Email, Login, Password, DateCreated)
                    VALUES ('$firstName', '$lastName', '$email', '$user', '$hashedPassword', NOW())";

            if ($conn->query($sql) === TRUE) {
                // Redirect to index.html after successful registration
                header('Location: index.html');
                exit(); // Ensure no further code is executed
            } else {
                $registerResult = "Error: " . $conn->error;
            }
        }
    }
}

$conn->close();

// If there is an error, display it on the form
if (!empty($registerResult)) {
    echo "<div class='error-message'>" . $registerResult . "</div>";
}
?>
