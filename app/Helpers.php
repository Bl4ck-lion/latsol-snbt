<?php
namespace app;

function jsonResponse($code, $data) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getDeviceHash() {
    // Prefer X-Forwarded-For to work behind reverse proxies
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ipSubnet = substr($ip, 0, strrpos($ip, '.'));
    $uuid = $_COOKIE['device_uuid'] ?? '';
    if (empty($uuid)) {
        $uuid = bin2hex(random_bytes(16));
        setcookie('device_uuid', $uuid, time() + (86400 * 365), "/"); // 1 year
    }
    return hash('sha256', $userAgent . $ipSubnet . $uuid);
}