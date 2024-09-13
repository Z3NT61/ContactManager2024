
<?php
#this motherfucker is too painful to connect tonight, do another day.
include "db.php"; // connection file for db

$registerResult = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $user = $_POST['loginName'];
    $pass = $_POST['loginPassword'];

    // this is where we check if the username matches
    $sql = "SELECT * FROM MAINUSERS WHERE Login = '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $registerResult = "Username already exists.";
    } else {
        // if it doesnt match add them to the database
        $sql = "INSERT INTO MAINUSERS (FirstName, LastName, Login, Password, DateCreated)
                VALUES ('$firstName', '$lastName', '$user', '$pass', NOW())";

        if ($conn->query($sql) === TRUE) {
            $registerResult = "Registration successful. You can now log in.";
        } else {
            $registerResult = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Register - Team 27</title>
	<link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">

</head>
<body>


<h1 id="title">Register for Team 27</h1>

<div id="registerDiv">
	<span id="inner-title">REGISTER</span>

	<form action="register.php" method="POST">
		<input type="text" id="firstName" name="firstName" placeholder="First Name" required /><br />
		<input type="text" id="lastName" name="lastName" placeholder="Last Name" required /><br />
		<input type="text" id="loginName" name="loginName" placeholder="Username" required /><br />
		<input type="password" id="loginPassword" name="loginPassword" placeholder="Password" required /><br />
		<button type="submit" id="registerButton" class="buttons">Register</button>
		<button type="button" id="loginButton" class="buttons" onclick="window.location.href='index.php';">Back to Login</button>
	</form>

	<br />
	<span id="registerResult"><?php echo $registerResult; ?></span>
</div>


</body>
</html>
<style>
        /* Inline styling for simplicity */
        body {
        margin: 0;
        padding: 0;
        height: 100vh;
        font-family: 'Ubuntu', sans-serif;
        color: #333;
        background: url('css/KawasakiNinja.jpg') no-repeat center center fixed;
        background-size: cover;
    }
        h1#title {
            color: #ff6347; /* Bright tomato color */
            text-align: center;
            font-size: 2.5em;
        }
        #registerDiv {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            margin: 50px auto;
            text-align: center;
        }
        #inner-title {
            font-size: 1.5em;
            color: #ff4500; /* OrangeRed */
            font-weight: bold;
        }
        input {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #ff6347; /* Brighter border */
            border-radius: 5px;
        }
        input::placeholder {
            color: #888;
        }
        button.buttons {
            background-color: #32cd32; /* Bright lime green */
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin: 5px;
        }
        button.buttons:hover {
            background-color: #228b22; /* Darker green on hover */
        }
        #registerResult {
            color: #ff4500; /* Bright color for registration feedback */
            margin-top: 10px;
        }
        .container {
        background-color: rgba(255, 255, 255, 0.8); /* White background with 80% opacity */
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 30px auto 0 auto; /* Added 30px margin-top */
        text-align: center;
    }


    </style>
