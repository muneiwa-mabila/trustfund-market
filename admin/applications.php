<?php
include '../db.php';

/* APPROVE SELLER */
if(isset($_GET['approve'])){
    $app_id = (int)$_GET['approve'];
    $getUser = mysqli_query($conn, "SELECT user_id FROM applications WHERE application_id='$app_id'");
    $appRow = mysqli_fetch_assoc($getUser);
    $user_id = $appRow['user_id'] ?? 0;
    if($user_id > 0){
        mysqli_query($conn, "UPDATE users SET seller_status='approved' WHERE user_id='$user_id'");
    }
    mysqli_query($conn, "UPDATE applications SET application_status='Approved' WHERE application_id='$app_id'");
    header("Location: applications.php");
    exit();
}

/* REJECT SELLER */
if(isset($_GET['reject'])){
    $app_id = (int)$_GET['reject'];
    $getUser = mysqli_query($conn, "SELECT user_id FROM applications WHERE application_id='$app_id'");
    $appRow = mysqli_fetch_assoc($getUser);
    $user_id = $appRow['user_id'] ?? 0;
    if($user_id > 0){
        mysqli_query($conn, "UPDATE users SET seller_status='none' WHERE user_id='$user_id'");
    }
    mysqli_query($conn, "UPDATE applications SET application_status='Rejected' WHERE application_id='$app_id'");
    header("Location: applications.php");
    exit();
}

/* FETCH APPLICATIONS */
$result = mysqli_query($conn, "SELECT * FROM applications ORDER BY application_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Applications | TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { background:#f4f4f4; font-family:Arial,sans-serif; }

.admin-sidebar {
    width:270px; min-height:100vh;
    background:linear-gradient(180deg,#9b59b6 0%,#7d3fa0 100%);
    position:fixed; left:0; top:0;
    padding:28px 20px; color:white;
    border-top-right-radius:30px;
    border-bottom-right-radius:30px;
    box-shadow:0 10px 35px rgba(155,89,182,0.25);
    display:flex; flex-direction:column; justify-content:space-between;
}
.logo { font-size:32px; font-weight:900; color:white; margin-bottom:40px; padding-left:10px; }
.sidebar-menu { display:flex; flex-direction:column; gap:10px; }
.admin-sidebar a {
    display:flex; align-items:center; gap:14px;
    color:white; text-decoration:none;
    padding:15px 18px; border-radius:18px;
    transition:0.25s; font-size:15px; font-weight:600;
}
.admin-sidebar a:hover { background:rgba(255,255,255,0.16); transform:translateX(4px); }
.admin-sidebar a.active { background:white; color:#9b59b6; box-shadow:0 10px 25px rgba(0,0,0,0.08); }

.admin-main { margin-left:290px; padding:35px; }
.page-title { font-size:38px; font-weight:800; color:#111; margin-bottom:8px; }
.page-subtitle { color:#777; margin-bottom:30px; }

.apps-grid { display:flex; flex-direction:column; gap:20px; }

.app-card {
    background:white; border-radius:20px; padding:28px;
    box-shadow:0 6px 24px rgba(0,0,0,0.05);
    border:1px solid #f0e8f8;
}
.app-card-header {
    display:flex; justify-content:space-between;
    align-items:flex-start; flex-wrap:wrap;
    gap:12px; margin-bottom:20px;
}
.app-card-header h3 { font-size:20px; font-weight:800; color:#111; margin:0; }
.app-id { font-size:12px; color:#bbb; margin-top:3px; }

.status-badge { padding:7px 16px; border-radius:999px; font-size:12px; font-weight:700; }
.pending  { background:#f3e8fb; color:#9b59b6; }
.approved { background:#e8f8ee; color:#1b9c5a; }
.rejected { background:#ffeaea; color:#e53935; }

.info-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));
    gap:14px; margin-bottom:20px;
}
.info-item label {
    font-size:11px; font-weight:700; color:#aaa;
    text-transform:uppercase; letter-spacing:0.5px;
    display:block; margin-bottom:4px;
}
.info-item span { font-size:14px; color:#222; font-weight:500; }

.id-doc-section {
    background:#faf5fd; border:1px solid #e8d5f5;
    border-radius:14px; padding:16px 20px; margin-bottom:20px;
    display:flex; align-items:center; gap:16px; flex-wrap:wrap;
}
.id-doc-section .doc-label { font-size:13px; font-weight:700; color:#9b59b6; white-space:nowrap; }
.id-doc-thumb { width:80px; height:60px; object-fit:cover; border-radius:8px; border:1px solid #ddd; cursor:pointer; }
.id-doc-section a { font-size:13px; color:#9b59b6; font-weight:600; text-decoration:none; }
.id-doc-section a:hover { text-decoration:underline; }
.no-doc { font-size:13px; color:#bbb; font-style:italic; }

.app-actions { display:flex; gap:10px; flex-wrap:wrap; }
.btn-approve {
    background:#9b59b6; color:white; border:none;
    border-radius:10px; padding:10px 22px;
    font-weight:700; font-size:13px;
    text-decoration:none; display:inline-block; transition:0.2s;
}
.btn-approve:hover { background:#7d3fa0; color:white; }
.btn-reject {
    background:#ffeaea; color:#e53935; border:none;
    border-radius:10px; padding:10px 22px;
    font-weight:700; font-size:13px;
    text-decoration:none; display:inline-block; transition:0.2s;
}
.btn-reject:hover { background:#ffd0d0; color:#e53935; }
.completed-text { color:#999; font-weight:700; font-size:13px; }
.submitted-at { font-size:12px; color:#aaa; margin-top:4px; }

@media(max-width:1000px){
    .admin-sidebar { position:relative; width:100%; min-height:auto; border-radius:0; }
    .admin-main { margin-left:0; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="admin-sidebar">
  <div>
    <div class="logo">TrustFund</div>
    <div class="sidebar-menu">
      <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
      <a href="applications.php" class="active"><i class="fa-solid fa-file-circle-check"></i> Seller Applications</a>
      <a href="listings.php"><i class="fa-solid fa-box"></i> Listings</a>
      <a href="messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
      <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
    </div>
  </div>
  <a href="../index.php"><i class="fa-solid fa-globe"></i> View Website</a>
</div>

<!-- MAIN -->
<div class="admin-main">

  <div class="page-title">Seller Applications</div>
  <div class="page-subtitle">Approve or reject marketplace seller requests</div>

  <div class="apps-grid">

  <?php if(mysqli_num_rows($result) > 0): ?>
  <?php while($row = mysqli_fetch_assoc($result)): ?>

  <?php
    $status = $row['application_status'];
    $cls    = strtolower(trim($status));
    $ext    = !empty($row['id_document']) ? strtolower(pathinfo($row['id_document'], PATHINFO_EXTENSION)) : '';
  ?>

  <div class="app-card">

    <div class="app-card-header">
      <div>
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <div class="app-id">
          Application #<?php echo $row['application_id']; ?> &nbsp;·&nbsp;
          <span class="submitted-at"><?php echo $row['created_at']; ?></span>
        </div>
      </div>
      <span class="status-badge <?php echo $cls; ?>"><?php echo $status; ?></span>
    </div>

    <div class="info-grid">
      <div class="info-item">
        <label>Email</label>
        <span><?php echo htmlspecialchars($row['email']); ?></span>
      </div>
      <div class="info-item">
        <label>Phone</label>
        <span><?php echo htmlspecialchars($row['phone']); ?></span>
      </div>
      <div class="info-item">
        <label>SA ID Number</label>
        <span><?php echo htmlspecialchars($row['id_number']); ?></span>
      </div>
      <div class="info-item">
        <label>Category</label>
        <span><?php echo htmlspecialchars($row['category']); ?></span>
      </div>
    </div>

    <div class="id-doc-section">
      <span class="doc-label">&#128273; ID Document:</span>

      <?php if(!empty($row['id_document'])): ?>
        <?php if($ext === 'pdf'): ?>
          <i class="fa-solid fa-file-pdf" style="font-size:32px;color:#e53935;"></i>
          <a href="../<?php echo htmlspecialchars($row['id_document']); ?>" target="_blank">View PDF Document</a>
        <?php else: ?>
          <img
            src="../<?php echo htmlspecialchars($row['id_document']); ?>"
            class="id-doc-thumb"
            alt="ID Document"
            onclick="openModal('../<?php echo htmlspecialchars($row['id_document']); ?>')"
            title="Click to enlarge"
          >
          <a href="../<?php echo htmlspecialchars($row['id_document']); ?>" target="_blank">Open full size</a>
        <?php endif; ?>
      <?php else: ?>
        <span class="no-doc">No ID document uploaded</span>
      <?php endif; ?>
    </div>

    <div class="app-actions">
      <?php if($cls === 'pending'): ?>
        <a href="?approve=<?php echo $row['application_id']; ?>" class="btn-approve">
          <i class="fa-solid fa-check"></i> Approve
        </a>
        <a href="?reject=<?php echo $row['application_id']; ?>" class="btn-reject">
          <i class="fa-solid fa-xmark"></i> Reject
        </a>
      <?php elseif($cls === 'approved'): ?>
        <a href="?reject=<?php echo $row['application_id']; ?>" class="btn-reject">Revoke Approval</a>
      <?php elseif($cls === 'rejected'): ?>
        <a href="?approve=<?php echo $row['application_id']; ?>" class="btn-approve">Re-approve</a>
      <?php else: ?>
        <span class="completed-text">Completed</span>
      <?php endif; ?>
    </div>

  </div>

  <?php endwhile; ?>
  <?php else: ?>
    <div style="background:white;border-radius:20px;padding:40px;text-align:center;color:#aaa;">
      No seller applications found.
    </div>
  <?php endif; ?>

  </div>
</div>

<!-- IMAGE MODAL -->
<div id="imgModal" onclick="closeModal()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:9999;
            align-items:center;justify-content:center;cursor:zoom-out;">
  <div onclick="event.stopPropagation()" style="max-width:90vw;max-height:90vh;position:relative;">
    <img id="modalImg" src="" style="max-width:90vw;max-height:85vh;border-radius:14px;display:block;">
    <button onclick="closeModal()"
            style="position:absolute;top:-14px;right:-14px;background:white;border:none;
                   border-radius:50%;width:32px;height:32px;font-weight:700;font-size:16px;
                   cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
      &times;
    </button>
  </div>
</div>

<script>
function openModal(src){
    document.getElementById('modalImg').src = src;
    document.getElementById('imgModal').style.display = 'flex';
}
function closeModal(){
    document.getElementById('imgModal').style.display = 'none';
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });
</script>

</body>
</html>