<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$conn = getDBConnection();
$userId = getCurrentUserId();
$message = '';
$error = '';

// Handle form submission for adding/editing scores
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $subject = trim($_POST['subject'] ?? '');
        $exam_name = trim($_POST['exam_name'] ?? '');
        $score = intval($_POST['score'] ?? 0);
        $max_score = intval($_POST['max_score'] ?? 0);
        $exam_date = $_POST['exam_date'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        
        if (empty($subject) || empty($exam_name) || $max_score <= 0 || empty($exam_date)) {
            $error = 'Please fill in all required fields.';
        } elseif ($score > $max_score) {
            $error = 'Score cannot be greater than maximum score.';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO scores (user_id, subject, exam_name, score, max_score, exam_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$userId, $subject, $exam_name, $score, $max_score, $exam_date, $notes]);
                    $message = 'Score record added successfully!';
                } else {
                    $id = intval($_POST['id'] ?? 0);
                    $stmt = $conn->prepare("UPDATE scores SET subject = ?, exam_name = ?, score = ?, max_score = ?, exam_date = ?, notes = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$subject, $exam_name, $score, $max_score, $exam_date, $notes, $id, $userId]);
                    $message = 'Score record updated successfully!';
                }
            } catch(PDOException $e) {
                error_log("Score action error: " . $e->getMessage());
                $error = 'Failed to save score record.';
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $conn->prepare("DELETE FROM scores WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            $message = 'Score record deleted successfully!';
        } catch(PDOException $e) {
            error_log("Score delete error: " . $e->getMessage());
            $error = 'Failed to delete score record.';
        }
    }
}

// Get all scores for the user
try {
    $stmt = $conn->prepare("SELECT * FROM scores WHERE user_id = ? ORDER BY exam_date DESC, created_at DESC");
    $stmt->execute([$userId]);
    $scores = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Scores fetch error: " . $e->getMessage());
    $scores = [];
}

// Handle edit mode
$editMode = false;
$editScore = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    foreach ($scores as $score) {
        if ($score['id'] == $editId) {
            $editMode = true;
            $editScore = $score;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Records - HKDSE Study Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header>
        <nav>
            <h1>HKDSE Study Tracker</h1>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="scores.php" aria-current="page">Score Records</a></li>
                <li><a href="mistakes.php">Mistake Records</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars(getCurrentUsername()); ?>)</a></li>
            </ul>
            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark Mode</button>
        </nav>
    </header>
    
    <main id="main-content" class="container">
        <h2><?php echo $editMode ? 'Edit' : 'Add'; ?> Score Record</h2>
        
        <?php if ($message): ?>
            <div class="message message-success" role="alert" aria-live="polite">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message message-error" role="alert" aria-live="polite">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST" action="scores.php" id="score-form">
                <input type="hidden" name="action" value="<?php echo $editMode ? 'edit' : 'add'; ?>">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id" value="<?php echo $editScore['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="subject">Subject <span aria-label="required">*</span></label>
                    <select 
                        id="subject" 
                        name="subject" 
                        required
                        aria-required="true"
                        data-current-value="<?php echo htmlspecialchars($editMode ? $editScore['subject'] : ''); ?>"
                    >
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="exam_name">Exam Name <span aria-label="required">*</span></label>
                    <input 
                        type="text" 
                        id="exam_name" 
                        name="exam_name" 
                        required
                        aria-required="true"
                        value="<?php echo htmlspecialchars($editMode ? $editScore['exam_name'] : ''); ?>"
                        placeholder="e.g., Mock Exam 1, Past Paper 2020"
                    >
                </div>
                
                <div class="form-group">
                    <label for="score">Score <span aria-label="required">*</span></label>
                    <input 
                        type="number" 
                        id="score" 
                        name="score" 
                        required
                        min="0"
                        aria-required="true"
                        value="<?php echo $editMode ? $editScore['score'] : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="max_score">Maximum Score <span aria-label="required">*</span></label>
                    <input 
                        type="number" 
                        id="max_score" 
                        name="max_score" 
                        required
                        min="1"
                        aria-required="true"
                        value="<?php echo $editMode ? $editScore['max_score'] : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="exam_date">Exam Date <span aria-label="required">*</span></label>
                    <input 
                        type="date" 
                        id="exam_date" 
                        name="exam_date" 
                        required
                        aria-required="true"
                        value="<?php echo $editMode ? $editScore['exam_date'] : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes (Optional)</label>
                    <textarea 
                        id="notes" 
                        name="notes"
                        placeholder="Add any additional notes about this exam..."
                    ><?php echo htmlspecialchars($editMode ? $editScore['notes'] : ''); ?></textarea>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editMode ? 'Update' : 'Add'; ?> Score Record
                    </button>
                    <?php if ($editMode): ?>
                        <a href="scores.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>Your Score Records</h2>
            <?php if (empty($scores)): ?>
                <p>No score records yet. Add your first score record above!</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Exam Name</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $score): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($score['subject']); ?></td>
                                <td><?php echo htmlspecialchars($score['exam_name']); ?></td>
                                <td><?php echo $score['score']; ?>/<?php echo $score['max_score']; ?></td>
                                <td><?php echo number_format(($score['score'] / $score['max_score']) * 100, 1); ?>%</td>
                                <td><?php echo date('d M Y', strtotime($score['exam_date'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="scores.php?edit=<?php echo $score['id']; ?>" class="btn btn-secondary btn-small">Edit</a>
                                        <form method="POST" action="scores.php" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $score['id']; ?>">
                                            <button 
                                                type="submit" 
                                                class="btn btn-danger btn-small"
                                                onclick="return confirmDelete('Are you sure you want to delete this score record?');"
                                            >Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="script.js"></script>
</body>
</html>
