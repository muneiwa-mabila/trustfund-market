<?php
/**
 * track_visitor.php
 * Include this at the top of any public page you want to track.
 * Usage: include 'track_visitor.php';
 * (Must be included AFTER db.php so $conn is available)
 */

function trustfund_track_visitor($conn) {

    // --- Page ---
    $page = isset($_SERVER['REQUEST_URI'])
        ? mysqli_real_escape_string($conn, substr($_SERVER['REQUEST_URI'], 0, 255))
        : '/';

    // --- IP ---
    $ip = 'unknown';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $ip = mysqli_real_escape_string($conn, trim($ip));

    // --- Country via free ip-api.com (no key needed) ---
    $country = 'Unknown';
    $cacheKey = 'country_' . md5($ip);
    if (isset($_SESSION[$cacheKey])) {
        $country = $_SESSION[$cacheKey];
    } else {
        $apiUrl = "http://ip-api.com/json/{$ip}?fields=country";
        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        $response = @file_get_contents($apiUrl, false, $ctx);
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['country'])) {
                $country = $data['country'];
            }
        }
        $_SESSION[$cacheKey] = $country;
    }
    $country = mysqli_real_escape_string($conn, substr($country, 0, 100));

    // --- Device type via User-Agent ---
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (preg_match('/tablet|ipad|playbook|silk|(android(?!.*mobile))/i', $ua)) {
        $device = 'Tablet';
    } elseif (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|windows phone/i', $ua)) {
        $device = 'Mobile';
    } else {
        $device = 'Desktop';
    }

    // --- Browser ---
    $browser = 'Other';
    if (str_contains($ua, 'edg')) {
        $browser = 'Edge';
    } elseif (str_contains($ua, 'chrome') && !str_contains($ua, 'chromium')) {
        $browser = 'Chrome';
    } elseif (str_contains($ua, 'firefox')) {
        $browser = 'Firefox';
    } elseif (str_contains($ua, 'safari') && !str_contains($ua, 'chrome')) {
        $browser = 'Safari';
    } elseif (str_contains($ua, 'opera') || str_contains($ua, 'opr')) {
        $browser = 'Opera';
    }
    $browser = mysqli_real_escape_string($conn, $browser);

    // --- Skip admin & bots ---
    if (
        str_contains($page, '/admin/') ||
        str_contains($ua, 'bot') ||
        str_contains($ua, 'crawl') ||
        str_contains($ua, 'spider')
    ) {
        return;
    }

    // --- Insert ---
    mysqli_query($conn,
        "INSERT INTO visitor_logs (page, ip_address, country, device_type, browser)
         VALUES ('$page', '$ip', '$country', '$device', '$browser')"
    );
}

// Auto-run if $conn exists
if (isset($conn)) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    trustfund_track_visitor($conn);
}