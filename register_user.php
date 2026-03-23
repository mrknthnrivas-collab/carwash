<?php
include 'conn.php';//connect.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$username = $_POST['username'];
$email = $_POST['email'];
// Encrypt password
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $password);
if ($stmt->execute()) {
echo "Registration successful! <a href='index.php'>Login here</a>";
} else {
echo "Error: " . $stmt->error;
}
}