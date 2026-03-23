<?php
// session_start();
// include 'db_connect.php';
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $email = $_POST['email'];
//     $password = $_POST['password'];
//     $sql = "SELECT id, username, password FROM users WHERE email=?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     $stmt->store_result();
//     $stmt->bind_result($id, $username, $hashed_password);
//     if ($stmt->fetch() && password_verify($password, $hashed_password)) {
//         $_SESSION['id'] = $id;
//         $_SESSION['username'] = $username;
//         echo "Login successful! Welcome, " . $username;
//     } else {
//         echo "Invalid email or password.";
//     }
// }

session_start();
include 'conn.php'; // Make sure this connects correctly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL to avoid SQL injection
    $sql = "SELECT id, username, password FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // ✅ Create session variables
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;

            // ✅ Redirect to dashboard.html
            header("Location: dashboard.php");
            exit;
        } else {
            // ❌ Wrong password
            echo "<script>alert('Invalid password!'); window.location='login.html';</script>";
        }
    } else {
        // ❌ Email not found
        echo "<script>alert('Email not found!'); window.location='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}

