<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok'=>false], 405);
csrf_check();
$o = $_SESSION['otp'] ?? null;
if (!$o || $o['exp'] < time()) json_out(['ok'=>false,'error'=>'OTP expired. Please resend.']);
if ($o['tries'] >= 5) json_out(['ok'=>false,'error'=>'Too many wrong attempts. Please resend OTP.']);
$_SESSION['otp']['tries']++;
if (!password_verify(trim($_POST['otp'] ?? ''), $o['hash'])) json_out(['ok'=>false,'error'=>'Wrong OTP.']);
$_SESSION['otp_ok'] = $o['phone'];
unset($_SESSION['otp']);
json_out(['ok'=>true]);
