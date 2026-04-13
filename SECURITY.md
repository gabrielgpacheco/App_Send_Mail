# Security Improvements Documentation

## Overview
This document details all security improvements made to the App Send Mail plugin.

---

## Critical Fixes Implemented

### 1. **Hardcoded Credentials Removed** ✓
**Vulnerability**: Email and password were hardcoded in the source code.
**Fix**: 
- Created `config.php` that reads from environment variables
- All credentials are now loaded from `getenv()` calls
- Added `.env.example` template for configuration
- Credentials are never stored in the repository

**Implementation**:
```php
$smtp_username = getenv('SMTP_USERNAME');
$smtp_password = getenv('SMTP_PASSWORD');
```

---

### 2. **Email Header Injection Protection** ✓
**Vulnerability**: Direct `$_POST['to']` accepted without validation allowed injecting SMTP headers.
**Fix**:
- Added strict email validation using `filter_var($email, FILTER_VALIDATE_EMAIL)`
- Email input now validated before being passed to PHPMailer
- Input length limits enforced (max 255 chars)

**Implementation**:
```php
if (!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
    return false;
}
```

---

### 3. **Cross-Site Scripting (XSS) Prevention** ✓
**Vulnerability**: Error messages displayed without HTML escaping.
**Fix**:
- All user-controlled output now wrapped in `htmlspecialchars()` with proper flags
- Created separate `result.php` for safe message display
- Used session storage for messages instead of URL parameters

**Implementation**:
```php
<?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
```

---

### 4. **CSRF Protection Added** ✓
**Vulnerability**: No CSRF token validation - attackers could forge requests.
**Fix**:
- Session-based CSRF tokens using `bin2hex(random_bytes(32))`
- Token validated on every form submission
- Token regenerated per session

**Implementation**:
- Generated in `index.php`: `$_SESSION['csrf_token'] = bin2hex(random_bytes(32));`
- Hidden form field: `<input type="hidden" name="csrf_token" value="...">`
- Validated in `processa_envio.php`: `if ($_POST['csrf_token'] !== $_SESSION['csrf_token'])`

---

### 5. **Rate Limiting Implemented** ✓
**Vulnerability**: No protection against email bombing/DoS attacks.
**Fix**:
- Session-based rate limiting: Maximum 5 emails per minute per IP
- Counter resets after 60 seconds
- Returns HTTP 429 (Too Many Requests) when exceeded

**Implementation**:
```php
if ($_SESSION[$rate_limit_key]['count'] >= 5) {
    http_response_code(429);
    die('Too many requests. Please try again later.');
}
```

---

### 6. **Input Sanitization & Validation** ✓
**Vulnerability**: Subject and message not validated.
**Fix**:
- All inputs trimmed with `trim()`
- Length limits enforced: Subject (255 chars), Message (10,000 chars)
- Email validation required
- HTML input validation with max length checks

**Implementation**:
```php
$to = isset($_POST['to']) ? trim($_POST['to']) : '';
if (strlen($this->message) > 10000) {
    return false;
}
```

---

### 7. **Secure Error Handling** ✓
**Vulnerability**: Detailed error messages exposed internal system info.
**Fix**:
- Detailed errors logged server-side with `error_log()`
- Generic messages shown to users
- No sensitive information in error messages

**Implementation**:
```php
error_log('Email sending failed: ' . $mail->ErrorInfo);
// Show to user:
'Não foi possível enviar este e-mail. Por favor tente novamente mais tarde.'
```

---

### 8. **POST-Redirect-GET Pattern** ✓
**Vulnerability**: Form resubmission could send duplicate emails.
**Fix**:
- After successful processing, redirect to `result.php`
- Messages stored in session, not URL
- Prevents accidental form resubmission

**Implementation**:
```php
$_SESSION['success_message'] = 'E-mail enviado com sucesso!';
header('Location: result.php?status=success');
exit;
```

---

### 9. **Security Headers Added** ✓
**Vulnerability**: Missing HTTP security headers.
**Fix**: Added to all pages:
- `X-Content-Type-Options: nosniff` - Prevents MIME sniffing
- `X-Frame-Options: DENY` - Prevents clickjacking
- `X-XSS-Protection: 1; mode=block` - Browser XSS filter
- `Strict-Transport-Security` - Forces HTTPS
- `Content-Security-Policy` - Controls resource loading

**Implementation**:
```php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Content-Security-Policy: default-src \'self\'; ...');
```

---

### 10. **Method Validation** ✓
**Vulnerability**: No validation of HTTP method.
**Fix**: Only POST requests accepted
```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}
```

---

## Configuration Instructions

### Step 1: Set Environment Variables

**Option A - Using System Environment Variables (Recommended for Production)**
```bash
# Windows Command Prompt
set SMTP_USERNAME=your-email@gmail.com
set SMTP_PASSWORD=your-app-password
set SMTP_FROM_EMAIL=your-email@gmail.com
set SMTP_FROM_NAME=Your Name

# Or Windows PowerShell
$env:SMTP_USERNAME="your-email@gmail.com"
```

**Option B - Using .env File (Development)**
1. Copy `.env.example` to `.env`
2. Edit `.env` with your credentials
3. Add `.env` to `.gitignore`
4. Install and use `vlucas/phpdotenv` package

### Step 2: For Gmail Users

1. Enable 2-Factor Authentication on your Google account
2. Create an App Password: https://support.google.com/accounts/answer/185833
3. Use the generated 16-character password as `SMTP_PASSWORD`

---

## Testing Security

### Test CSRF Protection
```bash
# Should fail (no CSRF token)
curl -X POST http://localhost/app_send_mail/processa_envio.php \
  -d "to=test@example.com&subject=test&message=test"
```

### Test Rate Limiting
```bash
# Send 6 requests rapidly - 6th should get 429 error
for i in {1..6}; do
  curl -X POST http://localhost/app_send_mail/processa_envio.php ...
done
```

### Test Email Validation
```bash
# Should fail (invalid email)
curl -X POST ... -d "to=not-an-email&subject=test&message=test"
```

---

## Deployment Checklist

- [ ] Set all required environment variables
- [ ] Add `.env` file to `.gitignore`
- [ ] Enable HTTPS/SSL (required for HSTS header)
- [ ] Configure PHP error logging (not display errors)
- [ ] Set `session.cookie_secure = On` in php.ini
- [ ] Set `session.cookie_httponly = On` in php.ini
- [ ] Set `session.cookie_samesite = Lax` in php.ini
- [ ] Configure server firewall for rate limiting
- [ ] Test all input validation
- [ ] Review server logs for errors

---

## Additional Recommendations

1. **Database Logging**: Log all email attempts for audit trails
2. **IP Whitelisting**: Consider restricting form access to known IPs
3. **reCAPTCHA**: Add Google reCAPTCHA v3 for bot protection
4. **Request Signing**: Implement HMAC signatures for API usage
5. **Encryption**: Encrypt sensitive data at rest and in transit
6. **Monitoring**: Set up alerts for suspicious activity
7. **Backup**: Maintain backups of configuration files

---

## Files Modified

- `app_send_mail/index.php` - Added CSRF tokens, security headers, input validation
- `app_send_mail/result.php` - Created for safe result display
- `app_send_mail_secure/processa_envio.php` - Complete security overhaul
- `app_send_mail_secure/config.php` - Created for environment-based configuration
- `.env.example` - Created template for environment variables

---

## Security References

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [PHPMailer Security](https://github.com/PHPMailer/PHPMailer/wiki)
- [HTTP Headers Security](https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html)
