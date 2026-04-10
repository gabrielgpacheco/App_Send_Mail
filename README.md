# App Send Mail

A secure, private mail service built with PHP and PHPMailer. This application provides a simple web interface for sending emails while keeping sensitive configuration data separate from public-facing files.

## Features

- **Secure Architecture**: Separation of public and private components for enhanced security
- **PHPMailer Integration**: Robust email sending using the popular PHPMailer library
- **Responsive Design**: Clean Bootstrap-based interface
- **Validation**: Basic input validation for required fields
- **Error Handling**: Comprehensive error messages and success notifications
- **SMTP Support**: Secure email transmission via SMTP with TLS encryption

## Project Structure

```
App_Send_Mail/
├── app_send_mail/                    # Public-facing web files
│   ├── index.php                     # Main web interface
│   ├── processa_envio.php            # Public processing script
│   └── logo.png                      # Application logo
├── app_send_mail_secure/             # Secure backend files
│   ├── processa_envio.php            # Secure email processing logic
│   └── bibliotecas/PHPMailer/        # PHPMailer library
│       ├── Exception.php
│       ├── OAuth.php
│       ├── PHPMailer.php
│       ├── POP3.php
│       └── SMTP.php
├── LICENSE                           # GNU GPL v3 License
└── README.md                         # This file
```

## Installation

### Prerequisites
- PHP 7.0 or higher
- Web server (Apache, Nginx, etc.)
- SMTP server credentials (Gmail, Outlook, or custom SMTP)

### Step-by-Step Installation

1. **Download or Clone the Repository**
   ```bash
   git clone https://github.com/gabrielgpacheco/App_Send_Mail.git
   ```

2. **Deploy Public Files**
   - Copy the `app_send_mail` folder to your web server's document root
   - Example: `/var/www/html/app_send_mail/` or `C:\xampp\htdocs\app_send_mail\`

3. **Deploy Secure Files**
   - Copy the `app_send_mail_secure` folder **outside** your web server's document root
   - Example: `/var/secure/app_send_mail_secure/` or `C:\xampp\secure\app_send_mail_secure\`
   - This separation prevents direct web access to sensitive configuration files

4. **Update File Paths**
   - Edit `app_send_mail/processa_envio.php` to point to the correct location of your secure folder:
   ```php
   require "/path/to/app_send_mail_secure/bibliotecas/PHPMailer/Exception.php";
   // ... other require statements
   require "/path/to/app_send_mail_secure/processa_envio.php";
   ```

## Configuration

### SMTP Configuration

Edit `app_send_mail_secure/processa_envio.php` (around line 52) with your SMTP server details:

```php
//Server settings
$mail->SMTPDebug = false;                      // Set to 2 for debugging
$mail->isSMTP();                               // Send using SMTP
$mail->Host       = 'smtp.gmail.com';          // SMTP server
$mail->SMTPAuth   = true;                      // Enable SMTP authentication
$mail->Username   = 'youremail@gmail.com';     // SMTP username
$mail->Password   = 'yourpassword';            // SMTP password or app password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption
$mail->Port       = 587;                       // TCP port

//Recipients
$mail->setFrom('youremail@gmail.com', 'Your Name');
```

### Common SMTP Server Settings

| Service | Host | Port | Encryption |
|---------|------|------|------------|
| Gmail | smtp.gmail.com | 587 | STARTTLS |
| Gmail | smtp.gmail.com | 465 | SSL |
| Outlook | smtp-mail.outlook.com | 587 | STARTTLS |
| Yahoo | smtp.mail.yahoo.com | 465 | SSL |
| Custom SMTP | your.smtp.server.com | 587/465 | STARTTLS/SSL |

### Gmail Specific Configuration

For Gmail, you may need to:
1. Enable "Less secure app access" (not recommended) OR
2. Use App Passwords (recommended):
   - Go to Google Account → Security → 2-Step Verification → App passwords
   - Generate an app password and use it instead of your regular password

## Usage

1. **Access the Application**
   - Navigate to `http://yourdomain.com/app_send_mail/`

2. **Send an Email**
   - Fill in the recipient's email address
   - Enter a subject line
   - Compose your message
   - Click "Submit Message"

3. **Response Handling**
   - Success: Green success message with confirmation
   - Error: Red error message with details (when debug is enabled)

## Security Considerations

### 1. **File Separation**
   - The `app_send_mail_secure` folder should be placed outside the web root
   - This prevents direct HTTP access to configuration files containing credentials

### 2. **SMTP Credentials**
   - Never commit actual credentials to version control
   - Consider using environment variables for production deployment
   - Use app-specific passwords instead of main account passwords

### 3. **Input Validation**
   - The application includes basic validation for required fields
   - Consider adding:
     - Email format validation
     - Input sanitization
     - Rate limiting to prevent abuse

### 4. **HTTPS**
   - Always deploy behind HTTPS to protect credentials in transit
   - Configure your web server with SSL/TLS certificates

### 5. **File Permissions**
   - Set appropriate file permissions:
   ```bash
   chmod 644 *.php          # Readable by web server, writable by owner
   chmod 755 app_send_mail/ # Executable directory
   ```

## Customization

### Changing the Interface
Edit `app_send_mail/index.php` to modify:
- Logo: Replace `logo.png` with your own logo
- Styling: Modify Bootstrap classes or add custom CSS
- Form fields: Add additional fields as needed

### Extending Functionality
Edit `app_send_mail_secure/processa_envio.php` to add:
- CC/BCC recipients
- File attachments
- HTML templates
- Multiple recipient support
- Email queue system

## Troubleshooting

### Common Issues

1. **"Could not connect to SMTP host"**
   - Verify SMTP server settings (host, port)
   - Check firewall settings
   - Ensure SMTP service is running

2. **Authentication Failed**
   - Verify username and password
   - For Gmail: Enable app passwords or less secure apps
   - Check for special characters in passwords

3. **Email Not Sending**
   - Enable debug mode: `$mail->SMTPDebug = 2;`
   - Check PHP error logs
   - Verify PHP mail() function is configured

4. **File Not Found Errors**
   - Verify paths in `app_send_mail/processa_envio.php`
   - Check file permissions

### Debug Mode
Enable detailed debugging in `app_send_mail_secure/processa_envio.php`:
```php
$mail->SMTPDebug = 2; // 0 = off, 1 = client messages, 2 = client and server messages
```

## Development

### Dependencies
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) - Email sending library
- [Bootstrap 5](https://getbootstrap.com/) - Frontend framework (CDN)

### Updating PHPMailer
To update the PHPMailer library:
1. Download the latest version from [PHPMailer GitHub](https://github.com/PHPMailer/PHPMailer)
2. Replace the contents of `app_send_mail_secure/bibliotecas/PHPMailer/`
3. Test the application thoroughly

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## Support

For issues and questions:
1. Check the [Troubleshooting](#troubleshooting) section
2. Review PHPMailer documentation
3. Create an issue on GitHub

## Acknowledgments

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) for the robust email library
- [Bootstrap](https://getbootstrap.com/) for the responsive design framework
- The open-source community for inspiration and support

---

**Note**: Always test email functionality in a development environment before deploying to production. Ensure you comply with your email provider's sending limits and policies.
