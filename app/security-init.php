<?php
// ✅ Only configure session if not already active
if (session_status() === PHP_SESSION_NONE) {
 
    session_name("CHANDUSESSION");
 
    ini_set("session.cookie_httponly", 1);
    ini_set("session.cookie_secure", isset($_SERVER["HTTPS"]) ? 1 : 0);
    ini_set("session.cookie_samesite", "Strict");
 
    session_start();
}
 
// ✅ HTTPS Enforcer
if (
    (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') &&
    (!empty($_ENV['FORCE_HTTPS']) && $_ENV['FORCE_HTTPS'] === 'true')
) {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirect", true, 301);
    exit;
}
 
// ✅ Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
 
// ✅ Single-line CSP (Stripe + Turnstile allowed)
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' https://js.stripe.com https://challenges.cloudflare.com; connect-src 'self'");
 