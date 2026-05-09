<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    redirect('../login.php');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $notes = trim($_POST['admin_notes'] ?? '');
    
    Database::update('demo_requests', [
        'status' => $status,
        'admin_notes' => $notes
    ], 'id = :id', ['id' => $id]);
}

// Get all requests
$requests = Database::fetchAll(
    "SELECT * FROM demo_requests ORDER BY created_at DESC"
);

$pageTitle = 'Demo Requests';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: var(--bg-secondary); border-right: 1px solid var(--line-subtle); padding: 2rem; position: fixed; height: 100vh; }
        .admin-sidebar .logo { font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; }
        .admin-nav { list-style: none; }
        .admin-nav li { margin-bottom: 0.5rem; }
        .admin-nav a { display: block; padding: 0.75rem 1rem; color: var(--text-secondary); border-radius: var(--radius-md); }
        .admin-nav a:hover, .admin-nav a.active { background: var(--bg-tertiary); color: var(--text-primary); }
        .admin-main { flex: 1; margin-left: 260px; padding: 2rem; }
        .admin-header { margin-bottom: 2rem; }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border: 1px solid var(--line-subtle); border-radius: var(--radius-md); font-size: 0.9375rem; }
        .admin-table th, .admin-table td { text-align: left; padding: 1rem; border-bottom: 1px solid var(--line-subtle); }
        .admin-table th { color: var(--text-muted); font-weight: 500; font-size: 0.8125rem; text-transform: uppercase; }
        .status-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600; }
        .status-new { background: var(--accent-gold); color: var(--bg-primary); }
        .status-scheduled { background: #3498db; color: #fff; }
        .status-completed { background: #27ae60; color: #fff; }
        .status-no_show { background: #e74c3c; color: #fff; }
        .status-closed { background: var(--text-muted); color: #fff; }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-card); border: 1px solid var(--line-subtle); border-radius: var(--radius-lg); padding: 2rem; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .modal-close { background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; }
        .detail-row { margin-bottom: 1rem; }
        .detail-label { color: var(--text-muted); font-size: 0.8125rem; text-transform: uppercase; margin-bottom: 0.25rem; }
        .detail-value { font-size: 1rem; }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="logo">Kaptain One</div>
            <nav>
                <ul class="admin-nav">
                    <li><a href="../dashboard.php">Dashboard</a></li>
                    <li><a href="../blog/">Blog Posts</a></li>
                    <li><a href="partners.php">Partner Inquiries</a></li>
                    <li><a href="demos.php" class="active">Demo Requests</a></li>
                    <li><a href="../dashboard.php?logout=1">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Demo Requests</h1>
            </div>
            
            <?php if (empty($requests)): ?>
                
                <p style="color: var(--text-muted);">No demo requests yet.</p>
            <?php else: ?>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Interest</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            
                            <tr>
                                <td><?= e($req['full_name']) ?></td>
                                <td><?= e($req['company'] ?: '-') ?></td>
                                <td><?= e(ucfirst($req['service_interest'])) ?></td>
                                <td>
                                    
                                    <span class="status-badge status-<?= $req['status'] ?>">
                                        <?= str_replace('_', ' ', ucfirst($req['status'])) ?>
                                    </span>
                                </td>
                                <td><?= format_date($req['created_at']) ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick="openModal(<?= $req['id'] ?>)">View</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>

    <?php foreach ($requests as $req): ?>
        <div id="modal-<?= $req['id'] ?>" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Demo Request Details</h2>
                    <button class="modal-close" onclick="closeModal(<?= $req['id'] ?>)">×</button>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Name</div>
                    <div class="detail-value"><?= e($req['full_name']) ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?= e($req['email']) ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?= e($req['phone'] ?: '-') ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Company</div>
                    <div class="detail-value"><?= e($req['company'] ?: '-') ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Company Size</div>
                    <div class="detail-value"><?= e($req['company_size'] ?: '-') ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Service Interest</div>
                    <div class="detail-value"><?= e(ucfirst($req['service_interest'])) ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Preferred Contact</div>
                    <div class="detail-value"><?= e(ucfirst($req['contact_method'])) ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Message</div>
                    <div class="detail-value" style="white-space: pre-wrap;"><?= e($req['message'] ?: '-') ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">IP Address</div>
                    <div class="detail-value"><?= e($req['ip_address']) ?></div>
                </div>
                
                
                <hr style="border: none; border-top: 1px solid var(--line-subtle); margin: 1.5rem 0;">
                
                
                <form method="post">
                    <input type="hidden" name="id" value="<?= $req['id'] ?>">
                    
                    
                    <div class="form-group">
                        <label>Update Status</label>
                        
                        <select name="status" class="form-control">
                            <option value="new" <?= $req['status'] === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="scheduled" <?= $req['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                            <option value="completed" <?= $req['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="no_show" <?= $req['status'] === 'no_show' ? 'selected' : '' ?>>No Show</option>
                            <option value="closed" <?= $req['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    
                    
                    <div class="form-group">
                        <label>Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3"><?= e($req['admin_notes']) ?></textarea>
                    </div>
                    
                    
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).classList.add('active');
        }
        function closeModal(id) {
            document.getElementById('modal-' + id).classList.remove('active');
        }
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) closeModal(modal.id.replace('modal-', ''));
            });
        });
    </script>
</body>
</html>
