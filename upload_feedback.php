<?php
session_start();

$message = isset($_SESSION['upload_message']) ? $_SESSION['upload_message'] : "No action performed.";
$is_success = strpos($message, '✅') !== false;

// Clear the session message
unset($_SESSION['upload_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Feedback</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .feedback { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 400px; text-align: center; }
        .message { padding: 15px; border-radius: 4px; font-weight: bold; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="feedback">
        <div class="message <?php echo $is_success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <p><a href="upload_form.html">Upload another file</a></p>
    </div>
</body>
</html>