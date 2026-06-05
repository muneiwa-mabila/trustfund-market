<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$seller_id = $_SESSION['user_id'];

$conversations = mysqli_query(

    $conn,

    "SELECT DISTINCT

    messages.product_id,
    messages.sender_id,
    users.name,
    seller_items.product_name

    FROM messages

    INNER JOIN users
    ON messages.sender_id = users.user_id

    INNER JOIN seller_items
    ON messages.product_id = seller_items.id

    WHERE receiver_id='$seller_id'

    ORDER BY messages.created_at DESC"

);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<title>Seller Messages</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
}

.wrapper{
    max-width:900px;
    margin:40px auto;
}

.card{
    background:white;
    border:none;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.chat-item{
    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:18px;

    border-bottom:1px solid #eee;

    text-decoration:none;
    color:#111;
}

.chat-item:hover{
    background:#fafafa;
}

.open-btn{
    background:#9b59b6;
    color:white;
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none;
    font-size:13px;
    font-weight:700;
}

</style>

</head>

<body>

<div class="wrapper">

<div class="card">

<h2 class="mb-4 fw-bold">
Customer Messages
</h2>

<?php while($chat = mysqli_fetch_assoc($conversations)): ?>

<div class="chat-item">

<div>

<div class="fw-bold">
<?php echo htmlspecialchars($chat['name']); ?>
</div>

<div>
Product: <?php echo htmlspecialchars($chat['product_name']); ?>
</div>

</div>

<a
href="chat.php?seller=<?php echo $seller_id; ?>&product=<?php echo $chat['product_id']; ?>"
class="open-btn"
>
Open Chat
</a>

</div>

<?php endwhile; ?>

</div>

</div>

</body>
</html>