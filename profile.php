<?php
// Include the database connection
include 'db.php';

// Start session to get user information
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: sss.php");
    exit();
}

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_profile'])) {
    $full_name = $_POST['fullname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $birthdate = $_POST['birthdate'];
    
    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/profile_pictures/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
                // Update session with new profile picture
                $_SESSION['profile_picture'] = $profile_picture;
            }
        }
    }
    
    // Update user information in database
    $sql = "UPDATE users SET 
            full_name = '$full_name',
            email = '$email',
            mobile_number = '$mobile',
            birth_date = '$birthdate'";
            
    if (isset($profile_picture)) {
        $sql .= ", profile_picture = '$profile_picture'";
    }
    
    $sql .= " WHERE email = '{$_SESSION['email']}'";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['email'] = $email; // Update session email if changed
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}

// Fetch user data from database
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Store profile picture in session if not already set
    if (!isset($_SESSION['profile_picture']) && !empty($user['profile_picture'])) {
        $_SESSION['profile_picture'] = $user['profile_picture'];
    }
} else {
    header("Location: sss.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .transition-all {
            transition: all 0.3s ease-in-out;
        }
        .profile-card, .profile-details {
            transition: all 0.3s ease;
        }
        .profile-card:hover, .profile-details:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(30, 70, 14, 0.1);
        }
        .profile-details:hover {
            background-color: #fff9e6;
        }
        .profile-card:hover {
            background-color: #fff9e6;
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:hover {
            border-color: #1e460e;
            transform: translateY(-2px);
        }
        .save-button {
            transition: all 0.3s ease;
        }
        .save-button:hover {
            transform: translateY(-2px);
            background-color: #1e460e;
        }
        .profile-picture {
            transition: all 0.3s ease;
        }
        .profile-picture:hover {
            transform: scale(1.05);
            border-color: #2d6a15;
        }
        .about-me {
            transition: all 0.3s ease;
        }
        .about-me:hover {
            background-color: #fff9e6;
            transform: scale(1.02);
        }
        .back-button {
            transition: all 0.3s ease;
        }
        .back-button:hover {
            transform: translateX(-5px);
            background-color: #1e460e;
        }
        .edit-form {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            background-color: white;
            border-radius: 5%;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(30, 70, 14, 0.1);
            flex: 1;
            height: 100%;
        }
        .edit-form.show {
            display: block;
            opacity: 1;
        }
        .edit-form-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .edit-form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .edit-form-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            flex: 1;
            overflow-y: auto;
        }
        .edit-button {
            transition: all 0.3s ease;
            background-color: #1e460e;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .edit-button:hover {
            background-color: #2d6a15;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gradient-to-r from-[#ffdd57] via-[#fff9e6] to-[#ffdd57] text-[#1e460e]">
    <div id="app" class="max-w-6xl mx-auto p-6">
        <h1 class="text-4xl font-bold text-center text-[#1e460e] mb-8">User Profile</h1>
        
        <!-- Main Layout -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Profile Details Section -->
            <div class="bg-white shadow-lg rounded-[5%] p-6 flex-1 relative profile-details">
                <a href="home.php" class="absolute top-4 left-4 bg-[#1e460e] text-white p-2 rounded-md hover:bg-[#2d6a15] transition-colors back-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="flex flex-col items-center justify-center h-full">
                    <img id="profile-picture" src="<?php echo !empty($user['profile_picture']) ? $user['profile_picture'] : 'https://via.placeholder.com/100'; ?>" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover border-4 border-[#1e460e] mb-6 profile-picture">
                    <div class="text-center">
                        <h2 class="text-3xl font-semibold text-gray-800 mb-4" id="fullname"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                        <p class="text-gray-600 mb-2"><strong>Email:</strong> <span id="email"><?php echo htmlspecialchars($user['email']); ?></span></p>
                        <p class="text-gray-600 mb-2"><strong>Mobile Number:</strong> <span id="mobile"><?php echo htmlspecialchars($user['mobile_number']); ?></span></p>
                        <p class="text-gray-600 mb-4"><strong>Birthdate:</strong> <span id="birthdate"><?php echo htmlspecialchars($user['birth_date']); ?></span></p>
                        <button class="edit-button mb-6" id="edit-info-btn">Edit Personal Info</button>
                        
                        <!-- Profile Description -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg about-me">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">About Me</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Passionate software developer with 5 years of experience in web development. 
                                Specialized in creating responsive and user-friendly applications. 
                                Always eager to learn new technologies and solve complex problems.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form Section -->
            <div id="edit-profile-form" class="edit-form">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="edit-form-container">
                        <div class="edit-form-header">
                            <h2 class="text-2xl font-semibold text-gray-800">Edit Profile</h2>
                        </div>
                        <div class="edit-form-content">
                            <div class="w-full">
                                <label for="profile-picture-input" class="block text-sm font-medium text-gray-700">Profile Picture:</label>
                                <div class="mt-2 flex items-center justify-center">
                                    <img id="preview-picture" src="<?php echo !empty($user['profile_picture']) ? $user['profile_picture'] : 'https://via.placeholder.com/100'; ?>" alt="Profile Picture Preview" class="w-32 h-32 rounded-full object-cover border-4 border-[#1e460e] mb-4">
                                </div>
                                <input type="file" id="profile-picture-input" name="profile_picture" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-semibold file:bg-[#ffdd57] file:text-[#1e460e] hover:file:bg-[#fff9e6] input-field">
                            </div>
                            <div class="w-full">
                                <label for="fullname-input" class="block text-sm font-medium text-gray-700">Full Name:</label>
                                <input type="text" id="fullname-input" name="fullname" value="<?php echo htmlspecialchars($user['full_name']); ?>" placeholder="Enter full name" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1e460e] focus:border-[#1e460e] input-field">
                            </div>
                            <div class="w-full">
                                <label for="email-input" class="block text-sm font-medium text-gray-700">Email:</label>
                                <input type="email" id="email-input" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Enter email" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1e460e] focus:border-[#1e460e] input-field">
                            </div>
                            <div class="w-full">
                                <label for="mobile-input" class="block text-sm font-medium text-gray-700">Mobile Number:</label>
                                <input type="tel" id="mobile-input" name="mobile" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" placeholder="Enter mobile number" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1e460e] focus:border-[#1e460e] input-field">
                            </div>
                            <div class="w-full">
                                <label for="birthdate-input" class="block text-sm font-medium text-gray-700">Birthdate:</label>
                                <input type="date" id="birthdate-input" name="birthdate" value="<?php echo htmlspecialchars($user['birth_date']); ?>" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1e460e] focus:border-[#1e460e] input-field">
                            </div>
                            <div class="w-full flex gap-4 mt-4">
                                <button type="submit" name="save_profile" class="flex-1 bg-[#1e460e] text-white py-3 px-4 rounded-md hover:bg-[#2d6a15] focus:ring-2 focus:ring-[#1e460e] focus:ring-offset-2 save-button">Save</button>
                                <button type="button" id="cancel-edit" class="flex-1 bg-[#ffdd57] text-[#1e460e] py-3 px-4 rounded-md hover:bg-[#fff9e6] focus:ring-2 focus:ring-[#ffdd57] focus:ring-offset-2">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const form = document.getElementById('profile-form');
        const profilePictureInput = document.getElementById('profile-picture-input');
        const profilePicture = document.getElementById('profile-picture');
        const previewPicture = document.getElementById('preview-picture');
        const editInfoBtn = document.getElementById('edit-info-btn');
        const editForm = document.getElementById('edit-profile-form');
        const cancelEdit = document.getElementById('cancel-edit');
        const saveButton = document.querySelector('.save-button');

        // Function to show edit form
        function showEditForm() {
            editForm.classList.add('show');
            // Pre-fill form with current values
            document.getElementById('fullname-input').value = document.getElementById('fullname').textContent;
            document.getElementById('email-input').value = document.getElementById('email').textContent;
            document.getElementById('mobile-input').value = document.getElementById('mobile').textContent;
            document.getElementById('birthdate-input').value = document.getElementById('birthdate').textContent;
            previewPicture.src = profilePicture.src;
        }

        // Show edit form when button is clicked
        editInfoBtn.addEventListener('click', showEditForm);

        // Hide edit form
        cancelEdit.addEventListener('click', () => {
            editForm.classList.remove('show');
        });

        profilePictureInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewPicture.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle save button click
        saveButton.addEventListener('click', () => {
            // Update the profile information
            document.getElementById('fullname').textContent = document.getElementById('fullname-input').value;
            document.getElementById('email').textContent = document.getElementById('email-input').value;
            document.getElementById('mobile').textContent = document.getElementById('mobile-input').value;
            document.getElementById('birthdate').textContent = document.getElementById('birthdate-input').value;
            profilePicture.src = previewPicture.src;
            
            // Hide the edit form
            editForm.classList.remove('show');
        });
    </script>
</body>
</html>