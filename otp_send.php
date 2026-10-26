<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok'=>false,'error'=>'Bad request'], 405);
csrf_check();
$b = build_order($_POST);
if (empty($b['ok'])) json_out(['ok'=>false,'error'=>$b['error']]);
if (OTP_MODE === 'off') json_out(['ok'=>true,'otp'=>false]);

$phone = $b['c']['phone'];
if (time() - ($_SESSION['otp_sent_at'] ?? 0) < 30) json_out(['ok'=>false,'error'=>'Please wait 30 seconds before requesting another OTP.']);
$_SESSION['otp_count'] = ($_SESSION['otp_hour'] ?? 0) > time() - 3600 ? ($_SESSION['otp_count'] ?? 0) + 1 : 1;
if ($_SESSION['otp_count'] === 1) $_SESSION['otp_hour'] = time();
if ($_SESSION['otp_count'] > 5) json_out(['ok'=>false,'error'=>'Too many OTP requests. Try again later.']);

$otp = (string)random_int(100000, 999999);
$_SESSION['otp'] = ['hash'=>password_hash($otp, PASSWORD_DEFAULT), 'phone'=>$phone, 'exp'=>time()+300, 'tries'=>0];
$_SESSION['otp_sent_at'] = time();
unset($_SESSION['otp_ok']);

if (OTP_MODE === 'msg91') {
    if (!send_msg91_otp($phone, $otp)) json_out(['ok'=>false,'error'=>'Could not send OTP. Please try again.']);
} else {
    file_put_contents(__DIR__.'/otp_dev.log', date('c')." $phone $otp\n", FILE_APPEND);   // testing only
}
json_out(['ok'=>true,'otp'=>true,'phone'=>substr($phone,0,2).'XXXXXX'.substr($phone,-2)]);
