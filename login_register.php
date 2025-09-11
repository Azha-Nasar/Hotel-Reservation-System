<?php
session_start();
require_once 'config.php';

// ---------------- REGISTER ----------------
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT email FROM guest WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered';
        $_SESSION['active_form'] = 'register';
    } else {
        $stmt = $conn->prepare("INSERT INTO guest (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $stmt->execute();
    }

    header("Location: index.php");
    exit();
}

// ---------------- LOGIN ----------------
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, email, role, password FROM guest WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($password, $result['password'])) {
        // Save session
        $_SESSION["user_id"] = $result['user_id'];
        $_SESSION["name"]    = $result['name'];
        $_SESSION["email"]   = $result['email'];
        $_SESSION["role"]    = $result['role'];

        // Role-based redirects
        switch ($result['role']) {
            case 'manager':
                header("Location: manager_page.php");
                break;
            case 'reservation_clerk':
                header("Location: clerk_page.php");
                break;
            case 'guest':
            case 'travel_agency': // Both go to same home page
                header("Location: home.php");
                break;
            default:
                header("Location: home.php");
                break;
        }
        exit();
    }

    // If login failed
    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header('Location: index.php');
    exit();
}
?>
