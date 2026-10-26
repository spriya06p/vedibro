SHREE SPARKLE CRACKERS - v2 (enquiry-based crackers store)

SETUP (XAMPP): copy folder to htdocs/crackers -> start Apache+MySQL -> phpMyAdmin -> import schema.sql
(schema.sql DROPS old tables - fresh install only) -> open http://localhost/crackers/
Admin: /admin/login.php   admin / password   -> change it first (Admin > Password).

FLOW: choose qty (live totals) -> Confirm Estimate -> details form (state min-order check)
 -> OTP -> order saved -> one-time confirmation page with PDF download (refresh = gone, order stays saved).

CONFIG (config.php): DB, shop name/phone, ORDER_PREFIX, OTP_MODE.
 OTP_MODE 'dev'   : OTP is written to otp_dev.log (testing only, never use online)
 OTP_MODE 'msg91' : set MSG91_AUTHKEY + MSG91_TEMPLATE_ID (DLT-approved OTP template)
 OTP_MODE 'off'   : no OTP
ADMIN: orders (filter, status, PDF), products (image upload), categories (discount %),
 settings (packing %, state-wise minimum order), CSV import, password.
Prices: you enter MRP; selling price = MRP - category discount %.

BEFORE GOING LIVE: HTTPS, strong admin password, OTP_MODE msg91, DB user (not root),
 uploads/ writable, keep the enquiry-only wording (online sale of crackers is not permitted).
