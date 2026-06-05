<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*
We get latest message per product conversation
This creates a "chat list"
*/

$query = "
SELECT 
m.product_id,
m.sender_id,
m.receiver_id,
m.message,
m.created_at,

seller_items.product_name,
seller_items.product_image,

users.name AS other_name,
users.user_id AS other_id

FROM messages m

INNER JOIN seller_items 
    ON seller_items.id = m.product_id

INNER JOIN users 
    ON users.user_id = 
        CASE 
            WHEN m.sender_id = '$user_id' THEN m.receiver_id
            ELSE m.sender_id
        END

WHERE m.sender_id = '$user_id' 
   OR m.receiver_id = '$user_id'

ORDER BY m.created_at DESC
";

$result = mysqli_query($conn, $query);

/* store only latest per conversation */
$conversations = [];

while($row = mysqli_fetch_assoc($result)){

    $key = $row['product_id'].'-'.$row['other_id'];

    if(!isset($conversations[$key])){
        $conversations[$key] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Inbox</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>

body{
    font-family:Arial;
    background:#f5f5f5;
    margin:0;
    padding:20px;
}

.container{
    max-width:700px;
    margin:auto;
}

h2{
    margin-bottom:20px;
}

.chat-card{
    background:#fff;
    padding:15px;
    border-radius:12px;
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:10px;
    text-decoration:none;
    color:#111;
    transition:0.2s;
}

.chat-card:hover{
    background:#f1f1f1;
}

.img{
    width:50px;
    height:50px;
    border-radius:10px;
    object-fit:cover;
    background:#ddd;
}

.info{
    flex:1;
}

.name{
    font-weight:700;
    margin-bottom:3px;
}

.msg{
    font-size:13px;
    color:#666;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.time{
    font-size:11px;
    color:#aaa;
}

</style>

</head>

<body>

<div class="container">

<h2>Inbox</h2>

<?php if(empty($conversations)): ?>

    <p>No messages yet.</p>

<?php endif; ?>

<?php foreach($conversations as $c): ?>

<a class="chat-card"
   href="conversation.php?seller=<?= $c['other_id']; ?>&product=<?= $c['product_id']; ?>">

    <img class="img" src="<?= htmlspecialchars($c['product_image']); ?>">

    <div class="info">
        <div class="name"><?= htmlspecialchars($c['other_name']); ?></div>
        <div class="msg"><?= htmlspecialchars($c['message']); ?></div>
    </div>

    <div class="time">
        <?= date('H:i', strtotime($c['created_at'])); ?>
    </div>

</a>

<?php endforeach; ?>

</div>

</body>
</html>