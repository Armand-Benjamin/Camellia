<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'config/db.php';

$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $post_name = mysqli_real_escape_string($conn, $_POST['post_name']);
                $query = "INSERT INTO Post (PostName) VALUES ('$post_name')";
                if (mysqli_query($conn, $query)) {
                    $message = 'post_added_successfully';
                } else {
                    $error = 'error_adding_post: ' . mysqli_error($conn);
                }
                break;
                
            case 'update':
                $post_id = (int)$_POST['post_id'];
                $post_name = mysqli_real_escape_string($conn, $_POST['post_name']);
                $query = "UPDATE Post SET PostName = '$post_name' WHERE PostId = $post_id";
                if (mysqli_query($conn, $query)) {
                    $message = 'post_updated_successfully';
                } else {
                    $error = 'error_updating_post: ' . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $post_id = (int)$_POST['post_id'];
                $query = "DELETE FROM Post WHERE PostId = $post_id";
                if (mysqli_query($conn, $query)) {
                    $message = 'post_deleted_successfully';
                } else {
                    $error = 'error_deleting_post: ' . mysqli_error($conn);
                }
                break;
        }
    }
}

// Get all posts
$posts_query = "SELECT * FROM Post ORDER BY PostName";
$posts_result = mysqli_query($conn, $posts_query);

// Get post for editing
$edit_post = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_query = "SELECT * FROM Post WHERE PostId = $edit_id";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_post = mysqli_fetch_assoc($edit_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Posts - Camellia HR System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">🌺</div>
                <div class="sidebar-title">camellia_hr</div>
                <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">main</div>
                    <a href="dashboard.php" class="nav-item" data-tooltip="dashboard">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">management</div>
                    <a href="posts.php" class="nav-item active" data-tooltip="job_posts">
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
                        <div class="user-role">hr_manager</div>
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
                <h1 class="page-title">job_posts</h1>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="search_posts...">
                    </div>
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <a href="logout.php" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i>
                        logout
                    </a>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <!-- Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="dashboard.php"><i class="fas fa-home"></i> home</a>
                    <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="posts.php">management</a>
                    <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                    <span>job_posts</span>
                </nav>
                
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Add/Edit Post Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-<?php echo $edit_post ? 'edit' : 'plus'; ?>"></i>
                            <?php echo $edit_post ? 'edit_post' : 'add_new_post'; ?>
                        </h3>
                    </div>
                    
                    <form method="POST" id="postForm">
                        <input type="hidden" name="action" value="<?php echo $edit_post ? 'update' : 'add'; ?>">
                        <?php if ($edit_post): ?>
                            <input type="hidden" name="post_id" value="<?php echo $edit_post['PostId']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="post_name"><i class="fas fa-briefcase"></i> post_name</label>
                            <input type="text" id="post_name" name="post_name" 
                                   value="<?php echo $edit_post ? $edit_post['PostName'] : ''; ?>" 
                                   placeholder="enter_job_position_name" required>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn">
                                <i class="fas fa-<?php echo $edit_post ? 'save' : 'plus'; ?>"></i>
                                <?php echo $edit_post ? 'update_post' : 'add_post'; ?>
                            </button>
                            
                            <?php if ($edit_post): ?>
                                <a href="posts.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Posts List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> existing_posts
                        </h3>
                        <div class="card-actions">
                            <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-secondary);">
                                total: <?php echo mysqli_num_rows($posts_result); ?> posts
                            </span>
                        </div>
                    </div>
                    
                    <?php if (mysqli_num_rows($posts_result) > 0): ?>
                        <div class="table-container">
                            <table class="table" id="postsTable">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> id</th>
                                        <th><i class="fas fa-briefcase"></i> post_name</th>
                                        <th><i class="fas fa-users"></i> candidates</th>
                                        <th><i class="fas fa-calendar"></i> created</th>
                                        <th><i class="fas fa-cogs"></i> actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($posts_result)): 
                                        // Get candidate count for this post
                                        $candidate_count_query = "SELECT COUNT(*) as count FROM CandidatesResult WHERE PostId = " . $row['PostId'];
                                        $candidate_count_result = mysqli_query($conn, $candidate_count_query);
                                        $candidate_count = mysqli_fetch_assoc($candidate_count_result)['count'];
                                    ?>
                                    <tr>
                                        <td>
                                            <span style="font-weight: 500; color: var(--primary-color);">
                                                #<?php echo str_pad($row['PostId'], 3, '0', STR_PAD_LEFT); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--accent-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 0.8rem;">
                                                    <i class="fas fa-briefcase"></i>
                                                </div>
                                                <span style="font-weight: 500;"><?php echo strtolower(str_replace(' ', '_', $row['PostName'])); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: var(--info-color)20; color: var(--info-color);">
                                                <i class="fas fa-users"></i> <?php echo $candidate_count; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                                <i class="fas fa-clock"></i> recently
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <a href="posts.php?edit=<?php echo $row['PostId']; ?>" 
                                                   class="btn btn-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">
                                                    <i class="fas fa-edit"></i> edit
                                                </a>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirmDelete('delete_this_post_permanently?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="post_id" value="<?php echo $row['PostId']; ?>">
                                                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">
                                                        <i class="fas fa-trash"></i> delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; color: var(--text-secondary); padding: 4rem; font-family: var(--font-mono);">
                            <i class="fas fa-briefcase" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.3;"></i>
                            <h3 style="margin-bottom: 1rem; color: var(--text-primary);">no_posts_found</h3>
                            <p style="font-size: 0.9rem; margin-bottom: 2rem;">create_your_first_job_post_to_get_started</p>
                            <button onclick="document.getElementById('post_name').focus()" class="btn">
                                <i class="fas fa-plus"></i> add_first_post
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Initialize search functionality
        if (document.getElementById('postsTable')) {
            searchTable('searchInput', 'postsTable');
        }
    </script>
</body>
</html>
