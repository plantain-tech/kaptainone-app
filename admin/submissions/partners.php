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
    
    Database::update('partner_inquiries', [
        'status' => $status,
        'admin_notes' => $notes
    ], 'id = :id', ['id' => $id]);
}

// Get all inquiries
$inquiries = Database::fetchAll(
    "SELECT * FROM partner_inquiries ORDER BY created_at DESC"
);

$pageTitle = 'Partner Inquiries';
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
        .status-contacted { background: #3498db; color: #fff; }
        .status-qualified { background: #9b59b6; color: #fff; }
        .status-converted { background: #27ae60; color: #fff; }
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
                    <li><a href="partners.php" class="active">Partner Inquiries</a></li>
                    <li><a href="demos.php">Demo Requests</a></li>
                    <li><a href="../dashboard.php?logout=1">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Partner Inquiries</h1>
            </div>
            
            <?php if (empty($inquiries)): ?>
                
                <p style="color: var(--text-muted);">No inquiries yet.</p>
            <?php else: ?>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php foreach ($inquiries as $inquiry): ?>
                            
                            <tr>
                                <td><?= e($inquiry['full_name']) ?></td>
                                <td><?= e($inquiry['company_name'] ?: '-') ?></td>
                                <td><?= e(str_replace('_', ' ', $inquiry['business_type'])) ?></td>
                                <td>
                                    
                                    <span class="status-badge status-<?= $inquiry['status'] ?>">
                                        <?= ucfirst($inquiry['status']) ?>
                                    </span>
                                </td>
                                <td><?= format_date($inquiry['created_at']) ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick="openModal(<?= $inquiry['id'] ?>)">View</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal -->
    <?php foreach ($inquiries as $inquiry): ?>
        <div id="modal-<?= $inquiry['id'] ?>" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Inquiry Details</h2>
                    <button class="modal-close" onclick="closeModal(<?= $inquiry['id'] ?>)">×</button>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Name</div>
                    <div class="detail-value"><?= e($inquiry['full_name']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?= e($inquiry['email']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?= e($inquiry['phone'] ?: '-') ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Company</div>
                    <div class="detail-value"><?= e($inquiry['company_name'] ?: '-') ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Business Type</div>
                    <div class="detail-value"><?= e(str_replace('_', ' ', $inquiry['business_type'])) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Location</div>
                    <div class="detail-value"><?= e(($inquiry['city'] ?: '') . ($inquiry['city'] && $inquiry['country'] ? ', ' : '') . ($inquiry['country'] ?: '-')) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Website</div>
                    <div class="detail-value">
                        <?php if ($inquiry['website']): ?>
                            <a href="<?= e($inquiry['website']) ?>" target="_blank"><?= e($inquiry['website']) ?></a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">Message</div>
                    <div class="detail-value" style="white-space: pre-wrap;"><?= e($inquiry['message'] ?: '-') ?></div>
                </div>
                
                
                <div class="detail-row">
                    <div class="detail-label">IP Address</div>
                    <div class="detail-value"><?= e($inquiry['ip_address']) ?></div>
                </div>
                
                
                <hr style="border: none; border-top: 1px solid var(--line-subtle); margin: 1.5rem 0;">
                
                
                <form method="post">
                    <input type="hidden" name="id" value="<?= $inquiry['id'] ?>">
                    
                    
                    <div class="form-group">
                        <label>Update Status</label>
                        
                        <select name="status" class="form-control">
                            <option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="contacted" <?= $inquiry['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                            <option value="qualified" <?= $inquiry['status'] === 'qualified' ? 'selected' : '' ?>>Qualified</option>
                            <option value="converted" <?= $inquiry['status'] === 'converted' ? 'selected' : '' ?>>Converted</option>
                            <option value="closed" <?= $inquiry['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    
                    
                    <div class="form-group">
                        <label>Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3"><?= e($inquiry['admin_notes']) ?></textarea>
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
