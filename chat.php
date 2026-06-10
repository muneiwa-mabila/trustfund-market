<<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$current_user_id = $_SESSION['user_id'];
$seller_id  = $_GET['seller']  ?? 0;
$product_id = $_GET['product'] ?? 0;
$i_am_seller = ($current_user_id == $seller_id);

// GET QUOTE FIRST
$qr = mysqli_query($conn, "SELECT * FROM service_quotes 
    WHERE product_id='$product_id' 
    ORDER BY id DESC LIMIT 1");
$quote = mysqli_fetch_assoc($qr);

// REQUEST SERVICE
if(isset($_GET['request']) && $_GET['request'] == 1 && !$i_am_seller){
    $buyer_id = $current_user_id;
    $check = mysqli_query($conn, "SELECT id FROM service_quotes 
        WHERE buyer_id='$buyer_id' AND product_id='$product_id'");
    if(mysqli_num_rows($check) == 0){
        mysqli_query($conn, "INSERT INTO service_quotes 
            (seller_id, buyer_id, product_id, status)
            VALUES ('$seller_id','$buyer_id','$product_id','Pending')");
        mysqli_query($conn, "INSERT INTO messages 
            (sender_id, receiver_id, product_id, message)
            VALUES ('$buyer_id','$seller_id','$product_id',
            'Hi! I am interested in this service.')");
    }
    header("Location: chat.php?seller=$seller_id&product=$product_id");
    exit();
}

// SEND MESSAGE (POST)
if(isset($_POST['send'])){
    $message = mysqli_real_escape_string($conn, $_POST['message'] ?? '');
    if(!empty($message)){
        if($i_am_seller){
            $receiver = $quote['buyer_id'] ?? 0;
        } else {
            $receiver = $seller_id;
        }
        if($receiver > 0){
            mysqli_query($conn, "INSERT INTO messages 
                (sender_id, receiver_id, product_id, message)
                VALUES ('$current_user_id','$receiver','$product_id','$message')");
        }
        header("Location: chat.php?seller=$seller_id&product=$product_id");
        exit();
    }
}

// GET PRODUCT
$productQuery = mysqli_query($conn, "SELECT * FROM seller_items WHERE id='$product_id'");
$product = mysqli_fetch_assoc($productQuery);

// GET USERS
if($i_am_seller){
    $buyer_id = $quote['buyer_id'] ?? 0;
} else {
    $buyer_id = $current_user_id;
}
$other_id = $i_am_seller ? $buyer_id : $seller_id;
$otherQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$other_id'");
$other = mysqli_fetch_assoc($otherQuery);

// GET CURRENT USER INFO
$meQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$current_user_id'");
$me = mysqli_fetch_assoc($meQuery);

// GET MESSAGES
$messages = mysqli_query($conn, "SELECT * FROM messages
    WHERE product_id='$product_id'
    AND (
        (sender_id='$buyer_id' AND receiver_id='$seller_id')
        OR
        (sender_id='$seller_id' AND receiver_id='$buyer_id')
    )
    ORDER BY created_at ASC");

// Helper: initials from name
function initials($name){
    $parts = explode(' ', trim($name));
    $i = strtoupper(substr($parts[0], 0, 1));
    if(count($parts) > 1) $i .= strtoupper(substr($parts[1], 0, 1));
    return $i;
}

$otherName   = $other['name']  ?? 'User';
$otherRole   = ($other_id == $seller_id) ? 'Seller' : 'Buyer';
$otherInit   = initials($otherName);
$myInit      = initials($me['name'] ?? 'Me');
$productName = $product['product_name'] ?? 'Product';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat — <?php echo htmlspecialchars($productName); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    background: #f0f2f5;
    font-family: 'Segoe UI', Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
}

.chat-wrapper {
    width: 100%;
    max-width: 780px;
}

.chat-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 32px rgba(0,0,0,0.10);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 82vh;
    min-height: 480px;
}

/* HEADER */
.chat-header {
    padding: 18px 22px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 14px;
    background: #fff;
}

.chat-avatar {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #dce8ff;
    color: #4a7fd4;
    font-size: 15px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: 0.5px;
}

.chat-header-info h3 {
    font-size: 16px;
    font-weight: 700;
    color: #111;
    margin-bottom: 2px;
}

.chat-header-info .role-tag {
    font-size: 12px;
    color: #888;
    font-weight: 500;
}

/* PRODUCT BAR */
.product-bar {
    padding: 10px 22px;
    background: #f8f9fb;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
    color: #555;
    display: flex;
    align-items: center;
    gap: 7px;
}

.product-bar i {
    color: #9b59b6;
    font-size: 12px;
}

.product-bar strong {
    color: #222;
    font-weight: 700;
}

/* MESSAGES AREA */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 22px 22px 10px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #f8f9fb;
}

.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }

/* DATE DIVIDER */
.date-divider {
    text-align: center;
    font-size: 11px;
    color: #bbb;
    font-weight: 600;
    position: relative;
    margin: 4px 0;
}
.date-divider::before, .date-divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 42%;
    height: 1px;
    background: #e8e8e8;
}
.date-divider::before { left: 0; }
.date-divider::after  { right: 0; }

/* MESSAGE ROW */
.msg-row {
    display: flex;
    align-items: flex-end;
    gap: 10px;
}

.msg-row.me {
    flex-direction: row-reverse;
}

.msg-row-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #dce8ff;
    color: #4a7fd4;
    font-size: 11px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: 0.5px;
}

.msg-row.me .msg-row-avatar {
    background: #e8f8f0;
    color: #27ae60;
}

.msg-content {
    max-width: 62%;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.msg-row.me .msg-content {
    align-items: flex-end;
}

.msg-bubble {
    padding: 11px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.5;
    color: #333;
    background: #fff;
    border: 1px solid #ececec;
    border-bottom-left-radius: 4px;
    word-break: break-word;
}

.msg-row.me .msg-bubble {
    background: #4a90d9;
    color: #fff;
    border: none;
    border-bottom-right-radius: 4px;
    border-bottom-left-radius: 18px;
}

.msg-time {
    font-size: 10px;
    color: #bbb;
    padding: 0 4px;
}

/* INPUT AREA */
.chat-input-area {
    padding: 14px 18px;
    background: #fff;
    border-top: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chat-input {
    flex: 1;
    border: none;
    background: #f0f2f5;
    border-radius: 24px;
    padding: 12px 20px;
    font-size: 14px;
    color: #333;
    outline: none;
    transition: background 0.2s;
}

.chat-input:focus {
    background: #e8eaf0;
}

.chat-input::placeholder {
    color: #aaa;
}

.send-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #4a90d9;
    border: none;
    color: white;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.2s, transform 0.15s;
}

.send-btn:hover {
    background: #3a7fc9;
    transform: scale(1.06);
}

.no-messages {
    text-align: center;
    color: #ccc;
    font-size: 14px;
    margin: auto;
    padding: 40px 0;
}

@media(max-width: 600px){
    body { padding: 0; align-items: stretch; }
    .chat-card { border-radius: 0; height: 100vh; }
    .msg-content { max-width: 78%; }
}
</style>
</head>
<body>

<div class="chat-wrapper">
<div class="chat-card">

    <!-- HEADER -->
    <div class="chat-header">
        <div class="chat-avatar"><?php echo htmlspecialchars($otherInit); ?></div>
        <div class="chat-header-info">
            <h3><?php echo htmlspecialchars($otherName); ?></h3>
            <span class="role-tag"><?php echo $otherRole; ?></span>
        </div>
    </div>

    <!-- PRODUCT BAR -->
    <div class="product-bar">
        <i class="fa-solid fa-cart-shopping"></i>
        About: <strong><?php echo htmlspecialchars($productName); ?></strong>
    </div>

    <!-- MESSAGES -->
    <div class="chat-messages" id="chatMessages">
        <?php
        $msgs = [];
        while($msg = mysqli_fetch_assoc($messages)) $msgs[] = $msg;

        if(empty($msgs)):
        ?>
            <div class="no-messages">No messages yet. Say hello!</div>
        <?php else:
            $prevDate = '';
            foreach($msgs as $msg):
                $isMe    = ($msg['sender_id'] == $current_user_id);
                $msgDate = date('d M Y', strtotime($msg['created_at']));
                $msgTime = date('g:i A', strtotime($msg['created_at']));
                $init    = $isMe ? $myInit : $otherInit;
        ?>
            <?php if($msgDate !== $prevDate): $prevDate = $msgDate; ?>
                <div class="date-divider"><?php echo $msgDate; ?></div>
            <?php endif; ?>

            <div class="msg-row <?php echo $isMe ? 'me' : ''; ?>">
                <div class="msg-row-avatar"><?php echo htmlspecialchars($init); ?></div>
                <div class="msg-content">
                    <div class="msg-bubble">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                    </div>
                    <span class="msg-time"><?php echo $msgTime; ?></span>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- INPUT -->
    <form method="POST" action="chat.php?seller=<?php echo $seller_id; ?>&product=<?php echo $product_id; ?>" class="chat-input-area">
        <input
            type="text"
            name="message"
            class="chat-input"
            placeholder="Type a message..."
            autocomplete="off"
            required
        >
        <button type="submit" name="send" class="send-btn">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>

</div>
</div>

<script>
    const chatBox = document.getElementById('chatMessages');
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>