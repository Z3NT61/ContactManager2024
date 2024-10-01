<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    echo json_encode(['error' => 'User is not logged in.']);
    exit();
}

// Set user ID
$userId = $_SESSION['UserID'];

// Database connection
$conn = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB");

// Check for connection error
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Query to fetch contacts for the logged-in user, including ContactID
$sql = "SELECT CONTACTS.ID AS ContactID, FirstName, LastName, Email 
        FROM CONTACTS 
        JOIN USERCONTACTS ON CONTACTS.ID = USERCONTACTS.ContactID
        WHERE USERCONTACTS.UserID = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare query: ' . $conn->error]);
    $conn->close();
    exit();
}

// Bind user ID parameter
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Collect contacts with ContactID
$contacts = [];
while ($row = $result->fetch_assoc()) {
    $contacts[] = [
        'contactId' => $row['ContactID'],  // Include the ContactID in the response
        'firstName' => $row['FirstName'],
        'lastName' => $row['LastName'],
        'email' => $row['Email']
    ];
}

// Close statement and connection
$stmt->close();
$conn->close();

// Return contacts as JSON
echo json_encode(['contacts' => $contacts]);
?>
