<?php
/**
 * Secure Configuration File
 * 
 * Store sensitive credentials in environment variables or a .env file
 * NEVER commit real credentials to version control
 */

// Load from environment variables (recommended for production)
$config = [
    'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'smtp_port' => getenv('SMTP_PORT') ?: 587,
    'smtp_username' => getenv('SMTP_USERNAME') ?: '',
    'smtp_password' => getenv('SMTP_PASSWORD') ?: '',
    'smtp_from_email' => getenv('SMTP_FROM_EMAIL') ?: '',
    'smtp_from_name' => getenv('SMTP_FROM_NAME') ?: 'Your Name',
    'smtp_encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls',
];

// Validate that required credentials are set
if (empty($config['smtp_username']) || empty($config['smtp_password']) || empty($config['smtp_from_email'])) {
    die('Configuration Error: SMTP credentials not set. Please set environment variables: SMTP_USERNAME, SMTP_PASSWORD, SMTP_FROM_EMAIL');
}

return $config;
