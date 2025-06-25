<?php
include 'config/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include './config/db.php';

// Get statistics with error handling
$posts_count = 0;
$candidates_count = 0;
$avg_marks = 0;
$top_score = 0;

if ($conn) {
    $posts_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM Post");
    if ($posts_result) {
        $posts_count = mysqli_fetch_assoc($posts_result)['count'];
    }
    
    $candidates_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM CandidatesResult");
    if ($candidates_result) {
        $candidates_count = mysqli_fetch_assoc($candidates_result)['count'];
    }
    
    $avg_result = mysqli_query($conn, "SELECT AVG(Marks) as avg FROM CandidatesResult");
    if ($avg_result) {
        $avg_marks = mysqli_fetch_assoc($avg_result)['avg'];
    }
    
    $top_result = mysqli_query($conn, "SELECT MAX(Marks) as max FROM CandidatesResult");
    if ($top_result) {
        $top_score = mysqli_fetch_assoc($top_result)['max'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Camellia HR System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<style>
    /* CAMELLIA HR SYSTEM DASHBOARD - MATCHING DESIGN STYLE */

@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap");
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css");

:root {
  --primary-bg: #fdf6e3;
  --accent-color: #6f4e37;
  --accent-light: #8b6f47;
  --accent-dark: #5a3e2a;
  --heading-color: #3c3b3f;
  --text-color: #222;
  --text-secondary: #999;
  --text-light: #ccc;
  --text-white: #ffffff;
  --input-border: #e0e0e0;
  --input-bg: #ffffff;
  --table-stripe: #f9f5e0;
  --table-border: #e8dcc6;
  --shadow-light: 0 2px 8px rgba(111, 78, 55, 0.1);
  --shadow-medium: 0 4px 16px rgba(111, 78, 55, 0.15);
  --shadow-heavy: 0 8px 32px rgba(111, 78, 55, 0.2);
  --success-color: #4a7c59;
  --warning-color: #d4a574;
  --danger-color: #c85a54;
  --info-color: #7ba7bc;
  --border-radius: 12px;
  --border-radius-small: 8px;
  --border-radius-large: 16px;
  --transition: all 0.3s ease;
  --sidebar-width: 280px;
  --header-height: 70px;
  --font-family: "Plus Jakarta Sans", sans-serif;
}

body {
  background: var(--primary-bg);
  font-family: var(--font-family);
  color: var(--text-color);
  margin: 0;
}

.app-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: var(--sidebar-width);
  background: #f8f6f3;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
  padding-bottom: 2rem;
}

.sidebar-header {
  padding: 2rem 1.5rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #e8e5e0;
}

.sidebar-logo {
  width: 40px;
  height: 40px;
  background: var(--accent-color);
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  font-weight: 600;
  font-size: 1.25rem;
}

.sidebar-title {
  color: var(--accent-color);
  font-size: 1.4rem;
  font-weight: 700;
}

.sidebar-toggle {
  margin-left: auto;
  background: transparent;
  border: 1px solid var(--input-border);
  border-radius: var(--border-radius-small);
  padding: 0.4rem 0.6rem;
  cursor: pointer;
  color: var(--text-secondary);
}

.sidebar-nav {
  padding: 2rem 0;
}

.nav-section {
  padding: 0 1.5rem;
  margin-bottom: 1.5rem;
}

.nav-section-title {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-secondary);
  text-transform: uppercase;
  margin-bottom: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0.5rem 0;
  gap: 0.75rem;
  color: var(--text-color);
  text-decoration: none;
  font-weight: 500;
  transition: var(--transition);
}

.nav-item:hover {
  color: var(--accent-color);
}

.nav-item.active {
  color: white;
  background: var(--accent-dark);
  border-radius: var(--border-radius-small);
  padding-left: 1rem;
}

.nav-icon {
  width: 20px;
  text-align: center;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid #e8e5e0;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: white;
  padding: 1rem;
  border-radius: var(--border-radius);
  border: 1px solid #e8e5e0;
}

.user-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  box-shadow: var(--shadow-light);
}

.main-content {
  flex: 1;
  padding-left: var(--sidebar-width);
  padding: 2rem;
}

.top-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: var(--header-height);
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--accent-color);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-box {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
}

.search-input {
  padding: 0.5rem 1rem 0.5rem 2rem;
  border: 1px solid var(--input-border);
  border-radius: var(--border-radius-small);
  font-size: 1rem;
  background: var(--input-bg);
  color: var(--text-color);
}

.notification-btn {
  position: relative;
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: var(--text-color);
}

.notification-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: var(--danger-color);
  color: white;
  font-size: 0.6rem;
  padding: 0.2rem 0.4rem;
  border-radius: 999px;
}

.btn {
  background: var(--accent-color);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: var(--border-radius-small);
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-outline {
  background: transparent;
  color: var(--accent-color);
  border: 1px solid var(--accent-color);
}

.btn:hover {
  background: var(--accent-dark);
}

.btn-outline:hover {
  background: var(--accent-color);
  color: white;
}

.card {
  background: white;
  border-radius: var(--border-radius);
  padding: 1.5rem;
  box-shadow: var(--shadow-light);
  border: 1px solid #eaeaea;
  margin-bottom: 2rem;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.card-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--heading-color);
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-medium);
  border: 1px solid #f0ece3;
  text-align: center;
}

.stat-icon {
  font-size: 1.8rem;
  color: var(--accent-color);
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
}

.stat-label {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.stat-change {
  font-size: 0.8rem;
  font-weight: 500;
  margin-top: 0.25rem;
}

.stat-change.positive {
  color: var(--success-color);
}

.stat-change.negative {
  color: var(--danger-color);
}

.breadcrumb {
  font-size: 0.9rem;
  margin-bottom: 1rem;
  color: var(--text-secondary);
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.breadcrumb a {
  color: var(--accent-color);
  text-decoration: none;
  font-weight: 500;
}

.table-container {
  overflow-x: auto;
  border: 1px solid var(--table-border);
  border-radius: var(--border-radius);
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th, .table td {
  padding: 1rem;
  border-bottom: 1px solid var(--table-border);
  text-align: left;
  font-size: 0.9rem;
}

.table th {
  background: var(--table-stripe);
  font-weight: 600;
  color: var(--heading-color);
}

.table-striped tbody tr:nth-child(even) {
  background: var(--table-stripe);
}

</style>
<body>
    <div class="app-layout">
        <!-- Professional Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">🌺</div>
                <div class="sidebar-title">Camellia</div>
                <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">main</div>
                    <a href="dashboard.php" class="nav-item active" data-tooltip="dashboard">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">management</div>
                    <a href="posts.php" class="nav-item" data-tooltip="job_posts">
                        <span class="nav-icon"><i class="fas fa-briefcase"></i></span>
                        <span class="nav-text">job_posts</span>
                    </a>
                    <a href="candidates.php" class="nav-item" data-tooltip="candidates">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        <span class="nav-text">candidates</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">reports</div>
                    <a href="reports.php" class="nav-item" data-tooltip="analytics">
                        <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="nav-text">analytics</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">system</div>
                    <a href="#" class="nav-item" data-tooltip="settings">
                        <span class="nav-icon"><i class="fas fa-cog"></i></span>
                        <span class="nav-text">settings</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                    <div class="user-details">
                        <div class="user-name"><?php echo $_SESSION['username']; ?></div>
                        <div class="user-role">HR Manager</div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Mobile Overlay -->
        <div class="mobile-overlay"></div>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
                <h1 class="page-title">Dashboard</h1>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Search anything...">
                    </div>
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <a href="logout.php" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <!-- Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
                    <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Dashboard</span>
                </nav>
                
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-briefcase"></i></span>
                        </div>
                        <div class="stat-value"><?php echo $posts_count; ?></div>
                        <div class="stat-label">Active Job Posts</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> +2 this month
                        </div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--info-color), #5a9fd4);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-users"></i></span>
                        </div>
                        <div class="stat-value"><?php echo $candidates_count; ?></div>
                        <div class="stat-label">Total Candidates</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> +12 this week
                        </div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color), #e6a85c);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-chart-bar"></i></span>
                        </div>
                        <div class="stat-value"><?php echo $avg_marks ? round($avg_marks, 1) : '0'; ?></div>
                        <div class="stat-label">Average Score</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> +5.2% improvement
                        </div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--success-color), #5a9b6b);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-trophy"></i></span>
                        </div>
                        <div class="stat-value"><?php echo $top_score ?: '0'; ?></div>
                        <div class="stat-label">Highest Score</div>
                        <div class="stat-change positive">
                            <i class="fas fa-star"></i> New record!
                        </div>
                    </div>
                </div>
                
                <!-- Content Grid -->
                <div class="grid grid-2">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="posts.php" class="btn">
                                <i class="fas fa-briefcase"></i> Manage Job Posts
                            </a>
                            <a href="candidates.php" class="btn btn-secondary">
                                <i class="fas fa-user-plus"></i> Add New Candidate
                            </a>
                            <a href="reports.php" class="btn btn-success">
                                <i class="fas fa-chart-line"></i> View Reports
                            </a>
                        </div>
                    </div>
                    
                    <!-- System Status -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">System Status</h3>
                        </div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.8;">
                            <p><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Database Connection: Active</p>
                            <p><i class="fas fa-check-circle" style="color: var(--success-color);"></i> User Sessions: <?php echo session_id() ? 'Active' : 'Inactive'; ?></p>
                            <p><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Last Backup: <?php echo date('Y-m-d H:i'); ?></p>
                            <p><i class="fas fa-info-circle" style="color: var(--info-color);"></i> System Uptime: 99.9%</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                        <div class="card-actions">
                            <button class="btn btn-outline">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem; line-height: 2;">
                        <p><i class="fas fa-user-plus" style="color: var(--success-color);"></i> New candidate John Doe applied for Barista position</p>
                        <p><i class="fas fa-calendar-check" style="color: var(--info-color);"></i> Interview scheduled for Jane Smith</p>
                        <p><i class="fas fa-file-alt" style="color: var(--warning-color);"></i> 3 new applications received today</p>
                        <p><i class="fas fa-chart-bar" style="color: var(--accent-color);"></i> Monthly report generated</p>
                        <p><i class="fas fa-cog" style="color: var(--text-secondary);"></i> System maintenance completed</p>
                    </div>
                </div>
                
                <!-- Recent Candidates -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Candidates</h3>
                        <div class="card-actions">
                            <a href="candidates.php" class="btn btn-outline">
                                <i class="fas fa-eye"></i> View All
                            </a>
                        </div>
                    </div>
                    
                    <?php
                    if ($conn) {
                        $recent_query = "SELECT cr.*, p.PostName FROM CandidatesResult cr 
                                       LEFT JOIN Post p ON cr.PostId = p.PostId 
                                       ORDER BY cr.ExamDate DESC LIMIT 5";
                        $recent_result = mysqli_query($conn, $recent_query);
                        
                        if ($recent_result && mysqli_num_rows($recent_result) > 0):
                    ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user"></i> Candidate</th>
                                        <th><i class="fas fa-briefcase"></i> Position</th>
                                        <th><i class="fas fa-calendar"></i> Exam Date</th>
                                        <th><i class="fas fa-chart-bar"></i> Score</th>
                                        <th><i class="fas fa-flag"></i> Status</th>
                                        <th><i class="fas fa-cogs"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($recent_result)): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--accent-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 0.8rem;">
                                                    <?php echo strtoupper(substr($row['FirstName'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 500;"><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></div>
                                                    <div style="font-size: 0.75rem; color: var(--text-secondary);"><?php echo $row['CandidateNationalId']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $row['PostName']; ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['ExamDate'])); ?></td>
                                        <td>
                                            <span style="font-weight: 500; color: <?php echo $row['Marks'] >= 70 ? 'var(--success-color)' : ($row['Marks'] >= 50 ? 'var(--warning-color)' : 'var(--danger-color)'); ?>">
                                                <?php echo $row['Marks']; ?>/100
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $status = $row['Marks'] >= 70 ? 'Excellent' : ($row['Marks'] >= 50 ? 'Good' : 'Needs Review');
                                            $statusColor = $row['Marks'] >= 70 ? 'var(--success-color)' : ($row['Marks'] >= 50 ? 'var(--warning-color)' : 'var(--danger-color)');
                                            $statusIcon = $row['Marks'] >= 70 ? 'fa-check-circle' : ($row['Marks'] >= 50 ? 'fa-exclamation-circle' : 'fa-times-circle');
                                            ?>
                                            <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>;">
                                                <i class="fas <?php echo $statusIcon; ?>"></i> <?php echo $status; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <a href="candidates.php?edit=<?php echo $row['CandidateNationalId']; ?>" 
                                                   style="color: var(--info-color); font-size: 0.9rem; padding: 0.25rem;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" onclick="return confirmDelete('Delete this candidate?')" 
                                                   style="color: var(--danger-color); font-size: 0.9rem; padding: 0.25rem;">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>No candidates found</p>
                            <p style="font-size: 0.8rem; margin-top: 0.5rem;">Add your first candidate to get started</p>
                        </div>
                    <?php endif; } else { ?>
                        <div style="text-align: center; color: var(--danger-color); padding: 2rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                            <p>Database connection error</p>
                            <p style="font-size: 0.8rem;">Please check your database configuration</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/script.js"></script>
</body>
</html>
