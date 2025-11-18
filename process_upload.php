<?php
session_start();

// ----------------------------------------------------
// Database & File Configuration
// ----------------------------------------------------
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web_experiment_db";
$upload_dir = "uploads/"; // This folder MUST exist and be writable!
$max_file_size = 2 * 1024 * 1024; // 2 MB in bytes

// Redirect function for clean error/success display
function redirect_with_message($message, $is_success = true) {
    $prefix = $is_success ? "✅ Success:" : "❌ Error:";
    $_SESSION['upload_message'] = $prefix . " " . $message;
    // Redirect to a simple feedback page or the form itself
    header("Location: upload_feedback.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_file'])) {
    
    // 1. Validate User ID
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    if (!$user_id) {
        redirect_with_message("Invalid User ID.", false);
    }

    // Check if a file was actually uploaded
    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        redirect_with_message("No file uploaded or an upload error occurred.", false);
    }

    $file = $_FILES['profile_picture'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // 2. Basic Validation: File Type and Size
    
    // Allowed extensions
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    if (!in_array($file_extension, $allowed_extensions)) {
        redirect_with_message("Invalid file type. Only JPG, JPEG, and PNG are allowed.", false);
    }

    // Size check
    if ($file['size'] > $max_file_size) {
        redirect_with_message("File size exceeds the limit of 2MB.", false);
    }
    
    // 3. File Preparation and Storage
    
    // Create a unique file name to prevent overwriting and path traversal attacks
    $new_file_name = $user_id . '_' . uniqid() . '.' . $file_extension;
    $target_file = $upload_dir . $new_file_name;
    
    // Move the uploaded file from the temporary location to the final destination
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        
        // 4. Update Database (Store the File Path)
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            // Log error and rollback the file move in a production environment
            redirect_with_message("Database connection failed. File was uploaded but path not saved.", false);
        }

        // PREPARED STATEMENT for UPDATE
        $sql = "UPDATE users SET profile_picture_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            redirect_with_message("Error preparing statement: " . $conn->error, false);
        }
        
        // Bind parameters: (s=string path, i=integer id)
        $stmt->bind_param("si", $target_file, $user_id);
        
        if ($stmt->execute()) {
            redirect_with_message("File uploaded successfully! Path stored: **{$target_file}**", true);
        } else {
            // Failure to update DB, delete the uploaded file
            unlink($target_file);
            redirect_with_message("Error updating database: " . $stmt->error, false);
        }

        $stmt->close();
        $conn->close();

    } else {
        // Failure to move file (e.g., directory doesn't exist or permissions issue)
        redirect_with_message("File upload failed. Check the 'uploads/' directory permissions.", false);
    }

} else {
    // If accessed directly without POST
    header("Location: upload_form.html");
    exit();
}
?>