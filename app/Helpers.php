<?php
namespace app;

function jsonResponse($code, $data) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getDeviceHash() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ipSubnet = substr($_SERVER['REMOTE_ADDR'] ?? '', 0, strrpos($_SERVER['REMOTE_ADDR'] ?? '', '.'));
    $uuid = $_COOKIE['device_uuid'] ?? '';
    if (empty($uuid)) {
        $uuid = bin2hex(random_bytes(16));
        setcookie('device_uuid', $uuid, time() + (86400 * 365), "/"); // 1 year
    }
    return hash('sha256', $userAgent . $ipSubnet . $uuid);
}