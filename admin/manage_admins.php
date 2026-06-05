<?php
session_start();
include '../db.php';

/* =========================
   PROTECT PAGE
========================= */
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role'] != 'super_admin'){
    die("Access denied.");
}

/* =========================
   ADD ADMIN BY EMAIL
========================= */
if(isset($_POST['add_admin'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn, "
            UPDATE users 
            SET role='mini_admin' 
            WHERE email='$email'
        ");
        $success = "User promoted to admin.";
    } else {
        $error = "User not found.";
    }
}

/* =========================
   REMOVE ADMIN
========================= */
if(isset($_GET['remove'])){
    $id = (int)$_GET['remove'];

    mysqli_query($conn, "
        UPDATE users 
        SET role='buyer' 
        WHERE user_id='$id'
        AND role != 'super_admin'
    ");

    header("Location: admins.php");
    exit();
}

/* =========================
   GET ADMINS
========================= */
$admins = mysqli_query($conn, "
    SELECT * FROM users 
    WHERE role IN ('mini_admin','super_admin')
    ORDER BY role DESC, user_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f6f6f6;
    font-family:Arial,sans-serif;
}
.container-box{
    max-width:900px;
    margin:auto;
    margin-top:60px;
}
.page-title{
    font-size:34px;
    font-weight:800;
    margin-bottom:25px;
}
.card{
    border:none;
    border-radius:15px;
    padding:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}
.badge{
    padding:6px 12px;
    font-size:12px;
}
</style>

</head>

<body>

<div class="container-box">

<h1 class="page-title">Admin Management</h1>

<!-- ADD ADMIN -->
<form method="POST" class="mb-4">
    <div class="input-group">
        <input type="email" name="email" class="form-control" placeholder="Enter user email" required>
        <button type="submit" name="add_admin" class="btn btn-primary">Add Admin</button>
    </div>
</form>

<!-- MESSAGES -->
<?php if(isset($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- ADMIN LIST -->
<div class="card">
<table class="table table-hover align-middle">

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($admins)): ?>
<tr>

<td><?= $row['user_id']; ?></td>

<td><?= htmlspecialchars($row['name']); ?></td>

<td><?= htmlspecialchars($row['email']); ?></td>

<td>
    <?php if($row['role'] == 'super_admin'): ?>
        <span class="badge bg-dark">Super Admin</span>
    <?php else: ?>
        <span class="badge bg-primary">Mini Admin</span>
    <?php endif; ?>
</td>

<td>
    <?php if($row['role'] != 'super_admin'): ?>
        <a href="?remove=<?= $row['user_id']; ?>" 
           class="btn btn-danger btn-sm"
           onclick="return confirm('Remove admin privileges?')">
           Remove
        </a>
    <?php else: ?>
        —
    <?php endif; ?>
</td>

</tr>
<?php endwhile; ?>

</tbody>

</table>
</div>

</div>

</body>
</html>