<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

$seller_id  = $_GET['seller'] ?? 0;
$product_id = $_GET['product'] ?? 0;

$is_seller = ($current_user_id == $seller_id);

/* PRODUCT */
$productQuery = mysqli_query($conn, "SELECT * FROM seller_items WHERE id='$product_id'");
$product = mysqli_fetch_assoc($productQuery);

/* QUOTE */
$qr = mysqli_query($conn, "SELECT * FROM service_quotes 
    WHERE product_id='$product_id' 
    ORDER BY id DESC LIMIT 1");
$quote = mysqli_fetch_assoc($qr);

/* SEND MESSAGE */
if(isset($_POST['send'])){
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if($is_seller){
        $receiver = $quote['buyer_id'] ?? 0;
    } else {
        $receiver = $seller_id;
    }

    if($receiver > 0){
        mysqli_query($conn, "INSERT INTO messages
            (sender_id, receiver_id, product_id, message)
            VALUES
            ('$current_user_id','$receiver','$product_id','$message')");
    }

    header("Location: conversation.php?seller=$seller_id&product=$product_id");
    exit();
}

/* OTHER USER */
if($is_seller){
    $other_id = $quote['buyer_id'] ?? 0;
} else {
    $other_id = $seller_id;
}
mysqli_query($conn, "
    UPDATE messages 
    SET is_read = 1 
    WHERE receiver_id='$current_user_id' 
    AND sender_id='$seller_id'
    AND product_id='$product_id'
");

$otherQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$other_id'");
$other = mysqli_fetch_assoc($otherQuery);

/* MESSAGES */
$messages = mysqli_query($conn, "SELECT * FROM messages
WHERE product_id='$product_id'
AND (
(sender_id='$current_user_id' AND receiver_id='$seller_id')
OR
(sender_id='$seller_id' AND receiver_id='$current_user_id')
)
ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Conversation</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{
    font-family:Arial;
    background:#f5f5f5;
    margin:0;
    padding:20px;
}

.box{
    max-width:600px;
    margin:auto;
    background:#fff;
    border-radius:12px;
    padding:20px;
    display:flex;
    flex-direction:column;
    height:90vh;
}

.msg{
    padding:10px;
    margin:8px 0;
    border-radius:10px;
    max-width:70%;
    word-wrap:break-word;
}

.me{
    background:#9b59b6;
    color:#fff;
    margin-left:auto;
}

.them{
    background:#eee;
}

.messages{
    flex:1;
    overflow-y:auto;
    padding-right:5px;
}

form{
    display:flex;
    margin-top:10px;
}

input{
    flex:1;
    padding:10px;
    border-radius:20px;
    border:1px solid #ccc;
    outline:none;
}

button{
    background:#9b59b6;
    color:#fff;
    border:none;
    padding:10px 20px;
    border-radius:20px;
    margin-left:10px;
    cursor:pointer;
}
</style>

</head>

<body>

<div class="box">

<h3><?= htmlspecialchars($product['product_name'] ?? 'Conversation'); ?></h3>

<!-- MESSAGES -->
<div class="messages" id="messagesBox">

<?php while($m = mysqli_fetch_assoc($messages)): ?>

    <div class="msg <?= $m['sender_id']==$current_user_id ? 'me':'them'; ?>">
        <?= htmlspecialchars($m['message']); ?>
    </div>

<?php endwhile; ?>

</div>

<!-- INPUT -->
<form method="POST"
      action="conversation.php?seller=<?= $seller_id; ?>&product=<?= $product_id; ?>">

    <input type="text" name="message" id="msgInput" required placeholder="Type a message...">

    <button type="submit" name="send">Send</button>

</form>

</div>

<!-- ===== MINI WHATSAPP FEATURES ===== -->
<script>

/* AUTO SCROLL */
function scrollBottom(){
    const box = document.getElementById('messagesBox');
    box.scrollTop = box.scrollHeight;
}
scrollBottom();

/* AUTO REFRESH (2 sec) */
setInterval(() => {
    fetch(window.location.href)
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newMsgs = doc.getElementById('messagesBox').innerHTML;
        document.getElementById('messagesBox').innerHTML = newMsgs;
        scrollBottom();
    });
}, 2000);

/* ENTER TO SEND */
document.getElementById('msgInput').addEventListener('keypress', function(e){
    if(e.key === 'Enter'){
        e.preventDefault();
        this.form.submit();
    }
});

</script>

</body>
</html>