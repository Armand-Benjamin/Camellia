<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'config/db.php';

// Get selected post for filtering
$selected_post = isset($_GET['post']) ? (int)$_GET['post'] : 0;

// Get all posts for dropdown
$posts_query = "SELECT * FROM Post ORDER BY PostName";
$posts_result = mysqli_query($conn, $posts_query);

// Build the main query
$where_clause = $selected_post ? "WHERE cr.PostId = $selected_post" : "";
$candidates_query = "SELECT cr.*, p.PostName FROM CandidatesResult cr 
                    LEFT JOIN Post p ON cr.PostId = p.PostId 
                    $where_clause
                    ORDER BY cr.Marks DESC, cr.LastName, cr.FirstName";
$candidates_result = mysqli_query($conn, $candidates_query);

// Get statistics for the selected post
$stats_query = "SELECT 
                    COUNT(*) as total_candidates,
                    AVG(Marks) as avg_marks,
                    MAX(Marks) as max_marks,
                    MIN(Marks) as min_marks
                FROM CandidatesResult cr 
                $where_clause";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Camellia HR System</title>
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
                    <a href="dashboard.php" class="nav-item">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">management</div>
                    <a href="posts.php" class="nav-item">
                        <span class="nav-icon"><i class="fas fa-briefcase"></i></span>
                        <span class="nav-text">job_posts</span>
                    </a>
                    <a href="candidates.php" class="nav-item">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        <span class="nav-text">candidates</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">reports</div>
                    <a href="reports.php" class="nav-item active">
                        <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="nav-text">analytics</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">system</div>
                    <a href="#" class="nav-item">
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
                <h1 class="page-title">analytics</h1>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="search_reports..." id="searchInput">
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
                    <a href="reports.php">reports</a>
                    <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                    <span>analytics</span>
                </nav>
                
                <!-- Filter Controls -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter"></i> filter_controls
                        </h3>
                        <div class="card-actions">
                            <button onclick="window.print()" class="btn btn-outline">
                                <i class="fas fa-print"></i> print_report
                            </button>
                            <button onclick="exportToCSV()" class="btn btn-success">
                                <i class="fas fa-file-csv"></i> export_csv
                            </button>
                        </div>
                    </div>
                    
                    <form method="GET" style="display: flex; gap: 1.5rem; align-items: end; flex-wrap: wrap;">
                        <div class="form-group" style="margin-bottom: 0; min-width: 250px;">
                            <label for="post">
                                <i class="fas fa-briefcase"></i> filter_by_position
                            </label>
                            <select id="post" name="post" onchange="this.form.submit()">
                                <option value="0">all_positions</option>
                                <?php 
                                mysqli_data_seek($posts_result, 0);
                                while ($post = mysqli_fetch_assoc($posts_result)): 
                                ?>
                                    <option value="<?php echo $post['PostId']; ?>" 
                                            <?php echo ($selected_post == $post['PostId']) ? 'selected' : ''; ?>>
                                        <?php echo strtolower(str_replace(' ', '_', $post['PostName'])); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <button type="button" onclick="resetFilters()" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> reset
                            </button>
                            <button type="button" onclick="refreshData()" class="btn">
                                <i class="fas fa-sync-alt"></i> refresh
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Statistics Overview -->
                <?php if ($stats['total_candidates'] > 0): ?>
                <div class="stats-grid">
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--info-color), #2980b9);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-users"></i></span>
                        </div>
                        <div class="stat-value"><?php echo $stats['total_candidates']; ?></div>
                        <div class="stat-label">total_candidates</div>
                        <div class="stat-change positive">
                            <i class="fas fa-chart-line"></i> filtered_results
                        </div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--success-color), #27ae60);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-chart-bar"></i></span>
                        </div>
                        <div class="stat-value"><?php echo round($stats['avg_marks'], 1); ?></div>
                        <div class="stat-label">average_score</div>
                        <div class="stat-change <?php echo $stats['avg_marks'] >= 70 ? 'positive' : 'negative'; ?>">
                            <i class="fas fa-<?php echo $stats['avg_marks'] >= 70 ? 'arrow-up' : 'arrow-down'; ?>"></i> 
                            <?php echo $stats['avg_marks'] >= 70 ? 'above_target' : 'below_target'; ?>
                        </div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color), #e67e22);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-trophy"></i></span>
                        </div>
                        <div class="stat-value"><?php echo $stats['max_marks']; ?></div>
                        <div class="stat-label">highest_score</div>
                        <div class="stat-change positive">
                            <i class="fas fa-star"></i> top_performer
                        </div>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--danger-color), #c0392b);">
                        <div class="stat-header">
                            <span class="stat-icon"><i class="fas fa-chart-line-down"></i></span>
                        </div>
                        <div class="stat-value"><?php echo $stats['min_marks']; ?></div>
                        <div class="stat-label">lowest_score</div>
                        <div class="stat-change negative">
                            <i class="fas fa-exclamation-triangle"></i> needs_attention
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Performance Analysis -->
                <?php if (mysqli_num_rows($candidates_result) > 0): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> performance_distribution
                        </h3>
                        <div class="card-actions">
                            <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-secondary);">
                                <?php 
                                if ($selected_post) {
                                    mysqli_data_seek($posts_result, 0);
                                    while ($post = mysqli_fetch_assoc($posts_result)) {
                                        if ($post['PostId'] == $selected_post) {
                                            echo 'position: ' . strtolower(str_replace(' ', '_', $post['PostName']));
                                            break;
                                        }
                                    }
                                } else {
                                    echo 'all_positions';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                        <?php
                        // Reset result pointer and calculate grade distribution
                        mysqli_data_seek($candidates_result, 0);
                        $grades = [
                            'A' => ['count' => 0, 'label' => 'excellent (80-100)', 'color' => 'var(--success-color)', 'icon' => 'fa-star'],
                            'B' => ['count' => 0, 'label' => 'good (70-79)', 'color' => 'var(--info-color)', 'icon' => 'fa-thumbs-up'],
                            'C' => ['count' => 0, 'label' => 'average (60-69)', 'color' => 'var(--warning-color)', 'icon' => 'fa-minus-circle'],
                            'D' => ['count' => 0, 'label' => 'below_average (50-59)', 'color' => 'var(--secondary-color)', 'icon' => 'fa-arrow-down'],
                            'F' => ['count' => 0, 'label' => 'fail (0-49)', 'color' => 'var(--danger-color)', 'icon' => 'fa-times-circle']
                        ];
                        $total = 0;
                        
                        while ($row = mysqli_fetch_assoc($candidates_result)) {
                            $marks = $row['Marks'];
                            if ($marks >= 80) $grades['A']['count']++;
                            elseif ($marks >= 70) $grades['B']['count']++;
                            elseif ($marks >= 60) $grades['C']['count']++;
                            elseif ($marks >= 50) $grades['D']['count']++;
                            else $grades['F']['count']++;
                            $total++;
                        }
                        
                        foreach ($grades as $grade => $data):
                            $percentage = $total > 0 ? round(($data['count'] / $total) * 100, 1) : 0;
                        ?>
                            <div class="stat-card" style="background: linear-gradient(135deg, <?php echo $data['color']; ?>, <?php echo $data['color']; ?>dd); padding: 1.5rem;">
                                <div class="stat-header" style="margin-bottom: 1rem;">
                                    <span class="stat-icon" style="font-size: 2rem;"><i class="fas <?php echo $data['icon']; ?>"></i></span>
                                </div>
                                <div class="stat-value" style="font-size: 2rem;"><?php echo $data['count']; ?></div>
                                <div class="stat-label" style="font-size: 0.9rem;">grade_<?php echo strtolower($grade); ?></div>
                                <div class="stat-change" style="background: rgba(255,255,255,0.2); margin-top: 0.5rem;">
                                    <i class="fas fa-percentage"></i> <?php echo $percentage; ?>%
                                </div>
                                <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.5rem;">
                                    <?php echo $data['label']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Detailed Results Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table"></i> detailed_results
                        </h3>
                        <div class="card-actions">
                            <button onclick="sortTable('resultsTable', 7)" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                                <i class="fas fa-sort-amount-down"></i> sort_by_score
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="table" id="resultsTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-medal"></i> rank</th>
                                    <th><i class="fas fa-id-card"></i> national_id</th>
                                    <th><i class="fas fa-user"></i> candidate</th>
                                    <th><i class="fas fa-venus-mars"></i> gender</th>
                                    <th><i class="fas fa-birthday-cake"></i> age</th>
                                    <th><i class="fas fa-briefcase"></i> position</th>
                                    <th><i class="fas fa-calendar"></i> exam_date</th>
                                    <th><i class="fas fa-phone"></i> contact</th>
                                    <th><i class="fas fa-chart-bar"></i> score</th>
                                    <th><i class="fas fa-flag"></i> grade</th>
                                    <th><i class="fas fa-star"></i> status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                mysqli_data_seek($candidates_result, 0);
                                $rank = 1;
                                while ($row = mysqli_fetch_assoc($candidates_result)): 
                                    // Calculate age
                                    $dob = new DateTime($row['DateOfBirth']);
                                    $today = new DateTime();
                                    $age = $today->diff($dob)->y;
                                    
                                    // Determine grade and status
                                    $marks = $row['Marks'];
                                    if ($marks >= 80) {
                                        $grade = 'A';
                                        $status = 'excellent';
                                        $statusColor = 'var(--success-color)';
                                        $statusIcon = 'fa-star';
                                    } elseif ($marks >= 70) {
                                        $grade = 'B';
                                        $status = 'good';
                                        $statusColor = 'var(--info-color)';
                                        $statusIcon = 'fa-thumbs-up';
                                    } elseif ($marks >= 60) {
                                        $grade = 'C';
                                        $status = 'average';
                                        $statusColor = 'var(--warning-color)';
                                        $statusIcon = 'fa-minus-circle';
                                    } elseif ($marks >= 50) {
                                        $grade = 'D';
                                        $status = 'below_average';
                                        $statusColor = 'var(--secondary-color)';
                                        $statusIcon = 'fa-arrow-down';
                                    } else {
                                        $grade = 'F';
                                        $status = 'fail';
                                        $statusColor = 'var(--danger-color)';
                                        $statusIcon = 'fa-times-circle';
                                    }
                                    
                                    // Row highlighting based on performance
                                    $rowStyle = '';
                                    if ($marks >= 80) $rowStyle = 'background: linear-gradient(135deg, rgba(39, 174, 96, 0.05), rgba(46, 204, 113, 0.02));';
                                    elseif ($marks >= 70) $rowStyle = 'background: linear-gradient(135deg, rgba(52, 152, 219, 0.05), rgba(41, 128, 185, 0.02));';
                                    elseif ($marks < 50) $rowStyle = 'background: linear-gradient(135deg, rgba(231, 76, 60, 0.05), rgba(192, 57, 43, 0.02));';
                                ?>
                                <tr style="<?php echo $rowStyle; ?>">
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <?php if ($rank <= 3): ?>
                                                <i class="fas fa-medal" style="color: <?php echo $rank == 1 ? '#FFD700' : ($rank == 2 ? '#C0C0C0' : '#CD7F32'); ?>"></i>
                                            <?php endif; ?>
                                            <span style="font-weight: 500; color: var(--primary-color);">#<?php echo $rank++; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-secondary);">
                                            <?php echo $row['CandidateNationalId']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 0.8rem;">
                                                <?php echo strtoupper(substr($row['FirstName'], 0, 1) . substr($row['LastName'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 500;"><?php echo strtolower($row['FirstName'] . '_' . $row['LastName']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="padding: 0.25rem 0.5rem; border-radius: 8px; font-size: 0.75rem; background: <?php echo $row['Gender'] == 'Male' ? 'var(--info-color)' : 'var(--secondary-color)'; ?>20; color: <?php echo $row['Gender'] == 'Male' ? 'var(--info-color)' : 'var(--secondary-color)'; ?>;">
                                            <i class="fas fa-<?php echo $row['Gender'] == 'Male' ? 'mars' : 'venus'; ?>"></i> <?php echo strtolower($row['Gender']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="color: var(--text-secondary);"><?php echo $age; ?> years</span>
                                    </td>
                                    <td>
                                        <span style="font-weight: 500;"><?php echo strtolower(str_replace(' ', '_', $row['PostName'])); ?></span>
                                    </td>
                                    <td>
                                        <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                            <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($row['ExamDate'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo $row['PhoneNumber']; ?>" style="color: var(--primary-color); text-decoration: none; font-size: 0.85rem;">
                                            <i class="fas fa-phone"></i> <?php echo $row['PhoneNumber']; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-weight: 500; color: <?php echo $statusColor; ?>; font-size: 1.1rem;">
                                                <?php echo $row['Marks']; ?>/100
                                            </span>
                                            <div style="width: 40px; height: 4px; background: var(--border-light); border-radius: 2px; overflow: hidden;">
                                                <div style="width: <?php echo $row['Marks']; ?>%; height: 100%; background: <?php echo $statusColor; ?>; transition: var(--transition);"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem; font-weight: 500; background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>;">
                                            <?php echo $grade; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>;">
                                            <i class="fas <?php echo $statusIcon; ?>"></i> <?php echo $status; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="card">
                    <div style="text-align: center; color: var(--text-secondary); padding: 4rem; font-family: var(--font-mono);">
                        <i class="fas fa-chart-line" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.3;"></i>
                        <h3 style="margin-bottom: 1rem; color: var(--text-primary);">no_data_available</h3>
                        <p style="font-size: 0.9rem; margin-bottom: 2rem;">no_candidates_found_for_selected_criteria</p>
                        <a href="candidates.php" class="btn">
                            <i class="fas fa-user-plus"></i> add_candidates
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Enhanced search functionality
        if (document.getElementById('resultsTable')) {
            searchTable('searchInput', 'resultsTable');
        }
        
        // Filter functions
        function resetFilters() {
            window.location.href = 'reports.php';
        }
        
        function refreshData() {
            window.location.reload();
        }
        
        // Export to CSV function
        function exportToCSV() {
            const table = document.getElementById('resultsTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                for (let j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                }
                csv.push(row.join(','));
            }
            
            const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
            const downloadLink = document.createElement('a');
            downloadLink.download = 'camellia_hr_report_' + new Date().toISOString().split('T')[0] + '.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
        
        // Enhanced table sorting
        function sortTable(tableId, columnIndex) {
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const aVal = a.cells[columnIndex].textContent.trim();
                const bVal = b.cells[columnIndex].textContent.trim();
                
                // Check if values are numeric
                const aNum = parseFloat(aVal.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bVal.replace(/[^\d.-]/g, ''));
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return bNum - aNum; // Descending order for numbers
                } else {
                    return aVal.localeCompare(bVal); // Ascending order for text
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // Print optimization
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
        });
        
        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });
    </script>
    
    <style>
        /* Print-specific styles */
        @media print {
            .sidebar,
            .top-header,
            .breadcrumb,
            .card-actions,
            .btn,
            .mobile-overlay {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .content-area {
                padding: 0 !important;
            }
            
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                margin-bottom: 1rem !important;
                page-break-inside: avoid;
            }
            
            .table {
                font-size: 0.7rem !important;
            }
            
            .stat-card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
        }
        
        /* Enhanced responsive design */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .form-group {
                min-width: 200px;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .card-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</body>
</html>
