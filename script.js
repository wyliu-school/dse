// HKDSE Subjects List
const hkdseSubjects = [
    "Chinese Language",
    "English Language",
    "Mathematics Core",
    "Mathematics M1",
    "Mathematics M2",
    "Citizenship and Social Development",
    "Biology",
    "Chemistry",
    "Physics",
    "Chinese History",
    "History",
    "Geography",
    "Economics",
    "Business, Accounting and Financial Studies",
    "Information and Communication Technology",
    "Ethics and Religious Studies",
    "Chinese Literature",
    "Visual Arts"
];

// Dark mode toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check for saved theme preference or default to light mode
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeButton(savedTheme);
    
    // Theme toggle button event listener
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeButton(newTheme);
        });
    }
    
    // Populate subject dropdowns if they exist
    populateSubjectDropdowns();
});

function updateThemeButton(theme) {
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        if (theme === 'dark') {
            themeToggle.innerHTML = '☀️ Light Mode';
            themeToggle.setAttribute('aria-label', 'Switch to light mode');
        } else {
            themeToggle.innerHTML = '🌙 Dark Mode';
            themeToggle.setAttribute('aria-label', 'Switch to dark mode');
        }
    }
}

// Populate subject dropdowns
function populateSubjectDropdowns() {
    const subjectSelects = document.querySelectorAll('select[name="subject"]');
    
    subjectSelects.forEach(select => {
        // Save the current value if editing
        const currentValue = select.getAttribute('data-current-value');
        
        // Clear existing options except the first one (if it's a placeholder)
        while (select.options.length > 0) {
            select.remove(0);
        }
        
        // Add placeholder option
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = '-- Select a subject --';
        placeholderOption.disabled = true;
        placeholderOption.selected = !currentValue;
        select.appendChild(placeholderOption);
        
        // Add all HKDSE subjects
        hkdseSubjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            if (currentValue && currentValue === subject) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    });
}

// Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = 'var(--danger-color)';
        } else {
            field.style.borderColor = 'var(--border-color)';
        }
    });
    
    return isValid;
}

// Confirmation dialog for delete actions
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}
