<?php
require_once 'db.php';
requireLogin();

$user = getCurrentUser();
$conn = getDBConnection();

// 카테고리 필터
$category = isset($_GET['category']) ? cleanInput($_GET['category']) : '전체';

// 페이지네이션 설정
$posts_per_page = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $posts_per_page;

// 게시글 수정 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_post'])) {
    $post_id = (int)$_POST['post_id'];
    $title = cleanInput($_POST['title']);
    $content = cleanInput($_POST['content']);
    $post_category = cleanInput($_POST['category']);
    
    // 작성자 본인 확인
    $check_stmt = $conn->prepare("SELECT user_id FROM anonymous_posts WHERE id = ?");
    $check_stmt->bind_param("i", $post_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $post_data = $result->fetch_assoc();
    
    if ($post_data && $post_data['user_id'] == $user['id']) {
        $stmt = $conn->prepare("UPDATE anonymous_posts SET title = ?, content = ?, category = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $title, $content, $post_category, $post_id, $user['id']);
        $stmt->execute();
        $stmt->close();
        header("Location: board.php?category=$category&view=$post_id&edited=1");
        exit();
    }
    $check_stmt->close();
}

// AJAX 좋아요 토글 처리
if (isset($_POST['toggle_like']) && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $post_id = (int)$_POST['post_id'];
    
    $check_stmt = $conn->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $post_id, $user['id']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $delete_stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $post_id, $user['id']);
        $delete_stmt->execute();
        $conn->query("UPDATE anonymous_posts SET likes = likes - 1 WHERE id = $post_id");
        $delete_stmt->close();
        $liked = false;
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $post_id, $user['id']);
        $insert_stmt->execute();
        $conn->query("UPDATE anonymous_posts SET likes = likes + 1 WHERE id = $post_id");
        $insert_stmt->close();
        $liked = true;
    }
    
    $check_stmt->close();
    
    // 현재 좋아요 수 조회
    $count_result = $conn->query("SELECT likes FROM anonymous_posts WHERE id = $post_id");
    $like_count = $count_result->fetch_assoc()['likes'];
    
    echo json_encode(['success' => true, 'liked' => $liked, 'likes' => $like_count]);
    exit();
}

// 게시글 작성 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_post'])) {
    $title = cleanInput($_POST['title']);
    $content = cleanInput($_POST['content']);
    $post_category = cleanInput($_POST['category']);
    
    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO anonymous_posts (user_id, title, content, category) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user['id'], $title, $content, $post_category);
        $stmt->execute();
        $stmt->close();
        header("Location: board.php?category=$post_category&success=1");
        exit();
    }
}

// 게시글 삭제 처리
if (isset($_GET['delete_post'])) {
    $post_id = (int)$_GET['delete_post'];
    $stmt = $conn->prepare("DELETE FROM anonymous_posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user['id']);
    $stmt->execute();
    $stmt->close();
    header("Location: board.php?category=$category&deleted=1");
    exit();
}

// 댓글 작성 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment'])) {
    $post_id = (int)$_POST['post_id'];
    $content = cleanInput($_POST['comment_content']);
    
    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO anonymous_comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user['id'], $content);
        $stmt->execute();
        $stmt->close();
        header("Location: board.php?category=$category&view={$post_id}&page={$page}#comments");
        exit();
    }
}

// 댓글 삭제 처리
if (isset($_GET['delete_comment'])) {
    $comment_id = (int)$_GET['delete_comment'];
    $post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
    
    $stmt = $conn->prepare("DELETE FROM anonymous_comments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user['id']);
    $stmt->execute();
    $stmt->close();
    
    header("Location: board.php?category=$category&view={$post_id}&page={$page}#comments");
    exit();
}

// 카테고리별 게시글 수 조회
$categories = ['전체', '자유', '질문', '정보', '고민', '후기'];
$category_counts = [];

foreach ($categories as $cat) {
    if ($cat == '전체') {
        $count_result = $conn->query("SELECT COUNT(*) as total FROM anonymous_posts");
    } else {
        $cat_escaped = $conn->real_escape_string($cat);
        $count_result = $conn->query("SELECT COUNT(*) as total FROM anonymous_posts WHERE category = '$cat_escaped'");
    }
    $category_counts[$cat] = $count_result->fetch_assoc()['total'];
}

// 전체 게시글 수 조회 (현재 카테고리)
if ($category == '전체') {
    $count_query = "SELECT COUNT(*) as total FROM anonymous_posts";
} else {
    $category_escaped = $conn->real_escape_string($category);
    $count_query = "SELECT COUNT(*) as total FROM anonymous_posts WHERE category = '$category_escaped'";
}
$count_result = $conn->query($count_query);
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// 게시글 목록 조회
if ($category == '전체') {
    $posts_query = "SELECT p.*, u.username, 
                    (SELECT COUNT(*) FROM anonymous_comments WHERE post_id = p.id) as comment_count,
                    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_liked
                    FROM anonymous_posts p 
                    JOIN users u ON p.user_id = u.id 
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?";
} else {
    $posts_query = "SELECT p.*, u.username, 
                    (SELECT COUNT(*) FROM anonymous_comments WHERE post_id = p.id) as comment_count,
                    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_liked
                    FROM anonymous_posts p 
                    JOIN users u ON p.user_id = u.id 
                    WHERE p.category = ?
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?";
}

$stmt = $conn->prepare($posts_query);
if ($category == '전체') {
    $stmt->bind_param("iii", $user['id'], $posts_per_page, $offset);
} else {
    $stmt->bind_param("isii", $user['id'], $category, $posts_per_page, $offset);
}
$stmt->execute();
$posts = $stmt->get_result();

// 특정 게시글 상세 보기
$viewing_post = null;
$comments = null;
if (isset($_GET['view'])) {
    $post_id = (int)$_GET['view'];
    
    $conn->query("UPDATE anonymous_posts SET views = views + 1 WHERE id = $post_id");
    
    $view_stmt = $conn->prepare("SELECT p.*, u.username, 
                                  (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_liked
                                  FROM anonymous_posts p 
                                  JOIN users u ON p.user_id = u.id 
                                  WHERE p.id = ?");
    $view_stmt->bind_param("ii", $user['id'], $post_id);
    $view_stmt->execute();
    $viewing_post = $view_stmt->get_result()->fetch_assoc();
    
    if ($viewing_post) {
        $comments_stmt = $conn->prepare("SELECT c.*, u.username 
                                         FROM anonymous_comments c 
                                         JOIN users u ON c.user_id = u.id 
                                         WHERE c.post_id = ? 
                                         ORDER BY c.created_at ASC");
        $comments_stmt->bind_param("i", $post_id);
        $comments_stmt->execute();
        $comments = $comments_stmt->get_result();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>익명 게시판 - Company Community Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .board-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .category-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow-x: auto;
            flex-wrap: wrap;
        }

        .category-tab {
            padding: 0.75rem 1.5rem;
            border: none;
            background: #f3f4f6;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            position: relative;
            user-select: none;
            outline: none;
        }

        .category-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .category-count {
            display: inline-block;
            margin-left: 0.5rem;
            padding: 0.15rem 0.5rem;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            font-size: 0.85rem;
        }

        .active .category-count {
            background: rgba(255, 255, 255, 0.2);
        }

        .board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .board-header h2 {
            color: #1f2937;
            font-size: 1.5rem;
            user-select: none;
        }

        .btn-write {
          
            color: white;
            padding: 0.1rem 1rem;
            border: none;
            border-radius:3px;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            outline: none;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .post-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            cursor: pointer;
            position: relative;
            border: 2px solid transparent;
            user-select: none;
        }

        .post-card-header {
            margin-bottom: 1rem;
        }

        .post-badges {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            user-select: none;
        }

        .badge.anonymous {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.category {
            background: #dbeafe;
            color: #1e40af;
        }

        .post-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        .post-content-preview {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .post-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #9ca3af;
            margin-bottom: 1rem;
            user-select: none;
        }

        .post-stats {
            display: flex;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .stat-item {
            font-size: 0.85rem;
            color: #6b7280;
            user-select: none;
        }

        .like-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            color: #6b7280;
            padding: 0;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            user-select: none;
            outline: none;
        }

        .like-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .like-btn.liked {
            color: #ef4444;
        }

        .post-actions {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            outline: none;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .post-detail {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .post-detail-header {
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 2rem;
        }

        .post-detail-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin: 1rem 0;
        }

        .post-detail-content {
            font-size: 1rem;
            line-height: 1.8;
            color: #374151;
            margin-bottom: 2rem;
            white-space: pre-wrap;
        }

        .detail-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .comments-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .comment-form {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .comment-form textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-comment {
            margin-top: 1rem;
         
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }

        .comment-item {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .comment-author {
            font-weight: 600;
            color: #1f2937;
            margin-right: 0.75rem;
        }

        .comment-time {
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .comment-delete {
            color: #dc2626;
            font-size: 0.85rem;
            text-decoration: none;
            font-weight: 600;
            margin-left: 1rem;
        }

        .comment-content {
            color: #374151;
            line-height: 1.6;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-btn {
            padding: 0.75rem 1.25rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            text-decoration: none;
            color: #374151;
            font-weight: 600;
            outline: none;
        }

        .page-btn.active {
          
            color: white;
            border-color: transparent;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .modal-header {
            padding: 2rem;
            border-bottom: 2px solid #e5e7eb;
            position: relative;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            color: #1f2937;
        }

        .modal-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: #6b7280;
            line-height: 1;
            outline: none;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 200px;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
          
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #6ee7b7;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .category-tabs {
                flex-wrap: wrap;
            }

            .posts-grid {
                grid-template-columns: 1fr;
            }

            .board-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .post-detail-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo" onclick="location.href='index.php'" style="cursor:pointer">
                <h1>Company</h1>
            </div>
            <nav class="nav" id="nav">
                <div class="nav-item">
                    <a href="index.php">홈</a>
                </div>
                <div class="nav-item dropdown">
                    <a href="study.php">스터디</a>
                    <div class="dropdown-menu">
                        <a href="study.php?type=개발">개발 스터디</a>
                        <a href="study.php?type=어학">어학 스터디</a>
                        <a href="study.php?type=자격증">자격증 스터디</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="jobs.php">채용 공고</a>
                    <div class="dropdown-menu">
                        <a href="jobs.php?type=신입">신입 채용</a>
                        <a href="jobs.php?type=경력">경력 채용</a>
                        <a href="jobs.php?type=인턴">인턴 채용</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="board.php" class="active">익명 게시판</a>
                    <div class="dropdown-menu">
                        <a href="board.php?category=자유">자유 게시판</a>
                        <a href="board.php?category=질문">질문 게시판</a>
                        <a href="board.php?category=정보">정보 공유</a>
                    </div>
                </div>
                <div class="nav-item">
                    <a href="notice.php">공지사항</a>
                </div>
                <div class="nav-item dropdown">
                    <a href="jobseeker.php">취준생 공간</a>
                    <div class="dropdown-menu">
                        <a href="jobseeker.php#resume">이력서 작성</a>
                        <a href="jobseeker.php#portfolio">포트폴리오</a>
                        <a href="jobseeker.php#interview">면접 준비</a>
                    </div>
                </div>
                
                <div class="user-info">
                    <span class="welcome-msg"><?php echo htmlspecialchars($user['username']); ?>님</span>
                    
                    <a href="logout.php" class="btn-logout">로그아웃</a>
                </div>
            </nav>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>
<main class="board-container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"> 게시글이 성공적으로 작성되었습니다!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-info">게시글이 삭제되었습니다.</div>
        <?php endif; ?>

        <?php if (isset($_GET['edited'])): ?>
            <div class="alert alert-success">게시글이 수정되었습니다!</div>
        <?php endif; ?>

        <?php if (!isset($_GET['view'])): ?>
            <div class="category-tabs">
                <?php foreach ($categories as $cat): ?>
                    <button class="category-tab <?php echo $category == $cat ? 'active' : ''; ?>" 
                            onclick="location.href='board.php?category=<?php echo urlencode($cat); ?>'">
                        <?php echo $cat; ?>
                        <span class="category-count"><?php echo $category_counts[$cat]; ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="board-header">
                <h2><?php echo htmlspecialchars($category); ?> 게시글 (<?php echo $total_posts; ?>)</h2>
                <button class="btn-write" onclick="openWriteModal()">글쓰기</button>
            </div>

            <?php if ($posts->num_rows > 0): ?>
                <div class="posts-grid">
                    <?php while ($post = $posts->fetch_assoc()): ?>
                        <div class="post-card" onclick="location.href='board.php?category=<?php echo urlencode($category); ?>&view=<?php echo $post['id']; ?>&page=<?php echo $page; ?>'">
                            <?php if ($post['user_id'] == $user['id']): ?>
                                <div class="post-actions" onclick="event.stopPropagation()">
                                    <button class="action-btn btn-edit" onclick="openEditModal(<?php echo $post['id']; ?>, '<?php echo addslashes(htmlspecialchars($post['title'])); ?>', '<?php echo addslashes(htmlspecialchars($post['content'])); ?>', '<?php echo addslashes($post['category']); ?>')">수정</button>
                                    <button class="action-btn btn-delete" onclick="if(confirm('정말로 삭제하시겠습니까?')) location.href='board.php?category=<?php echo urlencode($category); ?>&delete_post=<?php echo $post['id']; ?>&page=<?php echo $page; ?>'">삭제</button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-card-header">
                                <div class="post-badges">
                                    <span class="badge anonymous">익명</span>
                                    <span class="badge category"><?php echo htmlspecialchars($post['category']); ?></span>
                                </div>
                                <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                <div class="post-meta">
                                    <span>익명<?php echo substr($post['user_id'], -2); ?></span>
                                    <span><?php echo date('Y.m.d H:i', strtotime($post['created_at'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="post-content-preview">
                                <?php 
                                $preview = mb_substr(strip_tags($post['content']), 0, 100);
                                echo htmlspecialchars($preview);
                                if (mb_strlen($post['content']) > 100) echo '...';
                                ?>
                            </div>
                            
                            <div class="post-stats" onclick="event.stopPropagation()">
                                <button class="like-btn <?php echo $post['user_liked'] ? 'liked' : ''; ?>" 
                                        data-post-id="<?php echo $post['id']; ?>"
                                        onclick="toggleLike(this, event)">
                                    <span class="like-icon"><?php echo $post['user_liked'] ? '❤️' : '🤍'; ?></span>
                                    <span class="like-count"><?php echo $post['likes']; ?></span>
                                </button>
                                <span class="stat-item">👁️ <?php echo $post['views']; ?></span>
                                <span class="stat-item">💬 <?php echo $post['comment_count']; ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="board.php?category=<?php echo urlencode($category); ?>&page=<?php echo $page - 1; ?>" class="page-btn">‹ 이전</a>
                        <?php endif; ?>
                        
                        <?php 
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                            <a href="board.php?category=<?php echo urlencode($category); ?>&page=<?php echo $i; ?>" 
                               class="page-btn <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="board.php?category=<?php echo urlencode($category); ?>&page=<?php echo $page + 1; ?>" class="page-btn">다음 ›</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon"></div>
                    <h3>아직 작성된 게시글이 없습니다</h3>
                    <p>첫 번째 게시글을 작성해보세요!</p>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <?php if ($viewing_post): ?>
                <div class="post-detail">
                    <div class="post-detail-header">
                        <div class="post-badges">
                            <span class="badge anonymous">익명</span>
                            <span class="badge category"><?php echo htmlspecialchars($viewing_post['category']); ?></span>
                        </div>
                        <h1 class="post-detail-title"><?php echo htmlspecialchars($viewing_post['title']); ?></h1>
                        <div class="post-meta">
                            <span>👤 익명<?php echo substr($viewing_post['user_id'], -2); ?></span>
                            <span>📅 <?php echo date('Y.m.d H:i', strtotime($viewing_post['created_at'])); ?></span>
                            <span>👁️ <?php echo $viewing_post['views']; ?></span>
                            <span>❤️ <?php echo $viewing_post['likes']; ?></span>
                        </div>
                    </div>
                    
                    <div class="post-detail-content">
                        <?php echo nl2br(htmlspecialchars($viewing_post['content'])); ?>
                    </div>
                    
                    <div class="detail-actions">
                        <button class="btn-write like-detail-btn <?php echo $viewing_post['user_liked'] ? 'liked' : ''; ?>" 
                                style="background: <?php echo $viewing_post['user_liked'] ? '#ef4444' : '#10b981'; ?>"
                                data-post-id="<?php echo $viewing_post['id']; ?>"
                                onclick="toggleDetailLike(this)">
                            <span class="like-icon"><?php echo $viewing_post['user_liked'] ? '❤️' : '🤍'; ?></span>
                            <span class="like-text"><?php echo $viewing_post['user_liked'] ? '좋아요 취소' : '좋아요'; ?></span>
                        </button>
                        
                        <?php if ($viewing_post['user_id'] == $user['id']): ?>
                            <button class="btn-write" style="background: #3b82f6;" onclick="openEditModal(<?php echo $viewing_post['id']; ?>, '<?php echo addslashes(htmlspecialchars($viewing_post['title'])); ?>', '<?php echo addslashes(htmlspecialchars($viewing_post['content'])); ?>', '<?php echo addslashes($viewing_post['category']); ?>')">✏️ 수정</button>
                            <button class="btn-write" style="background: #ef4444;" onclick="if(confirm('정말로 삭제하시겠습니까?')) location.href='board.php?category=<?php echo urlencode($category); ?>&delete_post=<?php echo $viewing_post['id']; ?>&page=<?php echo $page; ?>'">🗑️ 삭제하기</button>
                        <?php endif; ?>
                        
                        <button class="btn-write" style="background: #6b7280;" onclick="location.href='board.php?category=<?php echo urlencode($category); ?>&page=<?php echo $page; ?>'">← 목록으로</button>
                    </div>
                </div>

                <div class="comments-section" id="comments">
                    <h3>💬 댓글 <?php echo $comments ? $comments->num_rows : 0; ?>개</h3>
                    
                    <div class="comment-form">
                        <form method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $viewing_post['id']; ?>">
                            <textarea name="comment_content" placeholder="댓글을 입력하세요..." required></textarea>
                            <button type="submit" name="add_comment" class="btn-comment">댓글 작성</button>
                        </form>
                    </div>

                    <?php if ($comments && $comments->num_rows > 0): ?>
                        <?php while ($comment = $comments->fetch_assoc()): ?>
                            <div class="comment-item">
                                <div class="comment-header">
                                    <div>
                                        <span class="comment-author">익명<?php echo substr($comment['user_id'], -2); ?></span>
                                        <span class="comment-time"><?php echo date('Y.m.d H:i', strtotime($comment['created_at'])); ?></span>
                                        <?php if ($comment['user_id'] == $user['id']): ?>
                                            <a href="board.php?category=<?php echo urlencode($category); ?>&delete_comment=<?php echo $comment['id']; ?>&post_id=<?php echo $viewing_post['id']; ?>&page=<?php echo $page; ?>" 
                                               class="comment-delete"
                                               onclick="return confirm('댓글을 삭제하시겠습니까?')">삭제</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="comment-content"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>아직 댓글이 없습니다. 첫 댓글을 작성해보세요!</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <div id="writeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>새 게시글 작성</h2>
                <button class="modal-close" onclick="closeWriteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="form-group">
                        <label>카테고리</label>
                        <select name="category" required>
                            <option value="자유" <?php echo $category == '자유' ? 'selected' : ''; ?>>자유</option>
                            <option value="질문" <?php echo $category == '질문' ? 'selected' : ''; ?>>질문</option>
                            <option value="정보" <?php echo $category == '정보' ? 'selected' : ''; ?>>정보</option>
                            <option value="고민" <?php echo $category == '고민' ? 'selected' : ''; ?>>고민</option>
                            <option value="후기" <?php echo $category == '후기' ? 'selected' : ''; ?>>후기</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>제목</label>
                        <input type="text" name="title" placeholder="제목을 입력하세요" required maxlength="200">
                    </div>
                    
                    <div class="form-group">
                        <label>내용</label>
                        <textarea name="content" placeholder="내용을 입력하세요" required></textarea>
                    </div>
                    
                    <button type="submit" name="create_post" class="btn-submit">게시글 등록</button>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>게시글 수정</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="post_id" id="edit_post_id">
                    
                    <div class="form-group">
                        <label>카테고리</label>
                        <select name="category" id="edit_category" required>
                            <option value="자유">자유</option>
                            <option value="질문">질문</option>
                            <option value="정보">정보</option>
                            <option value="고민">고민</option>
                            <option value="후기">후기</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>제목</label>
                        <input type="text" name="title" id="edit_title" placeholder="제목을 입력하세요" required maxlength="200">
                    </div>
                    
                    <div class="form-group">
                        <label>내용</label>
                        <textarea name="content" id="edit_content" placeholder="내용을 입력하세요" required></textarea>
                    </div>
                    
                    <button type="submit" name="edit_post" class="btn-submit">수정 완료</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Company Portal</h3>
                    <p>직장인과 취업준비생을 위한<br>커뮤니티 포털</p>
                </div>
                <div class="footer-section">
                    <h4>바로가기</h4>
                    <a href="notice.php">공지사항</a>
                    <a href="#">이용약관</a>
                    <a href="#">개인정보처리방침</a>
                </div>
                <div class="footer-section">
                    <h4>문의하기</h4>
                    <p>📧 nosu0320@naver.com</p>
                    <p>📞010-2681-9540</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 Company Community Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        function toggleLike(button, event) {
            event.stopPropagation();
            event.preventDefault();
            
            const postId = button.dataset.postId;
            const likeIcon = button.querySelector('.like-icon');
            const likeCount = button.querySelector('.like-count');
            
            button.disabled = true;
            
            fetch('board.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `toggle_like=1&post_id=${postId}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.liked) {
                        button.classList.add('liked');
                        likeIcon.textContent = '❤️';
                    } else {
                        button.classList.remove('liked');
                        likeIcon.textContent = '🤍';
                    }
                    likeCount.textContent = data.likes;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                button.disabled = false;
            });
        }

        function toggleDetailLike(button) {
            const postId = button.dataset.postId;
            const likeIcon = button.querySelector('.like-icon');
            const likeText = button.querySelector('.like-text');
            
            button.disabled = true;
            
            fetch('board.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `toggle_like=1&post_id=${postId}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.liked) {
                        button.classList.add('liked');
                        button.style.background = '#ef4444';
                        likeIcon.textContent = '❤️';
                        likeText.textContent = '좋아요 취소';
                    } else {
                        button.classList.remove('liked');
                        button.style.background = '#10b981';
                        likeIcon.textContent = '🤍';
                        likeText.textContent = '좋아요';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                button.disabled = false;
            });
        }

        function openWriteModal() {
            document.getElementById('writeModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeWriteModal() {
            document.getElementById('writeModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(postId, title, content, category) {
            document.getElementById('edit_post_id').value = postId;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_content').value = content;
            document.getElementById('edit_category').value = category;
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('writeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeWriteModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeWriteModal();
                closeEditModal();
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>