<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if(!in_array($_SESSION['role'], ['super_admin', 'mini_admin', 'admin'])){
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$isSuperAdmin = $_SESSION['role'] === 'super_admin';

// --- Date range filter ---
$range = isset($_GET['range']) ? $_GET['range'] : '7';
switch($range){
    case '1':   $dateFilter = "visited_at >= NOW() - INTERVAL 1 DAY";   $rangeLabel = 'Last 24 Hours'; break;
    case '30':  $dateFilter = "visited_at >= NOW() - INTERVAL 30 DAY";  $rangeLabel = 'Last 30 Days';  break;
    case '90':  $dateFilter = "visited_at >= NOW() - INTERVAL 90 DAY";  $rangeLabel = 'Last 90 Days';  break;
    case 'all': $dateFilter = "1=1";                                      $rangeLabel = 'All Time';      break;
    default:    $dateFilter = "visited_at >= NOW() - INTERVAL 7 DAY";   $rangeLabel = 'Last 7 Days';   break;
}

// --- Summary stats ---
$totalViews    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM visitor_logs WHERE $dateFilter"))[0];
$uniqueVisitors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT ip_address) FROM visitor_logs WHERE $dateFilter"))[0];
$todayViews    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM visitor_logs WHERE DATE(visited_at)=CURDATE()"))[0];
$mobileCount   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM visitor_logs WHERE device_type='Mobile' AND $dateFilter"))[0];

// --- Daily views chart data (last 14 days always for chart) ---
$chartResult = mysqli_query($conn,
    "SELECT DATE(visited_at) as day, COUNT(*) as views
     FROM visitor_logs
     WHERE visited_at >= NOW() - INTERVAL 14 DAY
     GROUP BY DATE(visited_at)
     ORDER BY day ASC"
);
$chartLabels = [];
$chartData   = [];
while($row = mysqli_fetch_assoc($chartResult)){
    $chartLabels[] = date('d M', strtotime($row['day']));
    $chartData[]   = (int)$row['views'];
}

// --- Top pages ---
$topPages = mysqli_query($conn,
    "SELECT page, COUNT(*) as views
     FROM visitor_logs
     WHERE $dateFilter
     GROUP BY page
     ORDER BY views DESC
     LIMIT 10"
);

// --- Countries ---
$countries = mysqli_query($conn,
    "SELECT country, COUNT(*) as visits
     FROM visitor_logs
     WHERE $dateFilter
     GROUP BY country
     ORDER BY visits DESC
     LIMIT 10"
);

// --- Device breakdown ---
$devices = mysqli_query($conn,
    "SELECT device_type, COUNT(*) as count
     FROM visitor_logs
     WHERE $dateFilter
     GROUP BY device_type"
);
$deviceData = ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0];
while($d = mysqli_fetch_assoc($devices)){
    $deviceData[$d['device_type']] = (int)$d['count'];
}
$deviceTotal = max(array_sum($deviceData), 1);

// --- Browser breakdown ---
$browsers = mysqli_query($conn,
    "SELECT browser, COUNT(*) as count
     FROM visitor_logs
     WHERE $dateFilter
     GROUP BY browser
     ORDER BY count DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Visitor Statistics | TrustFund Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ background:#f4f4f4; font-family:Arial,sans-serif; }

.admin-sidebar{
    width:270px; min-height:100vh;
    background:linear-gradient(180deg,#9b59b6 0%,#7d3fa0 100%);
    position:fixed; left:0; top:0; padding:28px 20px; color:white;
    border-top-right-radius:30px; border-bottom-right-radius:30px;
    box-shadow:0 10px 35px rgba(155,89,182,0.25);
    display:flex; flex-direction:column; justify-content:space-between;
}
.logo{ font-size:32px; font-weight:900; color:white; margin-bottom:40px; padding-left:10px; }
.sidebar-menu{ display:flex; flex-direction:column; gap:10px; }
.admin-sidebar a{
    display:flex; align-items:center; gap:14px; color:white; text-decoration:none;
    padding:15px 18px; border-radius:18px; transition:0.25s; font-size:15px; font-weight:600;
}
.admin-sidebar a i{ width:22px; text-align:center; }
.admin-sidebar a:hover{ background:rgba(255,255,255,0.16); transform:translateX(4px); }
.admin-sidebar a.active{ background:white; color:#9b59b6; box-shadow:0 10px 25px rgba(0,0,0,0.08); }
.sidebar-bottom{ margin-top:40px; }

.admin-main{ margin-left:290px; padding:35px; }
.topbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; flex-wrap:wrap; gap:16px; }
.page-title{ font-size:38px; font-weight:800; color:#111; }
.page-subtitle{ color:#777; margin-top:5px; }

.range-tabs{ display:flex; gap:8px; }
.range-tab{
    padding:9px 18px; border-radius:12px; font-size:13px; font-weight:700;
    text-decoration:none; color:#777; background:white;
    border:1px solid #e5e5e5; transition:0.2s;
}
.range-tab.active{ background:#9b59b6; color:white; border-color:#9b59b6; }
.range-tab:hover:not(.active){ background:#f3e8fb; color:#9b59b6; }

.stats-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:22px; margin-bottom:28px; }
.stat-card{
    background:white; border-radius:22px; padding:26px;
    box-shadow:0 8px 24px rgba(0,0,0,0.05); border:1px solid #eee; transition:0.2s;
}
.stat-card:hover{ transform:translateY(-4px); }
.card-icon{
    width:50px; height:50px; border-radius:14px; background:#f3e8fb;
    color:#9b59b6; display:flex; align-items:center; justify-content:center;
    font-size:20px; margin-bottom:14px;
}
.stat-card h2{ font-size:30px; font-weight:800; margin-bottom:6px; color:#111; }
.stat-card p{ color:#777; margin:0; font-size:14px; }

.grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:22px; margin-bottom:22px; }
.grid-3{ display:grid; grid-template-columns:2fr 1fr 1fr; gap:22px; margin-bottom:22px; }

.panel{ background:white; border-radius:22px; padding:26px; box-shadow:0 8px 24px rgba(0,0,0,0.05); }
.panel-title{ font-size:18px; font-weight:800; margin-bottom:20px; color:#111; }

.chart-wrap{ position:relative; height:240px; }

/* Progress bars */
.prog-item{ margin-bottom:14px; }
.prog-label{ display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px; color:#333; font-weight:600; }
.prog-bar-bg{ height:8px; background:#f0f0f0; border-radius:20px; overflow:hidden; }
.prog-bar-fill{ height:100%; border-radius:20px; background:linear-gradient(90deg,#9b59b6,#c39bd3); transition:width 0.6s ease; }

/* Top pages table */
table{ width:100%; border-collapse:collapse; font-size:13px; }
thead th{ background:#f8f2fd; color:#9b59b6; padding:11px 12px; text-align:left; font-weight:700; border-radius:6px; }
tbody tr{ border-bottom:1px solid #f5f5f5; }
tbody tr:hover{ background:#fdf7ff; }
tbody td{ padding:11px 12px; color:#444; }
.page-url{ max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Device icons */
.device-row{ display:flex; align-items:center; gap:14px; margin-bottom:18px; }
.device-icon{ width:42px; height:42px; border-radius:12px; background:#f3e8fb; color:#9b59b6; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.device-info{ flex:1; }
.device-name{ font-weight:700; font-size:14px; color:#111; }
.device-pct{ font-size:13px; color:#999; }

.no-data{ text-align:center; padding:40px; color:#bbb; font-size:15px; }

@media(max-width:1200px){ .grid-3{ grid-template-columns:1fr 1fr; } }
@media(max-width:1000px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } .grid-2{ grid-template-columns:1fr; } }
@media(max-width:700px){
    .admin-sidebar{ position:relative; width:100%; min-height:auto; border-radius:0; }
    .admin-main{ margin-left:0; padding:20px; }
    .stats-grid{ grid-template-columns:1fr; }
    .grid-3{ grid-template-columns:1fr; }
    .topbar{ flex-direction:column; align-items:flex-start; }
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
            <a href="Messages-admin.php"><i class="fa-solid fa-comments"></i> Messages</a>
            <a href="transactions.php"><i class="fa-solid fa-receipt"></i> Transactions</a>
            <a href="visitors.php" class="active"><i class="fa-solid fa-chart-bar"></i> Visitor Stats</a>
            <?php if($isSuperAdmin): ?>
            <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
            <a href="manage_admins.php"><i class="fa-solid fa-user-shield"></i> Admin Panel</a>
            <?php endif; ?>
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
            <div class="page-title">Visitor Statistics</div>
            <div class="page-subtitle">Tracking: <?php echo $rangeLabel; ?></div>
        </div>
        <div class="range-tabs">
            <a href="?range=1"   class="range-tab <?php echo $range=='1'  ?'active':''; ?>">24h</a>
            <a href="?range=7"   class="range-tab <?php echo $range=='7'  ?'active':''; ?>">7 Days</a>
            <a href="?range=30"  class="range-tab <?php echo $range=='30' ?'active':''; ?>">30 Days</a>
            <a href="?range=90"  class="range-tab <?php echo $range=='90' ?'active':''; ?>">90 Days</a>
            <a href="?range=all" class="range-tab <?php echo $range=='all'?'active':''; ?>">All Time</a>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-eye"></i></div>
            <h2><?php echo number_format($totalViews); ?></h2>
            <p>Total Page Views</p>
        </div>
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-user"></i></div>
            <h2><?php echo number_format($uniqueVisitors); ?></h2>
            <p>Unique Visitors</p>
        </div>
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-calendar-day"></i></div>
            <h2><?php echo number_format($todayViews); ?></h2>
            <p>Views Today</p>
        </div>
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-mobile-screen"></i></div>
            <h2><?php echo $totalViews > 0 ? round($mobileCount / $totalViews * 100) : 0; ?>%</h2>
            <p>Mobile Traffic</p>
        </div>
    </div>

    <!-- CHART + DEVICES -->
    <div class="grid-3">

        <!-- Daily views chart -->
        <div class="panel" style="grid-column: span 2;">
            <div class="panel-title">Page Views — Last 14 Days</div>
            <?php if(empty($chartData)): ?>
                <div class="no-data"><i class="fa-solid fa-chart-line" style="font-size:36px;display:block;margin-bottom:10px;"></i>No data yet.</div>
            <?php else: ?>
            <div class="chart-wrap">
                <canvas id="viewsChart"></canvas>
            </div>
            <?php endif; ?>
        </div>

        <!-- Device breakdown -->
        <div class="panel">
            <div class="panel-title">Devices</div>
            <?php
            $deviceIcons = ['Desktop'=>'fa-desktop','Mobile'=>'fa-mobile-screen','Tablet'=>'fa-tablet-screen-button'];
            foreach($deviceData as $dtype => $dcount):
                $pct = round($dcount / $deviceTotal * 100);
            ?>
            <div class="device-row">
                <div class="device-icon"><i class="fa-solid <?php echo $deviceIcons[$dtype]; ?>"></i></div>
                <div class="device-info">
                    <div class="device-name"><?php echo $dtype; ?></div>
                    <div class="device-pct"><?php echo number_format($dcount); ?> visits &bull; <?php echo $pct; ?>%</div>
                    <div class="prog-bar-bg" style="margin-top:6px;">
                        <div class="prog-bar-fill" style="width:<?php echo $pct; ?>%;"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- COUNTRIES + BROWSERS -->
    <div class="grid-2">

        <!-- Countries -->
        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-earth-africa" style="color:#9b59b6;margin-right:8px;"></i>Top Countries</div>
            <?php
            $countryRows = [];
            while($c = mysqli_fetch_assoc($countries)) $countryRows[] = $c;
            $maxCountry = $countryRows[0]['visits'] ?? 1;
            if(empty($countryRows)):
            ?>
                <div class="no-data">No data yet.</div>
            <?php else: ?>
                <?php foreach($countryRows as $c):
                    $pct = round($c['visits'] / $maxCountry * 100);
                ?>
                <div class="prog-item">
                    <div class="prog-label">
                        <span><?php echo htmlspecialchars($c['country']); ?></span>
                        <span><?php echo number_format($c['visits']); ?></span>
                    </div>
                    <div class="prog-bar-bg">
                        <div class="prog-bar-fill" style="width:<?php echo $pct; ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Browsers -->
        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-globe" style="color:#9b59b6;margin-right:8px;"></i>Browsers</div>
            <?php
            $browserRows = [];
            while($b = mysqli_fetch_assoc($browsers)) $browserRows[] = $b;
            $maxBrowser = $browserRows[0]['count'] ?? 1;
            $browserIcons = ['Chrome'=>'fa-brands fa-chrome','Firefox'=>'fa-brands fa-firefox-browser','Safari'=>'fa-brands fa-safari','Edge'=>'fa-brands fa-edge','Opera'=>'fa-brands fa-opera','Other'=>'fa-solid fa-globe'];
            if(empty($browserRows)):
            ?>
                <div class="no-data">No data yet.</div>
            <?php else: ?>
                <?php foreach($browserRows as $b):
                    $pct = round($b['count'] / $maxBrowser * 100);
                    $icon = $browserIcons[$b['browser']] ?? 'fa-solid fa-globe';
                ?>
                <div class="prog-item">
                    <div class="prog-label">
                        <span><i class="<?php echo $icon; ?>" style="margin-right:6px;color:#9b59b6;"></i><?php echo htmlspecialchars($b['browser']); ?></span>
                        <span><?php echo number_format($b['count']); ?></span>
                    </div>
                    <div class="prog-bar-bg">
                        <div class="prog-bar-fill" style="width:<?php echo $pct; ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- TOP PAGES -->
    <div class="panel">
        <div class="panel-title"><i class="fa-solid fa-file-lines" style="color:#9b59b6;margin-right:8px;"></i>Top Pages</div>
        <?php
        $pageRows = [];
        while($p = mysqli_fetch_assoc($topPages)) $pageRows[] = $p;
        $maxPageViews = $pageRows[0]['views'] ?? 1;
        if(empty($pageRows)):
        ?>
            <div class="no-data"><i class="fa-solid fa-inbox" style="font-size:36px;display:block;margin-bottom:10px;"></i>No page data yet.</div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Page URL</th>
                    <th>Views</th>
                    <th style="width:200px;">Share</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($pageRows as $i => $p):
                $pct = round($p['views'] / $maxPageViews * 100);
            ?>
                <tr>
                    <td style="color:#bbb;font-weight:700;"><?php echo $i+1; ?></td>
                    <td class="page-url"><?php echo htmlspecialchars($p['page']); ?></td>
                    <td><strong><?php echo number_format($p['views']); ?></strong></td>
                    <td>
                        <div class="prog-bar-bg">
                            <div class="prog-bar-fill" style="width:<?php echo $pct; ?>%;"></div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
<?php if(!empty($chartData)): ?>
const ctx = document.getElementById('viewsChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Page Views',
            data: <?php echo json_encode($chartData); ?>,
            borderColor: '#9b59b6',
            backgroundColor: 'rgba(155,89,182,0.08)',
            borderWidth: 3,
            pointBackgroundColor: '#9b59b6',
            pointRadius: 5,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 12 } } },
            y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { font: { size: 12 } } }
        }
    }
});
<?php endif; ?>
</script>

</body>
</html>