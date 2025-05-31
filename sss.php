<?php
// Include the database connection
include 'db.php';


// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}

// Handle form submission for login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['profile_picture'] = $user['profile_picture'];
        
        // Create login notification
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Login Successful', 'Welcome back!', 'system')");
        $stmt->execute([$user['id']]);

        header('Location: home.php');
        exit();
    } else {
        $error = "Invalid email or password";
    }
}

// Handle form submission for registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $mobile_number = $_POST['mobile_number'];
    $birth_date = $_POST['birth_date'];

    try {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, mobile_number, birth_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $password, $mobile_number, $birth_date]);
        
        $user_id = $conn->lastInsertId();
        
        // Create welcome notification
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Welcome!', 'Thank you for registering with us.', 'system')");
        $stmt->execute([$user_id]);

        // Set success message and redirect to login
        $_SESSION['registration_success'] = true;
        header('Location: sss.php');
        exit();
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            $error = "Email already exists";
        } else {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup Form</title>
    <style>
        /* CSS Styles */
       @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
    text-decoration: none;
    list-style: none;
}

body{
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: #ffdd8f; /* Updated background color */
}

.container{
    position: relative;
    width: 850px;
    height: 550px;
    background: #ffc300;
    margin: 20px;
    border-radius: 30px;
    box-shadow: 0 0 30px rgba(0, 0, 0, .2);
    overflow: hidden;
}

    .container h1{
        font-size: 36px;
        margin: -10px 0;
    }

    .container p{
        font-size: 14.5px;
        margin: 15px 0;
    }

form{ width: 100%; }

.form-box{
    position: absolute;
    right: 0;
    width: 50%;
    height: 100%;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #333;
    text-align: center;
    padding: 30px;
    z-index: 1;
    transition: .6s ease-in-out 1.2s, visibility 0s 1s;
}

    .container.active .form-box{ right: 50%; }

    .form-box.register {
        background: #ffc300; /* Replace with your desired background color */
        visibility: hidden;
    }
        .container.active .form-box.register{ visibility: visible; }

/* Adjusting the position of the "LOGIN" heading */
.form-box.login h1 {
    margin-bottom: 20px; /* Adjust the spacing below the heading */
    margin-top: -10px; /* Move the heading slightly upward */
}

.form-box.login {
    background: #ffc300; /* Replace with your desired background color */
}

.input-box{
    position: relative;
    margin: 15px 0;
}

    .input-box input{
        width: 100%;
        padding: 12px 50px 12px 20px;
        background: #eee;
        border-radius: 8px;
        border: none;
        outline: none;
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }

        .input-box input::placeholder{
            color: #888;
            font-weight: 400;
        }
    
    .input-box i{
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
    }

.forgot-link {
    margin: 15px 0 0; /* Add more space above the link */
    text-align: center; /* Ensure the link is centered */
}

.forgot-link a {
    font-size: 14.5px;
    color: #333;
}

.btn{
    width: 100%;
    height: 48px;
    background: #1d4b0b; /* Updated button color */
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .1);
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: #fff;
    font-weight: 600;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #163b09; /* Slightly darker shade for hover effect */
}

.social-icons{
    display: flex;
    justify-content: center;
}

    .social-icons a{
        display: inline-flex;
        padding: 10px;
        border: 2px solid #ccc;
        border-radius: 8px;
        font-size: 24px;
        color: #333;
        margin: 0 8px;
    }

.toggle-box{
    position: absolute;
    width: 100%;
    height: 100%;
}

    .toggle-box::before{
        content: '';
        position: absolute;
        left: -250%;
        width: 300%;
        height: 100%;
        background: url('images/Login.png') no-repeat center center; /* Replace with your image path */
        background-size: cover; /* Ensure the image covers the entire area */
        border-radius: 150px;
        z-index: 2;
        transition: 1.8s ease-in-out;
    }

        .container.active .toggle-box::before{ left: 50%; }

.toggle-panel{
    position: absolute;
    width: 50%;
    height: 100%;
    /* background: seagreen; */
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 2;
    transition: .6s ease-in-out;
}

    .toggle-panel.toggle-left{ 
        left: 0;
        transition-delay: 1.2s; 
    }
        .container.active .toggle-panel.toggle-left{
            left: -50%;
            transition-delay: .6s;
        }

    .toggle-panel.toggle-right{ 
        right: -50%;
        transition-delay: .6s;
    }
        .container.active .toggle-panel.toggle-right{
            right: 0;
            transition-delay: 1.2s;
        }

    .toggle-panel p{ margin-bottom: 20px; }

    .toggle-panel .btn{
        width: 160px;
        height: 46px;
        background: transparent;
        border: 2px solid #fff;
        box-shadow: none;
    }

@media screen and (max-width: 650px){
    .container{ height: calc(100vh - 40px); }

    .form-box{
        bottom: 0;
        width: 100%;
        height: 70%;
    }

        .container.active .form-box{
            right: 0;
            bottom: 30%;
        }

    .toggle-box::before{
        left: 0;
        top: -270%;
        width: 100%;
        height: 300%;
        border-radius: 20vw;
    }

        .container.active .toggle-box::before{
            left: 0;
            top: 70%;
        }

        .container.active .toggle-panel.toggle-left{
            left: 0;
            top: -30%;
        }

    .toggle-panel{ 
        width: 100%;
        height: 30%;
    }
        .toggle-panel.toggle-left{ top: 0; }
        .toggle-panel.toggle-right{
            right: 0;
            bottom: -30%;
        }

            .container.active .toggle-panel.toggle-right{ bottom: 0; }
}

@media screen and (max-width: 400px){
    .form-box { padding: 20px; }

    .toggle-panel h1{font-size: 30px; }
}

/* Add styles for success message */
.success-message {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #d4edda;
    color: #155724;
    padding: 15px 30px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 1000;
    animation: fadeOut 5s forwards;
}

@keyframes fadeOut {
    0% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}
    </style>
</head>
<body>
    <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="success-message">Registration successful! Please login with your credentials.</div>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>

    <div class="container">
        <!-- Login Form -->
        <div class="form-box login">
            <form method="POST" action="">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
                <div class="forgot-link">
                    <a href="#">Forgot Password?</a>
                </div>
            </form>
        </div>

        <!-- Registration Form -->
        <div class="form-box register">
            <form method="POST" action="">
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="mobile_number" placeholder="Mobile Number" required>
                    <i class='bx bxs-phone'></i>
                </div>
                <div class="input-box">
                    <input type="date" name="birth_date" placeholder="Birth Date" required>
                    <i class='bx bxs-calendar'></i>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
        </div>

        <!-- Toggle Panels -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script src="sss.js"></script>
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
</body>
</html>