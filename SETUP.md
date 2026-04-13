# Secure Email Setup Guide

## Quick Start (5 minutes)

### 1. Configure Environment Variables

#### Windows - Command Prompt
```cmd
set SMTP_USERNAME=your-email@gmail.com
set SMTP_PASSWORD=your-app-password
set SMTP_FROM_EMAIL=your-email@gmail.com
set SMTP_FROM_NAME=Your Name
```

#### Windows - PowerShell
```powershell
$env:SMTP_USERNAME="your-email@gmail.com"
$env:SMTP_PASSWORD="your-app-password"
$env:SMTP_FROM_EMAIL="your-email@gmail.com"
$env:SMTP_FROM_NAME="Your Name"
```

#### Local Development (Option 2)
1. Copy `.env.example` to `.env`
2. Edit `.env` with your credentials

### 2. For Gmail Users

1. Go to [myaccount.google.com/security](https://myaccount.google.com/security)
2. Enable **2-Step Verification**
3. Create an **App Password**: [https://support.google.com/accounts/answer/185833](https://support.google.com/accounts/answer/185833)
4. Use the 16-character password as `SMTP_PASSWORD`

### 3. Test the Installation

1. Open your browser to: `http://localhost/app_send_mail/`
2. Fill in the form with a test email
3. Click "Submit Message"
4. You should see a success page

---

## File Structure

```
app_send_mail/
├── index.php           ← Form page (START HERE)
├── processa_envio.php  ← Form processor (calls secure version via require)
└── result.php          ← Secure result display page

app_send_mail_secure/
├── processa_envio.php  ← Main secure email processing logic
├── config.php          ← Environment configuration loader
└── bibliotecas/
    └── PHPMailer/      ← Email library

.env.example            ← Copy & rename to .env (development only)
SECURITY.md             ← Detailed security documentation
```

---

## How the Secure Version Works

### Request Flow
```
User Form (index.php)
    ↓
[CSRF Token Generated in Session]
    ↓
Form Submit → processa_envio.php (requires secure version)
    ↓
[CSRF Token Validated]
[Rate Limit Checked]
[Email Validated]
[Input Sanitized]
    ↓
config.php loads credentials from environment
    ↓
PHPMailer sends email
    ↓
[Redirect to result.php with status]
    ↓
result.php displays message safely (HTML escaped)
```

---

## Security Features Active

✓ CSRF Token Protection
✓ Rate Limiting (5 emails/minute)
✓ Email Validation
✓ Input Sanitization
✓ XSS Prevention (HTML escaping)
✓ Secure Headers
✓ Environment-based Configuration
✓ Error Logging (no details to user)
✓ POST-Redirect-GET Pattern
✓ Session Management

---

## Troubleshooting

### "Configuration Error: SMTP credentials not set"
- Make sure environment variables are set
- Check that values are exactly as shown in configuration
- For Windows: Environment variables take effect on new terminal sessions

### "Invalid CSRF token"
- Session may have expired
- Clear browser cookies and try again
- CSRF token is regenerated per session

### "Too many requests"
- You've sent more than 5 emails in one minute
- Wait 60 seconds before trying again

### "Email validation failed"
- Check that you're using a valid email address format
- Example valid: `user@example.com`

### Email not sending
- Check server logs: `error_log` in PHP configuration
- Verify SMTP credentials are correct
- Try with Gmail: enable "Less secure apps" OR use App Password (recommended)
- Check firewall blocking port 587 or 465

---

## Production Checklist

- [ ] Environment variables configured on server
- [ ] HTTPS enabled (required for security headers)
- [ ] PHP session directory writable and secure
- [ ] Error logging enabled, display_errors disabled
- [ ] Database logging set up for important events
- [ ] Regular backups of configuration
- [ ] Rate limiting adequate for your use case
- [ ] SMTP credentials rotated periodically
- [ ] Security headers verified in browser dev tools

---

## For Developers

See [SECURITY.md](SECURITY.md) for detailed security documentation and additional recommendations.
