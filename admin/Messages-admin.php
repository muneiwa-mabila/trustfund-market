<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

include '../db.php';

/* HANDLE FLAG */
if(isset($_POST['flag_message'])){
    $flag_id = (int)$_POST['message_id'];
    mysqli_query($conn, "UPDATE messages SET flagged = 1 WHERE id = $flag_id");
}

/* HANDLE UNFLAG */
if(isset($_POST['unflag_message'])){
    $flag_id = (int)$_POST['message_id'];
    mysqli_query($conn, "UPDATE messages SET flagged = 0 WHERE id = $flag_id");
}

/* HANDLE DELETE */
if(isset($_POST['delete_message'])){
    $del_id = (int)$_POST['message_id'];
    mysqli_query($conn, "DELETE FROM messages WHERE id = $del_id");
}

/* ADD flagged COLUMN IF NOT EXISTS (runs once, safe to keep) */
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM messages LIKE 'flagged'");
if(mysqli_num_rows($checkCol) === 0){
    mysqli_query($conn, "ALTER TABLE messages ADD COLUMN flagged TINYINT(1) DEFAULT 0");
}

/* FILTER */
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

/* FETCH CONVERSATIONS grouped by sender/receiver pair + product */
$convQuery = mysqli_query($conn,
    "SELECT
        LEAST(m.sender_id, m.receiver_id) AS user_a,
        GREATEST(m.sender_id, m.receiver_id) AS user_b,
        m.product_id,
        si.product_name,
        COUNT(m.id) AS msg_count,
        SUM(m.flagged) AS flagged_count,
        SUM(m.is_read = 0) AS unread_count,
        MAX(m.created_at) AS last_message,
        (SELECT message FROM messages m2 WHERE
            ((m2.sender_id = LEAST(m.sender_id, m.receiver_id) AND m2.receiver_id = GREATEST(m.sender_id, m.receiver_id))
            OR (m2.sender_id = GREATEST(m.sender_id, m.receiver_id) AND m2.receiver_id = LEAST(m.sender_id, m.receiver_id)))
            AND m2.product_id = m.product_id
            ORDER BY m2.created_at DESC LIMIT 1) AS last_msg_text,
        ua.name AS name_a,
        ub.name AS name_b,
        ua.seller_status AS status_a,
        ub.seller_status AS status_b
    FROM messages m
    LEFT JOIN seller_items si ON si.id = m.product_id
    LEFT JOIN users ua ON ua.user_id = LEAST(m.sender_id, m.receiver_id)
    LEFT JOIN users ub ON ub.user_id = GREATEST(m.sender_id, m.receiver_id)
    GROUP BY user_a, user_b, m.product_id
    ORDER BY last_message DESC"
);

/* OPEN CONVERSATION */
$openConv = null;
$convMessages = [];
if(isset($_GET['conv'])){
    $parts = explode('_', $_GET['conv']);
    if(count($parts) === 3){
        $ua = (int)$parts[0];
        $ub = (int)$parts[1];
        $pid = (int)$parts[2];

        $msgQuery = mysqli_query($conn,
            "SELECT m.*, u.name AS sender_name, u.seller_status AS sender_role
             FROM messages m
             LEFT JOIN users u ON u.user_id = m.sender_id
             WHERE ((m.sender_id = $ua AND m.receiver_id = $ub)
                 OR (m.sender_id = $ub AND m.receiver_id = $ua))
               AND m.product_id = $pid
             ORDER BY m.created_at ASC"
        );
        while($row = mysqli_fetch_assoc($msgQuery)) $convMessages[] = $row;

        $uaInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, seller_status FROM users WHERE user_id = $ua"));
        $ubInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, seller_status FROM users WHERE user_id = $ub"));
        $prodInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT product_name FROM seller_items WHERE id = $pid"));
        $openConv = [
            'ua' => $ua, 'ub' => $ub, 'pid' => $pid,
            'name_a' => $uaInfo['name'], 'name_b' => $ubInfo['name'],
            'role_a' => $uaInfo['seller_status'], 'role_b' => $ubInfo['seller_status'],
            'product' => $prodInfo ? $prodInfo['product_name'] : 'Unknown Product'
        ];
    }
}

/* COUNTS FOR TABS */
$totalConvs = mysqli_num_rows($convQuery);
$flaggedCount = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM messages WHERE flagged = 1"));
$unreadCount  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM messages WHERE is_read = 0"));
mysqli_data_seek($convQuery, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages | TrustFund Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ background:#f4f4f4; font-family:Arial,sans-serif; }

/* SIDEBAR */
.admin-sidebar{
    width:270px; min-height:100vh;
    background:linear-gradient(180deg,#9b59b6 0%,#7d3fa0 100%);
    position:fixed; left:0; top:0; padding:28px 20px;
    color:white; border-top-right-radius:30px; border-bottom-right-radius:30px;
    box-shadow:0 10px 35px rgba(155,89,182,0.25);
    display:flex; flex-direction:column; justify-content:space-between;
}
.logo{ font-size:32px; font-weight:900; color:white; margin-bottom:40px; padding-left:10px; letter-spacing:0.5px; }
.sidebar-menu{ display:flex; flex-direction:column; gap:10px; }
.admin-sidebar a{
    display:flex; align-items:center; gap:14px; color:white; text-decoration:none;
    padding:15px 18px; border-radius:18px; transition:0.25s; font-size:15px; font-weight:600;
}
.admin-sidebar a i{ width:22px; text-align:center; }
.admin-sidebar a:hover{ background:rgba(255,255,255,0.16); transform:translateX(4px); }
.admin-sidebar a.active{ background:white; color:#9b59b6; box-shadow:0 10px 25px rgba(0,0,0,0.08); }
.sidebar-bottom{ margin-top:40px; }

/* MAIN */
.admin-main{ margin-left:290px; padding:35px; }
.topbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.page-title{ font-size:38px; font-weight:800; color:#111; }
.page-subtitle{ color:#777; margin-top:5px; }
.admin-profile{ background:white; padding:12px 18px; border-radius:14px; box-shadow:0 5px 15px rgba(0,0,0,0.05); font-weight:700; }

/* MESSAGES LAYOUT */
.messages-wrap{ display:grid; grid-template-columns:380px 1fr; gap:22px; height:calc(100vh - 180px); min-height:500px; }

/* LEFT PANEL */
.conv-panel{
    background:white; border-radius:22px; box-shadow:0 8px 24px rgba(0,0,0,0.05);
    border:1px solid #eee; display:flex; flex-direction:column; overflow:hidden;
}
.conv-panel-header{ padding:20px; border-bottom:1px solid #f0f0f0; }
.conv-search{
    width:100%; border:1px solid #e5e5e5; border-radius:12px;
    padding:10px 14px 10px 38px; font-size:14px; background:#f9f9f9;
    outline:none; transition:0.2s;
}
.conv-search:focus{ border-color:#9b59b6; background:white; }
.search-wrap{ position:relative; margin-bottom:14px; }
.search-wrap i{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:13px; }
.filter-tabs{ display:flex; gap:8px; }
.filter-tab{
    flex:1; text-align:center; padding:7px 4px; border-radius:10px;
    font-size:12px; font-weight:700; text-decoration:none; color:#777;
    background:#f4f4f4; transition:0.2s; border:none; cursor:pointer;
}
.filter-tab.active{ background:#9b59b6; color:white; }
.filter-tab .badge-num{
    display:inline-block; background:rgba(255,255,255,0.3);
    border-radius:20px; padding:1px 6px; font-size:11px; margin-left:4px;
}
.filter-tab:not(.active) .badge-num{ background:#e0e0e0; color:#555; }
.conv-list{ flex:1; overflow-y:auto; }
.conv-item{
    padding:16px 20px; border-bottom:1px solid #f5f5f5; cursor:pointer;
    transition:0.15s; text-decoration:none; display:block; color:inherit;
}
.conv-item:hover{ background:#faf5ff; }
.conv-item.selected{ background:#f3e8fb; border-left:4px solid #9b59b6; }
.conv-item.flagged-item{ border-left:4px solid #e74c3c; }
.conv-item-top{ display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; }
.conv-names{ font-weight:700; font-size:14px; color:#111; }
.conv-time{ font-size:11px; color:#aaa; }
.conv-product{ font-size:12px; color:#9b59b6; font-weight:600; margin-bottom:4px; }
.conv-preview{ font-size:13px; color:#888; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:280px; }
.conv-badges{ display:flex; gap:5px; margin-top:6px; flex-wrap:wrap; }
.badge-pill{ font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; }
.pill-seller{ background:#e8f8f0; color:#27ae60; }
.pill-buyer{ background:#eef2ff; color:#5c6bc0; }
.pill-flagged{ background:#fdecea; color:#e74c3c; }
.pill-unread{ background:#fff3cd; color:#856404; }
.no-convs{ text-align:center; padding:40px 20px; color:#aaa; }

/* CHAT PANEL */
.chat-panel{
    background:white; border-radius:22px; box-shadow:0 8px 24px rgba(0,0,0,0.05);
    border:1px solid #eee; display:flex; flex-direction:column; overflow:hidden;
}
.chat-empty{
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    height:100%; color:#ccc; gap:14px;
}
.chat-empty i{ font-size:52px; }
.chat-empty p{ font-size:16px; font-weight:600; }
.chat-header{
    padding:18px 24px; border-bottom:1px solid #f0f0f0;
    display:flex; justify-content:space-between; align-items:center;
}
.chat-header-left h3{ font-size:17px; font-weight:800; color:#111; margin-bottom:3px; }
.chat-header-left span{ font-size:13px; color:#9b59b6; font-weight:600; }
.chat-messages{
    flex:1; overflow-y:auto; padding:20px 24px;
    display:flex; flex-direction:column; gap:14px; background:#fafafa;
}
.msg-row{ display:flex; gap:10px; align-items:flex-end; }
.msg-row.right{ flex-direction:row-reverse; }
.msg-avatar{
    width:34px; height:34px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:800; flex-shrink:0; color:white;
}
.avatar-seller{ background:#9b59b6; }
.avatar-buyer{ background:#5c6bc0; }
.msg-bubble{
    max-width:65%; padding:11px 15px; border-radius:18px;
    font-size:14px; line-height:1.5;
}
.msg-row:not(.right) .msg-bubble{
    background:white; border:1px solid #ececec; border-bottom-left-radius:4px; color:#333;
}
.msg-row.right .msg-bubble{
    background:#9b59b6; color:white; border-bottom-right-radius:4px;
}
.msg-bubble.is-flagged{ border:2px solid #e74c3c !important; }
.msg-meta{ font-size:10px; margin-top:4px; display:flex; gap:6px; align-items:center; }
.msg-row:not(.right) .msg-meta{ color:#aaa; }
.msg-row.right .msg-meta{ color:rgba(255,255,255,0.6); justify-content:flex-end; }
.msg-sender-name{ font-weight:700; font-size:11px; margin-bottom:2px; }
.msg-row:not(.right) .msg-sender-name{ color:#9b59b6; }
.msg-row.right .msg-sender-name{ color:rgba(255,255,255,0.8); text-align:right; }
.msg-actions{ display:flex; gap:4px; align-self:center; opacity:0; transition:0.15s; }
.msg-row:hover .msg-actions{ opacity:1; }
.act-btn{
    border:none; background:none; padding:5px 7px; border-radius:8px;
    cursor:pointer; font-size:12px; transition:0.15s;
}
.act-btn.flag-btn{ color:#e74c3c; }
.act-btn.flag-btn:hover{ background:#fdecea; }
.act-btn.unflag-btn{ color:#27ae60; }
.act-btn.unflag-btn:hover{ background:#e8f8f0; }
.act-btn.del-btn{ color:#999; }
.act-btn.del-btn:hover{ background:#f5f5f5; color:#e74c3c; }
.date-divider{
    text-align:center; font-size:11px; color:#bbb; font-weight:700;
    position:relative; margin:6px 0;
}
.date-divider::before, .date-divider::after{
    content:''; position:absolute; top:50%; width:40%; height:1px; background:#ebebeb;
}
.date-divider::before{ left:0; }
.date-divider::after{ right:0; }
.stat-strip{
    padding:12px 24px; border-top:1px solid #f0f0f0;
    background:#fff; display:flex; gap:20px; font-size:12px; color:#888;
}
.stat-strip strong{ color:#111; }
.role-tag{
    font-size:10px; font-weight:700; padding:2px 7px; border-radius:10px; margin-left:6px;
}
.role-seller{ background:#e8f8f0; color:#27ae60; }
.role-buyer{ background:#eef2ff; color:#5c6bc0; }

@media(max-width:1100px){
    .messages-wrap{ grid-template-columns:1fr; height:auto; }
    .conv-panel{ height:400px; }
    .chat-panel{ height:600px; }
}
@media(max-width:700px){
    .admin-sidebar{ position:relative; width:100%; min-height:auto; border-radius:0; }
    .admin-main{ margin-left:0; padding:20px; }
    .topbar{ flex-direction:column; align-items:flex-start; gap:14px; }
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
            <a href="applications.php"><i class="fa-solid fa-file-circle-check"></i> Seller Applications</a>
            <a href="listings.php"><i class="fa-solid fa-box"></i> Listings</a>
            <a href="messages.php" class="active"><i class="fa-solid fa-comments"></i> Messages</a>
            <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        </div>
    </div>
    <div class="sidebar-bottom">
        <a href="../index.php"><i class="fa-solid fa-globe"></i> View Website</a>
    </div>
</div>

<!-- MAIN -->
<div class="admin-main">

    <div class="topbar">
        <div>
            <div class="page-title">Messages</div>
            <div class="page-subtitle">Monitor buyer &amp; seller conversations</div>
        </div>
        <div class="admin-profile">Admin Panel</div>
    </div>

    <div class="messages-wrap">

        <!-- LEFT: CONVERSATION LIST -->
        <div class="conv-panel">
            <div class="conv-panel-header">
                <form method="GET" action="messages.php">
                    <?php if($filter !== 'all'): ?>
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <?php endif; ?>
                    <div class="search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="conv-search"
                            placeholder="Search messages or users..."
                            value="<?php echo htmlspecialchars($search); ?>"
                            onchange="this.form.submit()">
                    </div>
                </form>
                <div class="filter-tabs">
                    <a href="messages.php" class="filter-tab <?php echo $filter==='all'?'active':''; ?>">
                        All <span class="badge-num"><?php echo $totalConvs; ?></span>
                    </a>
                    <a href="messages.php?filter=unread" class="filter-tab <?php echo $filter==='unread'?'active':''; ?>">
                        Unread <span class="badge-num"><?php echo $unreadCount; ?></span>
                    </a>
                    <a href="messages.php?filter=flagged" class="filter-tab <?php echo $filter==='flagged'?'active':''; ?>">
                        <i class="fa-solid fa-flag" style="font-size:10px;"></i> Flagged <span class="badge-num"><?php echo $flaggedCount; ?></span>
                    </a>
                </div>
            </div>

            <div class="conv-list">
                <?php
                $convCount = 0;
                while($conv = mysqli_fetch_assoc($convQuery)):
                    $convCount++;
                    $ua  = $conv['user_a'];
                    $ub  = $conv['user_b'];
                    $pid = $conv['product_id'];
                    $convKey   = "{$ua}_{$ub}_{$pid}";
                    $isSelected = isset($_GET['conv']) && $_GET['conv'] === $convKey;
                    $isFlagged  = $conv['flagged_count'] > 0;
                    $labelA = $conv['status_a'] === 'approved' ? 'Seller' : 'Buyer';
                    $labelB = $conv['status_b'] === 'approved' ? 'Seller' : 'Buyer';
                    $convUrl = "messages.php?conv=$convKey";
                    if($filter !== 'all') $convUrl .= "&filter=$filter";
                    if($search)           $convUrl .= "&search=".urlencode($search);
                ?>
                <a href="<?php echo $convUrl; ?>"
                   class="conv-item <?php echo $isSelected?'selected':''; ?> <?php echo $isFlagged?'flagged-item':''; ?>">
                    <div class="conv-item-top">
                        <span class="conv-names">
                            <?php echo htmlspecialchars($conv['name_a'] ?? 'User '.$ua); ?> &amp;
                            <?php echo htmlspecialchars($conv['name_b'] ?? 'User '.$ub); ?>
                        </span>
                        <span class="conv-time"><?php echo date('d M', strtotime($conv['last_message'])); ?></span>
                    </div>
                    <div class="conv-product">
                        <i class="fa-solid fa-tag" style="font-size:10px;"></i>
                        <?php echo htmlspecialchars($conv['product_name'] ?? 'Product #'.$pid); ?>
                    </div>
                    <div class="conv-preview"><?php echo htmlspecialchars($conv['last_msg_text'] ?? ''); ?></div>
                    <div class="conv-badges">
                        <span class="badge-pill pill-<?php echo strtolower($labelA); ?>">
                            <?php echo htmlspecialchars($conv['name_a'] ?? 'User'); ?>: <?php echo $labelA; ?>
                        </span>
                        <span class="badge-pill pill-<?php echo strtolower($labelB); ?>">
                            <?php echo htmlspecialchars($conv['name_b'] ?? 'User'); ?>: <?php echo $labelB; ?>
                        </span>
                        <?php if($conv['unread_count'] > 0): ?>
                            <span class="badge-pill pill-unread"><?php echo $conv['unread_count']; ?> unread</span>
                        <?php endif; ?>
                        <?php if($isFlagged): ?>
                            <span class="badge-pill pill-flagged"><i class="fa-solid fa-flag" style="font-size:9px;"></i> Flagged</span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endwhile; ?>
                <?php if($convCount === 0): ?>
                    <div class="no-convs">
                        <i class="fa-regular fa-comment-dots" style="font-size:36px; margin-bottom:10px; display:block;"></i>
                        No conversations found.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: OPEN CONVERSATION -->
        <div class="chat-panel">
            <?php if(!$openConv): ?>
                <div class="chat-empty">
                    <i class="fa-regular fa-comments"></i>
                    <p>Select a conversation to view messages</p>
                </div>
            <?php else: ?>
                <div class="chat-header">
                    <div class="chat-header-left">
                        <h3>
                            <?php echo htmlspecialchars($openConv['name_a']); ?>
                            <span class="role-tag role-<?php echo $openConv['role_a']==='approved'?'seller':'buyer'; ?>">
                                <?php echo $openConv['role_a']==='approved'?'Seller':'Buyer'; ?>
                            </span>
                            &nbsp;&amp;&nbsp;
                            <?php echo htmlspecialchars($openConv['name_b']); ?>
                            <span class="role-tag role-<?php echo $openConv['role_b']==='approved'?'seller':'buyer'; ?>">
                                <?php echo $openConv['role_b']==='approved'?'Seller':'Buyer'; ?>
                            </span>
                        </h3>
                        <span><i class="fa-solid fa-tag" style="font-size:11px;"></i> <?php echo htmlspecialchars($openConv['product']); ?></span>
                    </div>
                    <div style="font-size:13px; color:#aaa;"><?php echo count($convMessages); ?> message<?php echo count($convMessages)!=1?'s':''; ?></div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <?php
                    $prevDate = '';
                    foreach($convMessages as $msg):
                        $msgDate    = date('d M Y', strtotime($msg['created_at']));
                        $isRight    = $msg['sender_id'] == $openConv['ub'];
                        $senderRole = $msg['sender_role'] === 'approved' ? 'seller' : 'buyer';
                        $initial    = strtoupper(substr($msg['sender_name'] ?? 'U', 0, 1));
                        $isMsgFlagged = !empty($msg['flagged']);
                        $convParam  = $openConv['ua'].'_'.$openConv['ub'].'_'.$openConv['pid'];
                    ?>
                    <?php if($msgDate !== $prevDate): $prevDate = $msgDate; ?>
                        <div class="date-divider"><?php echo $msgDate; ?></div>
                    <?php endif; ?>

                    <div class="msg-row <?php echo $isRight?'right':''; ?>">
                        <div class="msg-avatar avatar-<?php echo $senderRole; ?>"><?php echo $initial; ?></div>
                        <div>
                            <div class="msg-sender-name"><?php echo htmlspecialchars($msg['sender_name'] ?? 'User'); ?></div>
                            <div class="msg-bubble <?php echo $isMsgFlagged?'is-flagged':''; ?>">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                <div class="msg-meta">
                                    <span><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                                    <?php if($isMsgFlagged): ?><span><i class="fa-solid fa-flag" style="font-size:10px;"></i> Flagged</span><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="msg-actions">
                            <?php if(!$isMsgFlagged): ?>
                            <form method="POST" action="messages.php?conv=<?php echo $convParam; ?>">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="flag_message" class="act-btn flag-btn" title="Flag message">
                                    <i class="fa-solid fa-flag"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <form method="POST" action="messages.php?conv=<?php echo $convParam; ?>">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="unflag_message" class="act-btn unflag-btn" title="Unflag">
                                    <i class="fa-solid fa-flag-checkered"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="messages.php?conv=<?php echo $convParam; ?>"
                                  onsubmit="return confirm('Delete this message permanently?');">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="delete_message" class="act-btn del-btn" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($convMessages)): ?>
                        <div style="text-align:center;color:#ccc;padding:30px;">No messages yet.</div>
                    <?php endif; ?>
                </div>

                <div class="stat-strip">
                    <span><strong><?php echo count($convMessages); ?></strong> total</span>
                    <span><strong><?php echo array_sum(array_column($convMessages,'flagged')); ?></strong> flagged</span>
                    <span><strong><?php echo count(array_filter($convMessages,fn($m)=>!$m['is_read'])); ?></strong> unread</span>
                    <span>Started <strong><?php echo !empty($convMessages)?date('d M Y',strtotime($convMessages[0]['created_at'])):'—'; ?></strong></span>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    const chat = document.getElementById('chatMessages');
    if(chat) chat.scrollTop = chat.scrollHeight;
</script>
</body>
</html>