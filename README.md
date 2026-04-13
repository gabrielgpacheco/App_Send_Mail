# 🔒 App Send Mail - Secure Edition

A **production-ready, enterprise-grade secure mail service** built with PHP and PHPMailer. This application provides a robust web interface for sending emails with **comprehensive security protections** and industry best practices.

## ✨ Features

### Security First
- 🛡️ **CSRF Token Protection** - Session-based tokens prevent cross-site request forgery
- 🔑 **Environment Variables** - No hardcoded credentials; all config from environment
- 📧 **Email Header Injection Protection** - Strict validation prevents SMTP header attacks
- 🚫 **XSS Prevention** - All output HTML-escaped with proper sanitization
- ⏱️ **Rate Limiting** - Automatic DoS protection (5 emails/minute per IP)
- 🔐 **Security Headers** - X-Frame-Options, CSP, HSTS, X-XSS-Protection
- ✅ **Input Validation** - Strict email format checking and length limits
- 📝 **Error Logging** - Generic user messages, detailed server-side logs
- 🔄 **POST-Redirect-GET Pattern** - Prevents form resubmission

### Functional Features
- **PHPMailer Integration** - Robust, well-maintained email library
- **Responsive Design** - Modern Bootstrap 5 interface
- **Session Management** - Secure session handling with HttpOnly cookies
- **SMTP Support** - STARTTLS and SMTPS encryption support
- **Separated Architecture** - Public and secure components properly isolated

## 📁 Project Structure

```
App_Send_Mail/
├── app_send_mail/                    # Public-facing web files
│   ├── index.php                     # ✨ Form with CSRF protection & security headers
│   ├── processa_envio.php            # Entry point (requires secure version)
│   ├── result.php                    # Safe result display with output escaping
│   └── logo.png                      # Application logo
├── app_send_mail_secure/             # 🔒 Secure backend (not directly accessible)
│   ├── processa_envio.php            # Core email processing with all security checks
│   ├── config.php                    # Environment-based configuration
│   └── bibliotecas/PHPMailer/        # PHPMailer library
│       ├── Exception.php
│       ├── OAuth.php
│       ├── PHPMailer.php
│       ├── POP3.php
│       └── SMTP.php
├── SECURITY.md                       # 📋 Detailed security documentation
├── SETUP.md                          # ⚡ Quick start guide
├── .env.example                      # 🔑 Environment variables template
├── LICENSE                           # GNU GPL v3 License
└── README.md                         # This file
```

## 🚀 Quick Start

### 1. Prerequisites
- PHP 7.4 or higher
- Web server (Apache, Nginx, IIS, etc.)
- SMTP server access (Gmail recommended)

### 2. Installation

```bash
# Clone the repository
git clone https://github.com/gabrielgpacheco/App_Send_Mail.git
cd App_Send_Mail

# Deploy to web server (XAMPP example)
xcopy app_send_mail C:\xampp\htdocs\app_send_mail\ /E
xcopy app_send_mail_secure C:\xampp\secure_mail\ /E
```

### 3. Configuration

#### Option A: Environment Variables (Recommended - Production)

**Windows Command Prompt:**
```cmd
set SMTP_USERNAME=your-email@gmail.com
set SMTP_PASSWORD=your-app-password
set SMTP_FROM_EMAIL=your-email@gmail.com
set SMTP_FROM_NAME=Your Name
```

**Windows PowerShell:**
```powershell
$env:SMTP_USERNAME="your-email@gmail.com"
$env:SMTP_PASSWORD="your-app-password"
$env:SMTP_FROM_EMAIL="your-email@gmail.com"
$env:SMTP_FROM_NAME="Your Name"
```

#### Option B: .env File (Development)

1. Copy `.env.example` to `.env`
2. Edit `.env` with your credentials
3. Add `.env` to `.gitignore` (never commit!)

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=Your Name
SMTP_ENCRYPTION=tls
```

### 4. Test Installation

Open: `http://localhost/app_send_mail/`

You should see the secure email form. Fill it out and verify you receive the email.

## 📧 SMTP Configuration

### Gmail (Recommended)

1. Enable 2-Factor Authentication: [myaccount.google.com/security](https://myaccount.google.com/security)
2. Create App Password: [https://support.google.com/accounts/answer/185833](https://support.google.com/accounts/answer/185833)
3. Use the generated 16-character password

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=xxxx xxxx xxxx xxxx  # 16-character app password
SMTP_ENCRYPTION=tls
```

### Other Services

| Service | Host | Port | Encryption |
|---------|------|------|------------|
| Gmail | smtp.gmail.com | 587 | STARTTLS |
| Gmail (SSL) | smtp.gmail.com | 465 | SMTPS |
| Outlook | smtp-mail.outlook.com | 587 | STARTTLS |
| Yahoo | smtp.mail.yahoo.com | 465 | SMTPS |
| SendGrid | smtp.sendgrid.net | 587 | STARTTLS |

## 🔒 Security Features Explained

### CSRF Protection
Every form submission requires a valid CSRF token:
- Token generated per session using `bin2hex(random_bytes(32))`
- Stored in `$_SESSION['csrf_token']`
- Verified before processing

### Rate Limiting
Built-in protection against email bombing:
- Maximum 5 emails per minute per IP address
- Counter automatically resets after 60 seconds
- Returns HTTP 429 for excessive requests

### Email Validation
Strict validation prevents header injection attacks:
- Email format verified with `filter_var()`
- Maximum length: 255 characters
- Subject maximum: 255 characters
- Message maximum: 10,000 characters

### Input Sanitization
All user inputs are trimmed and validated:
- No HTML entities in emails (prevents injection)
- Special characters properly handled
- Length limits prevent DoS

### XSS Prevention
All output properly escaped:
```php
<?php echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); ?>
```

### Error Handling
- Detailed errors logged server-side
- Generic messages shown to users
- No sensitive information exposed

### Security Headers
Applied to all pages:
```
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000
Content-Security-Policy: default-src 'self'
```

## 📚 Documentation

- **[SECURITY.md](SECURITY.md)** - Comprehensive security details and recommendations
- **[SETUP.md](SETUP.md)** - Quick setup guide with troubleshooting

## ✅ Production Checklist

- [ ] Environment variables configured on server
- [ ] HTTPS enabled (required for security headers)
- [ ] Error logging enabled, `display_errors` disabled
- [ ] Session cookies secured (`secure`, `httponly`, `samesite`)
- [ ] SMTP credentials never in code
- [ ] Rate limiting tested and adequate
- [ ] Backup system for configurations in place
- [ ] Server logs monitored for suspicious activity
- [ ] SECURITY.md recommendations reviewed

## 🐛 Troubleshooting

**"Configuration Error: SMTP credentials not set"**
- Ensure environment variables are exported
- For Windows: New terminal needed after setting env vars
- Check variable names exactly

**"Invalid CSRF token"**
- Clear browser cookies
- Session may have expired
- Try again

**"Too many requests (429)"**
- Rate limit exceeded (5/minute)
- Wait 60 seconds

**Email not sending**
- Check error logs
- Verify SMTP credentials
- For Gmail: Use App Password, not account password
- Check firewall blocking ports 587/465

See **[SETUP.md](SETUP.md)** for more troubleshooting.

## 🔐 Technology Stack

- **PHP** - 7.4+ for modern security features
- **PHPMailer** - Industry-standard email library
- **Bootstrap 5** - Responsive UI framework
- **SMTP** - Secure email transmission

## 📄 License

GNU General Public License v3.0 - See [LICENSE](LICENSE) for details

## 🤝 Contributing

Security first! Before submitting PRs:
1. Review [SECURITY.md](SECURITY.md)
2. No hardcoded credentials allowed
3. All inputs must be validated/sanitized
4. All outputs must be escaped
5. Add security headers if needed

## ⚠️ Security Notice

**NEVER:**
- Commit `.env` files with real credentials
- Hardcode usernames/passwords in code
- Expose detailed error messages to users
- Disable CSRF protection
- Allow direct access to `app_send_mail_secure`
- Use weak passwords for SMTP

## 📞 Support

For issues and questions:
1. Check [SETUP.md](SETUP.md) troubleshooting section
2. Review [SECURITY.md](SECURITY.md) for security questions
3. Check server error logs
4. Verify environment configuration

---

**Last Updated:** April 2026  
**Version:** 2.0 (Secure Edition)
