<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

/* ONLY RUN ON POST */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    /* user_id is optional now */
    $user_id = $_SESSION['user_id'] ?? NULL;

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');
    $category = trim($_POST['category'] ?? '');

    /* basic validation */
if($name == '' || $email == '' || $phone == '' || $id_number == '' || $category == ''){
    die("Please fill in all fields.");
}

/* ID DOCUMENT UPLOAD */
$id_doc = '';

if (!empty($_FILES['id_document']['name'])) {

    $allowed_exts  = ['jpg', 'jpeg', 'png', 'pdf'];
    $allowed_mimes = ['image/jpeg', 'image/png', 'application/pdf'];

    $ext  = strtolower(pathinfo($_FILES['id_document']['name'], PATHINFO_EXTENSION));
    $mime = mime_content_type($_FILES['id_document']['tmp_name']);

    if (in_array($ext, $allowed_exts) && in_array($mime, $allowed_mimes)) {
        $id_doc = "uploads/id_docs/" . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['id_document']['tmp_name'], $id_doc);
    } else {
        die("Invalid file type. Please upload a JPG, PNG or PDF.");
    }

} else {
    die("Please upload your ID document.");
}

    $stmt = $conn->prepare("
    INSERT INTO applications(
        user_id,
        name,
        email,
        phone,
        id_number,
        category,
        id_document
    )
    VALUES(?,?,?,?,?,?,?)
");

    if(!$stmt){
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
    "issssss",
    $user_id,
    $name,
    $email,
    $phone,
    $id_number,
    $category,
    $id_doc
);

    if(!$stmt->execute()){
        die("Execute failed: " . $stmt->error);
    }

    header("Location: success.php");
    exit();
}

/* if someone opens page directly */
header("Location: index.php");
exit();