<?php
session_start();
include '../db.php';

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role'] != 'super_admin'){
    die("You are not authorized to view this page.");
}

/* DELETE USER */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE user_id='$id'");
    header("Location: users.php");
    exit();
}

/* GET USERS */
$users = mysqli_query(
    $conn,
    "SELECT * FROM users ORDER BY user_id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users | TrustFund Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body{
    background:#f6f6f6;
    font-family:Arial,sans-serif;
}
.wrapper{
    padding:40px;
}
.page-title{
    font-size:38px;
    font-weight:800;
    color:#111;
    margin-bottom:28px;
}
.card{
    background:white;
    border:none;
    border-radius:22px;
    padding:25px;
    box-shadow:0 8px 24px rgba(0,0,0,0.05);
}
.table th{
    color:#9b59b6;
    font-weight:700;
}
.role{
    padding:7px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}
.super-admin{
    background:#f3e8fb;
    color:#9b59b6;
}
.mini-admin{
    background:#e8eafb;
    color:#3949ab;
}
.buyer{
    background:#e9f7ef;
    color:#14804a;
}
.seller{
    background:#fff4cc;
    color:#8a6d00;
}
.btn{
    border-radius:10px;
    font-weight:600;
}
</style>
</head>
<body>
<div class="wrapper">

<h1 class="page-title">Registered Users</h1>

<div class="card">
<table class="table table-hover align-middle">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Seller Status</th>
<th>Role</th>
<th>Verified</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($users)): ?>
<tr>
<td><?php echo $row['user_id']; ?></td>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo ucfirst($row['seller_status']); ?></td>
<td>
    <?php if($row['role'] == 'super_admin'): ?>
        <span class="role super-admin">Super Admin</span>
    <?php elseif($row['role'] == 'mini_admin'): ?>
        <span class="role mini-admin">Mini Admin</span>
    <?php elseif($row['seller_status'] == 'approved'): ?>
        <span class="role seller">Seller</span>
    <?php else: ?>
        <span class="role buyer">Buyer</span>
    <?php endif; ?>
</td>
<td>
    <?php if($row['is_verified'] == 1): ?>
        <span class="badge bg-success">Verified</span>
    <?php else: ?>
        <span class="badge bg-danger">Not Verified</span>
    <?php endif; ?>
</td>
<td>
    <?php if($row['role'] != 'super_admin'): ?>
        
          <a  href="?delete=<?php echo $row['user_id']; ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Delete this user?')"
        >
            Delete
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