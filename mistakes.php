<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$conn = getDBConnection();
$userId = getCurrentUserId();
$message = '';
$error = '';

// Handle form submission for adding/editing mistakes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $subject = trim($_POST['subject'] ?? '');
        $topic = trim($_POST['topic'] ?? '');
        $question_text = trim($_POST['question_text'] ?? '');
        $correct_answer = trim($_POST['correct_answer'] ?? '');
        $my_answer = trim($_POST['my_answer'] ?? '');
        $explanation = trim($_POST['explanation'] ?? '');
        $mistake_date = $_POST['mistake_date'] ?? '';
        $status = $_POST['status'] ?? 'unresolved';
        
        if (empty($subject) || empty($topic) || empty($question_text) || empty($correct_answer) || empty($mistake_date)) {
            $error = 'Please fill in all required fields.';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO mistakes (user_id, subject, topic, question_text, correct_answer, my_answer, explanation, mistake_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$userId, $subject, $topic, $question_text, $correct_answer, $my_answer, $explanation, $mistake_date, $status]);
                    $message = 'Mistake record added successfully!';
                } else {
                    $id = intval($_POST['id'] ?? 0);
                    $stmt = $conn->prepare("UPDATE mistakes SET subject = ?, topic = ?, question_text = ?, correct_answer = ?, my_answer = ?, explanation = ?, mistake_date = ?, status = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$subject, $topic, $question_text, $correct_answer, $my_answer, $explanation, $mistake_date, $status, $id, $userId]);
                    $message = 'Mistake record updated successfully!';
                }
            } catch(PDOException $e) {
                error_log("Mistake action error: " . $e->getMessage());
                $error = 'Failed to save mistake record.';
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $conn->prepare("DELETE FROM mistakes WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            $message = 'Mistake record deleted successfully!';
        } catch(PDOException $e) {
            error_log("Mistake delete error: " . $e->getMessage());
            $error = 'Failed to delete mistake record.';
        }
    }
}

// Get all mistakes for the user
$filterStatus = $_GET['status'] ?? 'all';
try {
    if ($filterStatus === 'all') {
        $stmt = $conn->prepare("SELECT * FROM mistakes WHERE user_id = ? ORDER BY mistake_date DESC, created_at DESC");
        $stmt->execute([$userId]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM mistakes WHERE user_id = ? AND status = ? ORDER BY mistake_date DESC, created_at DESC");
        $stmt->execute([$userId, $filterStatus]);
    }
    $mistakes = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Mistakes fetch error: " . $e->getMessage());
    $mistakes = [];
}

// Handle edit mode
$editMode = false;
$editMistake = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    foreach ($mistakes as $mistake) {
        if ($mistake['id'] == $editId) {
            $editMode = true;
            $editMistake = $mistake;
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
    <title>Mistake Records - HKDSE Study Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header>
        <nav>
            <h1>HKDSE Study Tracker</h1>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="scores.php">Score Records</a></li>
                <li><a href="mistakes.php" aria-current="page">Mistake Records</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars(getCurrentUsername()); ?>)</a></li>
            </ul>
            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark Mode</button>
        </nav>
    </header>
    
    <main id="main-content" class="container">
        <h2><?php echo $editMode ? 'Edit' : 'Add'; ?> Mistake Record</h2>
        
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
            <form method="POST" action="mistakes.php" id="mistake-form">
                <input type="hidden" name="action" value="<?php echo $editMode ? 'edit' : 'add'; ?>">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id" value="<?php echo $editMistake['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="subject">Subject <span aria-label="required">*</span></label>
                    <select 
                        id="subject" 
                        name="subject" 
                        required
                        aria-required="true"
                        data-current-value="<?php echo htmlspecialchars($editMode ? $editMistake['subject'] : ''); ?>"
                    >
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="topic">Topic <span aria-label="required">*</span></label>
                    <input 
                        type="text" 
                        id="topic" 
                        name="topic" 
                        required
                        aria-required="true"
                        value="<?php echo htmlspecialchars($editMode ? $editMistake['topic'] : ''); ?>"
                        placeholder="e.g., Quadratic Equations, Grammar, Newton's Laws"
                    >
                </div>
                
                <div class="form-group">
                    <label for="question_text">Question <span aria-label="required">*</span></label>
                    <textarea 
                        id="question_text" 
                        name="question_text"
                        required
                        aria-required="true"
                        placeholder="Describe the question or problem..."
                    ><?php echo htmlspecialchars($editMode ? $editMistake['question_text'] : ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="my_answer">My Answer (Optional)</label>
                    <textarea 
                        id="my_answer" 
                        name="my_answer"
                        placeholder="What answer did you give?"
                    ><?php echo htmlspecialchars($editMode ? $editMistake['my_answer'] : ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="correct_answer">Correct Answer <span aria-label="required">*</span></label>
                    <textarea 
                        id="correct_answer" 
                        name="correct_answer"
                        required
                        aria-required="true"
                        placeholder="What is the correct answer?"
                    ><?php echo htmlspecialchars($editMode ? $editMistake['correct_answer'] : ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="explanation">Explanation (Optional)</label>
                    <textarea 
                        id="explanation" 
                        name="explanation"
                        placeholder="Explain why the correct answer is right..."
                    ><?php echo htmlspecialchars($editMode ? $editMistake['explanation'] : ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="mistake_date">Date <span aria-label="required">*</span></label>
                    <input 
                        type="date" 
                        id="mistake_date" 
                        name="mistake_date" 
                        required
                        aria-required="true"
                        value="<?php echo $editMode ? $editMistake['mistake_date'] : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="status">Status <span aria-label="required">*</span></label>
                    <select 
                        id="status" 
                        name="status"
                        required
                        aria-required="true"
                    >
                        <option value="unresolved" <?php echo ($editMode && $editMistake['status'] === 'unresolved') ? 'selected' : ''; ?>>Unresolved</option>
                        <option value="reviewing" <?php echo ($editMode && $editMistake['status'] === 'reviewing') ? 'selected' : ''; ?>>Reviewing</option>
                        <option value="resolved" <?php echo ($editMode && $editMistake['status'] === 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editMode ? 'Update' : 'Add'; ?> Mistake Record
                    </button>
                    <?php if ($editMode): ?>
                        <a href="mistakes.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>Your Mistake Records</h2>
            
            <div style="margin-bottom: 1rem;">
                <label for="filter-status" style="display: inline; margin-right: 0.5rem;">Filter by status:</label>
                <select id="filter-status" onchange="window.location.href='mistakes.php?status=' + this.value;" style="width: auto; display: inline-block;">
                    <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="unresolved" <?php echo $filterStatus === 'unresolved' ? 'selected' : ''; ?>>Unresolved</option>
                    <option value="reviewing" <?php echo $filterStatus === 'reviewing' ? 'selected' : ''; ?>>Reviewing</option>
                    <option value="resolved" <?php echo $filterStatus === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                </select>
            </div>
            
            <?php if (empty($mistakes)): ?>
                <p>No mistake records found. Add your first mistake record above!</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Topic</th>
                            <th>Question</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mistakes as $mistake): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mistake['subject']); ?></td>
                                <td><?php echo htmlspecialchars($mistake['topic']); ?></td>
                                <td><?php echo htmlspecialchars(substr($mistake['question_text'], 0, 100)) . (strlen($mistake['question_text']) > 100 ? '...' : ''); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $mistake['status']; ?>">
                                        <?php echo ucfirst($mistake['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($mistake['mistake_date'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="mistakes.php?edit=<?php echo $mistake['id']; ?>" class="btn btn-secondary btn-small">Edit</a>
                                        <form method="POST" action="mistakes.php" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $mistake['id']; ?>">
                                            <button 
                                                type="submit" 
                                                class="btn btn-danger btn-small"
                                                onclick="return confirmDelete('Are you sure you want to delete this mistake record?');"
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
