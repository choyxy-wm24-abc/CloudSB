<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$user_id = req('user_id');

$user = $_db->query('SELECT * FROM user WHERE user_id > 1')->fetchAll();

$user_search = req('user_search');
if ($user_search) {
    $stm = $_db->prepare('SELECT * FROM user  
        WHERE username LIKE ?');
    $stm->execute(["%$user_search%"]);
    $user = $stm->fetchAll();
}

?>

<link rel="stylesheet" href="../css/admin-members-modern.css">

<!-- Hero Section -->
<section class="members-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="back-navigation">
                <a href="../admin/admin.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>
            <h1 class="hero-title">
                <span class="brand-highlight">Member Management</span>
            </h1>
            <p class="hero-subtitle">Manage user accounts and member information</p>
            
            <!-- Search Section -->
            <div class="search-section">
                <form method="get" class="search-form">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <?= html_search('user_search', $user_search, 'placeholder="Search members by username..."') ?>
                        <button type="submit" class="search-btn">
                            <span>Search</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Members Section -->
<section class="members-section">
    <div class="section-container">
        <div class="members-header">
            <h2 class="section-title">Member List</h2>
            <div class="members-count"><?= count($user) ?> members found</div>
        </div>
        
        <?php if (!empty($user)): ?>
            <div class="members-grid">
                <?php foreach ($user as $u): ?>
                    <div class="member-card">
                        <div class="member-photo">
                            <img src="/photos/<?= $u->photo ?>" alt="<?= $u->username ?>" class="member-image">
                            <div class="member-status">
                                <span class="status-badge active">Active</span>
                            </div>
                        </div>
                        
                        <div class="member-info">
                            <h3 class="member-name"><?= $u->username ?></h3>
                            <p class="member-email"><?= $u->email ?></p>
                            
                            <div class="member-details">
                                <div class="detail-item">
                                    <span class="detail-label">ID:</span>
                                    <span class="detail-value"><?= $u->user_id ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Gender:</span>
                                    <span class="detail-value"><?= $u->gender ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Age:</span>
                                    <span class="detail-value"><?= $u->age ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="member-actions">
                            <a href="../admin/user_details.php?user_id=<?= $u->user_id ?>&username=<?= $u->username ?>" class="details-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <div class="no-members">
                <div class="no-members-icon">👥</div>
                <h3>No Members Found</h3>
                <p>No members match your search criteria. Try adjusting your search terms.</p>
                <?php if (!empty($user_search)): ?>
                    <a href="/admin/member.php" class="reset-btn">
                        View All Members
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>