# HKDSE Study Tracker

A comprehensive web application for tracking HKDSE exam scores and mistakes to help students improve their performance.

## Features

- **User Authentication**: Secure login and registration system
- **Score Recording**: Track exam scores with detailed information (subject, exam name, scores, dates)
- **Mistake Recording**: Document mistakes with questions, correct answers, explanations, and status tracking
- **Dashboard**: View statistics and recent records at a glance
- **Dark/Light Mode**: Toggle between dark and light themes for comfortable viewing
- **Accessibility**: ARIA labels, semantic HTML, keyboard navigation support
- **Responsive Design**: Works on desktop, tablet, and mobile devices

## Technology Stack

- **Backend**: PHP
- **Database**: MySQL with PDO
- **Frontend**: HTML5, CSS3, JavaScript
- **Features**: Session-based authentication, prepared statements for SQL injection prevention

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) with PHP support
- Or use XAMPP/WAMP/MAMP for local development

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/wyliu-school/dse.git
   cd dse
   ```

2. **Setup the database**
   - Create a MySQL database named `hkdse_records`
   - Import the database schema:
     ```bash
     mysql -u root -p hkdse_records < database.sql
     ```
   - Or execute the SQL file through phpMyAdmin

3. **Configure database connection**
   - Open `config.php`
   - Update the database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'hkdse_records');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

4. **Deploy to web server**
   - Copy all files to your web server's document root (e.g., `htdocs` for XAMPP)
   - Ensure PHP has write permissions for session files

5. **Access the application**
   - Open your browser and navigate to: `http://localhost/dse/`
   - Create a new account using the registration page
   - Start tracking your HKDSE exam scores and mistakes!

## Usage

### Registration
1. Click "Register here" on the login page
2. Fill in username, email, and password
3. Click "Register" to create your account

### Login
1. Enter your username and password
2. Click "Login" to access the dashboard

### Adding Score Records
1. Navigate to "Score Records" from the menu
2. Fill in the form with subject, exam name, score, max score, date, and optional notes
3. Click "Add Score Record"

### Adding Mistake Records
1. Navigate to "Mistake Records" from the menu
2. Fill in subject, topic, question, correct answer, your answer, explanation, date, and status
3. Click "Add Mistake Record"
4. Update the status as you review and resolve mistakes

### Dark Mode
- Click the "🌙 Dark Mode" / "☀️ Light Mode" button in the header to toggle themes
- Your preference is saved automatically

## Database Schema

### Users Table
- `id`: Primary key
- `username`: Unique username
- `email`: Unique email address
- `password`: Hashed password
- `created_at`: Registration timestamp

### Scores Table
- `id`: Primary key
- `user_id`: Foreign key to users
- `subject`: Subject name
- `exam_name`: Name of the exam
- `score`: Score achieved
- `max_score`: Maximum possible score
- `exam_date`: Date of the exam
- `notes`: Optional notes
- `created_at`, `updated_at`: Timestamps

### Mistakes Table
- `id`: Primary key
- `user_id`: Foreign key to users
- `subject`: Subject name
- `topic`: Topic of the mistake
- `question_text`: Question description
- `correct_answer`: Correct answer
- `my_answer`: Student's answer (optional)
- `explanation`: Explanation of the correct answer
- `mistake_date`: Date of the mistake
- `status`: unresolved/reviewing/resolved
- `created_at`, `updated_at`: Timestamps

## Security Features

- Password hashing using PHP's `password_hash()`
- Prepared statements to prevent SQL injection
- Session-based authentication
- CSRF protection through session validation
- XSS prevention with `htmlspecialchars()`

## Accessibility Features

- Semantic HTML5 elements
- ARIA labels and roles
- Keyboard navigation support
- Skip to main content link
- High contrast color schemes
- Focus indicators
- Screen reader friendly

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is open source and available for educational purposes.

## Author

Created for HKDSE exam preparation