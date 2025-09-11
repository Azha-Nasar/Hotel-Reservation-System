<?php
session_start();

// Collect errors from session if they exist
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];

// Which form should be active (login/register)
$activeForm = $_SESSION['active_form'] ?? 'login';

// Clear flash session values (so errors don’t persist on refresh)
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);

// Helper functions
function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation - Login & Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">

        <!-- Login Form -->
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login_register.php" method="post">
                <h2>Login</h2>
                <?= showError($errors['login']); ?>
                
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? 
                    <a href="#" onclick="showForm('register-form')">Register</a>
                </p>
            </form>
        </div>

        <!-- Register Form -->
        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="login_register.php" method="post">
                <h2>Register</h2>
                <?= showError($errors['register']); ?>

                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="User">Guest</option>
                    <option value="Manager">Manager</option>
                    <option value="Reservation_clerk">Reservation Clerk</option>
                    <option value="Travel_agency">Travel Agency</option>
                </select>

                <button type="submit" name="register">Register</button>
                <p>Already have an account? 
                    <a href="#" onclick="showForm('login-form')">Login</a>
                </p>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
