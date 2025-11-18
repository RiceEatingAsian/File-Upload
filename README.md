# File-Upload
HTML form configured with enctype="multipart/form-data" and a file input field for selecting the image.

### 8. File Upload (Server-Side)

This experiment introduces file handling, a critical server-side task, focusing on security and proper validation.

| File | Description |
| :--- | :--- |
| **Database Update** | Adds the `profile_picture_path` column to the `users` table. |
| `upload_form.html` | Uses the required `enctype="multipart/form-data"` for the form and accepts an image file. |
| `process_upload.php` | Validates file type (JPG/PNG) and size (2MB limit). Securely moves the file to the **`uploads/`** directory using a unique filename, and stores the resulting path in the database via a **prepared UPDATE statement**. **Requires the `uploads/` directory to exist and be writable.** |
