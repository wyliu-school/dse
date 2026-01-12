<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$conn = getDBConnection();
$userId = getCurrentUserId();

// Get statistics
try {
    // Total scores
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM scores WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalScores = $stmt->fetch()['total'];
    
    // Average score percentage
    $stmt = $conn->prepare("SELECT AVG((score * 100.0) / max_score) as avg_percentage FROM scores WHERE user_id = ?");
    $stmt->execute([$userId]);
    $avgPercentage = $stmt->fetch()['avg_percentage'] ?? 0;
    
    // Total mistakes
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM mistakes WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalMistakes = $stmt->fetch()['total'];
    
    // Unresolved mistakes
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM mistakes WHERE user_id = ? AND status = 'unresolved'");
    $stmt->execute([$userId]);
    $unresolvedMistakes = $stmt->fetch()['total'];
    
    // Recent scores
    $stmt = $conn->prepare("SELECT * FROM scores WHERE user_id = ? ORDER BY exam_date DESC, created_at DESC LIMIT 5");
    $stmt->execute([$userId]);
    $recentScores = $stmt->fetchAll();
    
    // Recent mistakes
    $stmt = $conn->prepare("SELECT * FROM mistakes WHERE user_id = ? ORDER BY mistake_date DESC, created_at DESC LIMIT 5");
    $stmt->execute([$userId]);
    $recentMistakes = $stmt->fetchAll();
    
} catch(PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $totalScores = 0;
    $avgPercentage = 0;
    $totalMistakes = 0;
    $unresolvedMistakes = 0;
    $recentScores = [];
    $recentMistakes = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HKDSE Study Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header>
        <nav>
            <h1>HKDSE Study Tracker</h1>
            <ul>
                <li><a href="index.php" aria-current="page">Dashboard</a></li>
                <li><a href="scores.php">Score Records</a></li>
                <li><a href="mistakes.php">Mistake Records</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars(getCurrentUsername()); ?>)</a></li>
            </ul>
            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">🌙 Dark Mode</button>
        </nav>
    </header>
    
    <main id="main-content" class="container">
        <h2>Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?>!</h2>
        <p>Track your HKDSE exam scores and mistakes to improve your performance.</p>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <h3>Total Score Records</h3>
                <div class="number"><?php echo $totalScores; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Average Score</h3>
                <div class="number"><?php echo number_format($avgPercentage, 1); ?>%</div>
            </div>
            
            <div class="stat-card">
                <h3>Total Mistakes</h3>
                <div class="number"><?php echo $totalMistakes; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Unresolved Mistakes</h3>
                <div class="number"><?php echo $unresolvedMistakes; ?></div>
            </div>
        </div>
        
        <div class="card">
            <h2>Recent Score Records</h2>
            <?php if (empty($recentScores)): ?>
                <p>No score records yet. <a href="scores.php">Add your first score record</a>.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Exam</th>
                            <th>Score</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentScores as $score): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($score['subject']); ?></td>
                                <td><?php echo htmlspecialchars($score['exam_name']); ?></td>
                                <td><?php echo $score['score']; ?>/<?php echo $score['max_score']; ?> (<?php echo number_format(($score['score'] / $score['max_score']) * 100, 1); ?>%)</td>
                                <td><?php echo date('d M Y', strtotime($score['exam_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="margin-top: 1rem;"><a href="scores.php">View all score records →</a></p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Recent Mistakes</h2>
            <?php if (empty($recentMistakes)): ?>
                <p>No mistake records yet. <a href="mistakes.php">Add your first mistake record</a>.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Topic</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMistakes as $mistake): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mistake['subject']); ?></td>
                                <td><?php echo htmlspecialchars($mistake['topic']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $mistake['status']; ?>">
                                        <?php echo ucfirst($mistake['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($mistake['mistake_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="margin-top: 1rem;"><a href="mistakes.php">View all mistake records →</a></p>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="script.js"></script>
</body>
</html>
