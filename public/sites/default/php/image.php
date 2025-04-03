<?php

myRequireOnce('dirMake.php');

function imageStore($p)
{
    if (!isset($p['directory'])) {
        trigger_error("Directory not set in uploadImage", E_USER_ERROR);
        return null;
    }

    $file = $_FILES['file'] ?? null;
    if (!$file || !isset($file['type'], $file['name'], $file['tmp_name'])) {
        trigger_error("Invalid file upload", E_USER_ERROR);
        return null;
    }

    $allowedTypes = [
        'image/png'  => '.png',
        'image/jpeg' => '.jpg',
        'image/gif'  => '.gif',
    ];

    writeLog('image-15', "I am in imageStore\nfiletype: {$file['type']}");

    if (!array_key_exists($file['type'], $allowedTypes)) {
        trigger_error("Attempting to upload a file that is not an image", E_USER_ERROR);
        return "Invalid file type";
    }

    $dir = ROOT_EDIT . $p['directory'];
    writeLog('image-20', 'directory: ' . $dir);

    if (!file_exists($dir)) {
        dirMake($dir);
    }

    $extension = $allowedTypes[$file['type']];
    $filename = isset($p['rename']) ? $p['rename'] . $extension : $file['name'];
    $destination = $dir . '/' . $filename;

    writeLog('image-30', 'Saving to: ' . $destination);

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        writeLog('image-41', 'Image Saved');
        return 'success';
    }

    trigger_error("Image NOT Saved", E_USER_ERROR);
    return "Failed to save image";
}
