# Quick Start Guide - HKDSE Study Tracker

## For Local Testing (XAMPP/WAMP/MAMP)

### Step 1: Install XAMPP (Recommended for Windows/Mac/Linux)
Download from: https://www.apachefriends.org/

### Step 2: Copy Files
1. Clone or download this repository
2. Copy all files to XAMPP's `htdocs` folder:
   - Windows: `C:\xampp\htdocs\dse\`
   - Mac: `/Applications/XAMPP/htdocs/dse/`
   - Linux: `/opt/lampp/htdocs/dse/`

### Step 3: Start XAMPP
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** modules
3. Click "Admin" next to MySQL to open phpMyAdmin

### Step 4: Create Database
In phpMyAdmin:
1. Click "New" to create a database
2. Database name: `hkdse_records`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"
5. Click "Import" tab
6. Choose file: `database.sql`
7. Click "Go" to import

### Step 5: Update Database Config (if needed)
Open `config.php` and verify these settings:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hkdse_records');
define('DB_USER', 'root');      // Default XAMPP user
define('DB_PASS', '');          // Default XAMPP password is empty
```

### Step 6: Access the Website
1. Open your web browser
2. Go to: `http://localhost/dse/register.php`
3. Create your first account
4. Login and start tracking!

## File Structure
```
dse/
├── database.sql        # Database schema
├── config.php          # Database configuration
├── auth.php            # Authentication functions
├── login.php           # Login page
├── register.php        # Registration page
├── logout.php          # Logout script
├── index.php           # Dashboard (home page)
├── scores.php          # Score recording page
├── mistakes.php        # Mistake recording page
├── styles.css          # All CSS styles
├── script.js           # JavaScript for dark mode
└── README.md           # Full documentation
```

## Default Features

### Authentication
- Secure password hashing
- Session-based login
- User registration with validation

### Score Recording
- Track exam scores by subject
- Calculate percentages automatically
- Add notes for each exam
- Edit and delete records

### Mistake Recording
- Document mistakes by subject and topic
- Track status (unresolved/reviewing/resolved)
- Filter by status
- Add explanations and correct answers
- Edit and delete records

### UI Features
- **Dark/Light Mode**: Click the moon/sun button in header
- **Responsive Design**: Works on mobile, tablet, and desktop
- **Accessibility**: Keyboard navigation, ARIA labels, semantic HTML

## Troubleshooting

### Database Connection Error
- Make sure MySQL is running in XAMPP
- Check database name in `config.php` matches phpMyAdmin
- Verify database user/password (default is root with empty password)

### Page Not Found
- Make sure files are in the correct htdocs folder
- Check URL: `http://localhost/dse/` (not `http://localhost/`)
- Restart Apache in XAMPP

### Cannot Register/Login
- Make sure database tables were created (import database.sql)
- Check browser console for JavaScript errors
- Ensure PHP session is working (check PHP configuration)

## Production Deployment

For production deployment on a real web server:

1. **Update config.php** with actual database credentials
2. **Use HTTPS** - Never use HTTP for production
3. **Change database password** from default
4. **Set proper file permissions**:
   - Files: 644
   - Directories: 755
   - config.php: 640 (if possible)
5. **Enable PHP error logging** (disable display_errors)
6. **Regular backups** of database

## Security Checklist

✅ Password hashing (PHP password_hash)
✅ SQL injection prevention (PDO prepared statements)
✅ XSS prevention (htmlspecialchars)
✅ Session-based authentication
✅ Input validation
✅ HTTPS recommended for production

## Browser Compatibility

- ✅ Chrome
- ✅ Firefox  
- ✅ Safari
- ✅ Edge
- ✅ Mobile browsers

## Support

For issues or questions:
- Check README.md for full documentation
- Review INSTALL.md (this file)
- Check database.sql for schema details

Good luck with your HKDSE exam preparation! 📚
