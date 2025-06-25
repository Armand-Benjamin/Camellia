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
                $national_id = mysqli_real_escape_string($conn, $_POST['national_id']);
                $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
                $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
                $gender = mysqli_real_escape_string($conn, $_POST['gender']);
                $dob = mysqli_real_escape_string($conn, $_POST['dob']);
                $post_id = (int)$_POST['post_id'];
                $exam_date = mysqli_real_escape_string($conn, $_POST['exam_date']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $marks = (int)$_POST['marks'];
                
                $query = "INSERT INTO CandidatesResult (CandidateNationalId, FirstName, LastName, Gender, DateOfBirth, PostId, ExamDate, PhoneNumber, Marks) 
                         VALUES ('$national_id', '$first_name', '$last_name', '$gender', '$dob', $post_id, '$exam_date', '$phone', $marks)";
                
                if (mysqli_query($conn, $query)) {
                    $message = 'candidate_added_successfully';
                } else {
                    $error = 'error_adding_candidate: ' . mysqli_error($conn);
                }
                break;
                
            case 'update':
                $national_id = mysqli_real_escape_string($conn, $_POST['national_id']);
                $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
                $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
                $gender = mysqli_real_escape_string($conn, $_POST['gender']);
                $dob = mysqli_real_escape_string($conn, $_POST['dob']);
                $post_id = (int)$_POST['post_id'];
                $exam_date = mysqli_real_escape_string($conn, $_POST['exam_date']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $marks = (int)$_POST['marks'];
                
                $query = "UPDATE CandidatesResult SET FirstName = '$first_name', LastName = '$last_name', 
                         Gender = '$gender', DateOfBirth = '$dob', PostId = $post_id, 
                         ExamDate = '$exam_date', PhoneNumber = '$phone', Marks = $marks 
                         WHERE CandidateNationalId = '$national_id'";
                
                if (mysqli_query($conn, $query)) {
                    $message = 'candidate_updated_successfully';
                } else {
                    $error = 'error_updating_candidate: ' . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $national_id = mysqli_real_escape_string($conn, $_POST['national_id']);
                $query = "DELETE FROM CandidatesResult WHERE CandidateNationalId = '$national_id'";
                if (mysqli_query($conn, $query)) {
                    $message = 'candidate_deleted_successfully';
                } else {
                    $error = 'error_deleting_candidate: ' . mysqli_error($conn);
                }
                break;
        }
    }
}

// Get all posts for dropdown
$posts_query = "SELECT * FROM Post ORDER BY PostName";
$posts_result = mysqli_query($conn, $posts_query);

// Get all candidates
$candidates_query = "SELECT cr.*, p.PostName FROM CandidatesResult cr 
                    LEFT JOIN Post p ON cr.PostId = p.PostId 
                    ORDER BY cr.LastName, cr.FirstName";
$candidates_result = mysqli_query($conn, $candidates_query);

// Get candidate for editing
$edit_candidate = null;
if (isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_query = "SELECT * FROM CandidatesResult WHERE CandidateNationalId = '$edit_id'";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_candidate = mysqli_fetch_assoc($edit_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates - Camellia HR System</title>
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
                    <a href="posts.php" class="nav-item" data-tooltip="job_posts">
                        <span class="nav-icon"><i class="fas fa-briefcase"></i></span>
                        <span class="nav-text">job_posts</span>
                    </a>
                    <a href="candidates.php" class="nav-item active" data-tooltip="candidates">
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
                <h1 class="page-title">candidates</h1>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="search_candidates..." id="searchInput">
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
                    <a href="candidates.php">management</a>
                    <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                    <span>candidates</span>
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
                
                <!-- Quick Stats -->
                <div class="stats-grid" style="margin-bottom: 2rem;">
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--info-color), #2980b9);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-users"></i></span>
                        </div>
                        <div class="stat-value"><?php echo mysqli_num_rows($candidates_result); ?></div>
                        <div class="stat-label">total_candidates</div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--success-color), #27ae60);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-user-check"></i></span>
                        </div>
                        <div class="stat-value">
                            <?php 
                            $high_performers = mysqli_query($conn, "SELECT COUNT(*) as count FROM CandidatesResult WHERE Marks >= 70");
                            echo mysqli_fetch_assoc($high_performers)['count'];
                            ?>
                        </div>
                        <div class="stat-label">high_performers</div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color), #e67e22);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-user-clock"></i></span>
                        </div>
                        <div class="stat-value">
                            <?php 
                            $recent_candidates = mysqli_query($conn, "SELECT COUNT(*) as count FROM CandidatesResult WHERE ExamDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                            echo mysqli_fetch_assoc($recent_candidates)['count'];
                            ?>
                        </div>
                        <div class="stat-label">this_week</div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--accent-color), var(--accent-light));">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-chart-bar"></i></span>
                        </div>
                        <div class="stat-value">
                            <?php 
                            $avg_score = mysqli_query($conn, "SELECT AVG(Marks) as avg FROM CandidatesResult");
                            echo round(mysqli_fetch_assoc($avg_score)['avg'], 1);
                            ?>
                        </div>
                        <div class="stat-label">average_score</div>
                    </div>
                </div>
                
                <!-- Add/Edit Candidate Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-<?php echo $edit_candidate ? 'user-edit' : 'user-plus'; ?>"></i>
                            <?php echo $edit_candidate ? 'edit_candidate' : 'add_new_candidate'; ?>
                        </h3>
                        <?php if ($edit_candidate): ?>
                            <div class="card-actions">
                                <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-secondary);">
                                    editing: <?php echo $edit_candidate['CandidateNationalId']; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" id="candidateForm">
                        <input type="hidden" name="action" value="<?php echo $edit_candidate ? 'update' : 'add'; ?>">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="national_id"><i class="fas fa-id-card"></i> national_id</label>
                                <input type="text" id="national_id" name="national_id" 
                                       value="<?php echo $edit_candidate ? $edit_candidate['CandidateNationalId'] : ''; ?>" 
                                       placeholder="enter_national_id"
                                       <?php echo $edit_candidate ? 'readonly' : ''; ?> required>
                            </div>
                            
                            <div class="form-group">
                                <label for="first_name"><i class="fas fa-user"></i> first_name</label>
                                <input type="text" id="first_name" name="first_name" 
                                       value="<?php echo $edit_candidate ? $edit_candidate['FirstName'] : ''; ?>" 
                                       placeholder="enter_first_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name"><i class="fas fa-user"></i> last_name</label>
                                <input type="text" id="last_name" name="last_name" 
                                       value="<?php echo $edit_candidate ? $edit_candidate['LastName'] : ''; ?>" 
                                       placeholder="enter_last_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="gender"><i class="fas fa-venus-mars"></i> gender</label>
                                <select id="gender" name="gender" required>
                                    <option value="">select_gender</option>
                                    <option value="Male" <?php echo ($edit_candidate && $edit_candidate['Gender'] == 'Male') ? 'selected' : ''; ?>>male</option>
                                    <option value="Female" <?php echo ($edit_candidate && $edit_candidate['Gender'] == 'Female') ? 'selected' : ''; ?>>female</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="dob"><i class="fas fa-birthday-cake"></i> date_of_birth</label>
                                <input type="date" id="dob" name="dob" 
                                       value="<?php echo $edit_candidate ? $edit_candidate['DateOfBirth'] : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="post_id"><i class="fas fa-briefcase"></i> position_applied</label>
                                <select id="post_id" name="post_id" required>
                                    <option value="">select_position</option>
                                    <?php 
                                    mysqli_data_seek($posts_result, 0);
                                    while ($post = mysqli_fetch_assoc($posts_result)): 
                                    ?>
                                        <option value="<?php echo $post['PostId']; ?>" 
                                                <?php echo ($edit_candidate && $edit_candidate['PostId'] == $post['PostId']) ? 'selected' : ''; ?>>
                                            <?php echo strtolower(str_replace(' ', '_', $post['PostName'])); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="exam_date"><i class="fas fa-calendar-alt"></i> exam_date</label>
                                <input type="date" id="exam_date" name="exam_date" 
                                       value="<?php echo $edit_candidate ? $edit_candidate['ExamDate'] : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone"></i> phone_number</label>
                                <input type="tel" id="phone" name="phone" 
                                       value="<?php echo $edit_candidate ? $edit_candidate['PhoneNumber'] : ''; ?>" 
                                       placeholder="enter_phone_number" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="marks"><i class="fas fa-chart-bar"></i> marks (0-100)</label>
                                <input type="number" id="marks" name="marks" min="0" max="100" 
                                       value="<?php echo $edit_candidate ? $edit_candidate['Marks'] : ''; ?>" 
                                       placeholder="enter_marks"
                                       onchange="validateMarks(this)" required>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                            <button type="submit" class="btn">
                                <i class="fas fa-<?php echo $edit_candidate ? 'save' : 'plus'; ?>"></i>
                                <?php echo $edit_candidate ? 'update_candidate' : 'add_candidate'; ?>
                            </button>
                            
                            <?php if ($edit_candidate): ?>
                                <a href="candidates.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Candidates List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> all_candidates
                        </h3>
                        <div class="card-actions">
                            <button class="btn btn-outline" onclick="exportCandidates()">
                                <i class="fas fa-download"></i> export
                            </button>
                        </div>
                    </div>
                    
                    <?php if (mysqli_num_rows($candidates_result) > 0): ?>
                        <div class="table-container">
                            <table class="table" id="candidatesTable">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-id-card"></i> national_id</th>
                                        <th><i class="fas fa-user"></i> candidate</th>
                                        <th><i class="fas fa-venus-mars"></i> gender</th>
                                        <th><i class="fas fa-briefcase"></i> position</th>
                                        <th><i class="fas fa-calendar"></i> exam_date</th>
                                        <th><i class="fas fa-phone"></i> contact</th>
                                        <th><i class="fas fa-chart-bar"></i> score</th>
                                        <th><i class="fas fa-flag"></i> status</th>
                                        <th><i class="fas fa-cogs"></i> actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    mysqli_data_seek($candidates_result, 0);
                                    while ($row = mysqli_fetch_assoc($candidates_result)): 
                                        // Calculate age
                                        $dob = new DateTime($row['DateOfBirth']);
                                        $today = new DateTime();
                                        $age = $today->diff($dob)->y;
                                        
                                        // Determine status
                                        $marks = $row['Marks'];
                                        if ($marks >= 80) {
                                            $status = 'excellent';
                                            $statusColor = 'var(--success-color)';
                                            $statusIcon = 'fa-star';
                                        } elseif ($marks >= 70) {
                                            $status = 'good';
                                            $statusColor = 'var(--info-color)';
                                            $statusIcon = 'fa-thumbs-up';
                                        } elseif ($marks >= 50) {
                                            $status = 'average';
                                            $statusColor = 'var(--warning-color)';
                                            $statusIcon = 'fa-minus-circle';
                                        } else {
                                            $status = 'needs_review';
                                            $statusColor = 'var(--danger-color)';
                                            $statusIcon = 'fa-exclamation-triangle';
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <span style="font-weight: 500; color: var(--primary-color);">
                                                <?php echo $row['CandidateNationalId']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 0.9rem;">
                                                    <?php echo strtoupper(substr($row['FirstName'], 0, 1) . substr($row['LastName'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 500;"><?php echo strtolower($row['FirstName'] . '_' . $row['LastName']); ?></div>
                                                    <div style="font-size: 0.75rem; color: var(--text-secondary);"><?php echo $age; ?> years_old</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?php echo $row['Gender'] == 'Male' ? 'var(--info-color)' : 'var(--secondary-color)'; ?>20; color: <?php echo $row['Gender'] == 'Male' ? 'var(--info-color)' : 'var(--secondary-color)'; ?>;">
                                                <i class="fas fa-<?php echo $row['Gender'] == 'Male' ? 'mars' : 'venus'; ?>"></i> <?php echo strtolower($row['Gender']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo strtolower(str_replace(' ', '_', $row['PostName'])); ?></td>
                                        <td>
                                            <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                                <i class="fas fa-calendar"></i> <?php echo date('Y-m-d', strtotime($row['ExamDate'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="tel:<?php echo $row['PhoneNumber']; ?>" style="color: var(--primary-color); text-decoration: none;">
                                                <i class="fas fa-phone"></i> <?php echo $row['PhoneNumber']; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="font-weight: 500; color: <?php echo $statusColor; ?>">
                                                    <?php echo $row['Marks']; ?>/100
                                                </span>
                                                <div style="width: 50px; height: 4px; background: var(--border-light); border-radius: 2px; overflow: hidden;">
                                                    <div style="width: <?php echo $row['Marks']; ?>%; height: 100%; background: <?php echo $statusColor; ?>; transition: var(--transition);"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>;">
                                                <i class="fas <?php echo $statusIcon; ?>"></i> <?php echo $status; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <a href="candidates.php?edit=<?php echo $row['CandidateNationalId']; ?>" 
                                                   class="btn btn-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">
                                                    <i class="fas fa-edit"></i> edit
                                                </a>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirmDelete('delete_this_candidate_permanently?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="national_id" value="<?php echo $row['CandidateNationalId']; ?>">
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
                            <i class="fas fa-users" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.3;"></i>
                            <h3 style="margin-bottom: 1rem; color: var(--text-primary);">no_candidates_found</h3>
                            <p style="font-size: 0.9rem; margin-bottom: 2rem;">add_your_first_candidate_to_get_started</p>
                            <button onclick="document.getElementById('national_id').focus()" class="btn">
                                <i class="fas fa-user-plus"></i> add_first_candidate
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
        if (document.getElementById('candidatesTable')) {
            searchTable('searchInput', 'candidatesTable');
        }
        
        // Export functionality
        function exportCandidates() {
            window.print();
        }
        
        // Enhanced form validation
        document.getElementById('candidateForm').addEventListener('submit', function(e) {
            const marks = document.getElementById('marks').value;
            if (marks < 0 || marks > 100) {
                e.preventDefault();
                alert('marks_must_be_between_0_and_100');
                return;
            }
        });
        
        // Real-time form validation
        document.getElementById('marks').addEventListener('input', function() {
            validateMarks(this);
        });
        
        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + '-' + value.slice(3);
                } else {
                    value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            this.value = value;
        });
        
        // Auto-focus on form errors
        document.addEventListener('DOMContentLoaded', function() {
            const errorInputs = document.querySelectorAll('.form-group input:invalid');
            if (errorInputs.length > 0) {
                errorInputs[0].focus();
            }
        });
        
        // Enhanced search with highlighting
        function enhancedSearch() {
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('candidatesTable');
            
            if (!searchInput || !table) return;
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let found = false;
                    
                    // Reset highlighting
                    cells.forEach(cell => {
                        cell.innerHTML = cell.textContent;
                    });
                    
                    // Search and highlight
                    cells.forEach(cell => {
                        const text = cell.textContent.toLowerCase();
                        if (text.includes(searchTerm) && searchTerm.length > 0) {
                            found = true;
                            const regex = new RegExp(`(${searchTerm})`, 'gi');
                            cell.innerHTML = cell.textContent.replace(regex, 
                                '<mark style="background: var(--accent-light); padding: 0.1em 0.2em; border-radius: 3px;">$1</mark>'
                            );
                        }
                    });
                    
                    if (found || searchTerm.length === 0) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update results counter
                updateResultsCounter(visibleCount, rows.length);
            });
        }
        
        function updateResultsCounter(visible, total) {
            let counter = document.querySelector('.results-counter');
            if (!counter) {
                counter = document.createElement('div');
                counter.className = 'results-counter';
                counter.style.cssText = `
                    position: absolute;
                    top: 1rem;
                    right: 1rem;
                    background: var(--bg-secondary);
                    padding: 0.5rem 1rem;
                    border-radius: var(--border-radius-small);
                    font-family: var(--font-mono);
                    font-size: 0.8rem;
                    color: var(--text-secondary);
                    border: 1px solid var(--border-color);
                `;
                document.querySelector('.table-container').style.position = 'relative';
                document.querySelector('.table-container').appendChild(counter);
            }
            
            counter.textContent = `showing ${visible} of ${total} candidates`;
            
            if (visible === 0 && total > 0) {
                counter.style.color = 'var(--danger-color)';
                counter.innerHTML = '<i class="fas fa-search"></i> no_matches_found';
            } else {
                counter.style.color = 'var(--text-secondary)';
            }
        }
        
        // Initialize enhanced search
        enhancedSearch();
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('searchInput').focus();
            }
            
            // Ctrl/Cmd + N to add new candidate
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                document.getElementById('national_id').focus();
            }
            
            // Escape to clear search
            if (e.key === 'Escape') {
                const searchInput = document.getElementById('searchInput');
                if (searchInput.value) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
            }
        });
    </script>
</body>
</html>
