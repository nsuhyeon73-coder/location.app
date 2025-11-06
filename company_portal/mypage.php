<?php
require_once 'db.php';
requireLogin();

$user = getCurrentUser();
$success = '';
$error = '';

// 프로필 정보 업데이트
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email']);
    $user_type = cleanInput($_POST['user_type']);
    
    if (empty($username) || empty($email)) {
        $error = '이름과 이메일은 필수 항목입니다.';
    } else {
        $conn = getDBConnection();
        
        // 이메일 중복 체크 (자신의 이메일 제외)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = '이미 사용 중인 이메일입니다.';
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, user_type = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $email, $user_type, $user['id']);
            
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['user_type'] = $user_type;
                $user = getCurrentUser();
                $success = '✅ 프로필 정보가 성공적으로 업데이트되었습니다!';
            } else {
                $error = '프로필 업데이트 중 오류가 발생했습니다.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

// 비밀번호 변경
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = '모든 비밀번호 항목을 입력해주세요.';
    } elseif ($new_password !== $confirm_password) {
        $error = '새 비밀번호가 일치하지 않습니다.';
    } elseif (strlen($new_password) < 6) {
        $error = '새 비밀번호는 최소 6자 이상이어야 합니다.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (password_verify($current_password, $row['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user['id']);
            
            if ($stmt->execute()) {
                $success = '✅ 비밀번호가 성공적으로 변경되었습니다!';
            } else {
                $error = '비밀번호 변경 중 오류가 발생했습니다.';
            }
        } else {
            $error = '현재 비밀번호가 일치하지 않습니다.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// 회원 탈퇴
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $confirm_password = $_POST['delete_password'];
    
    if (empty($confirm_password)) {
        $error = '비밀번호를 입력해주세요.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (password_verify($confirm_password, $row['password'])) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                session_destroy();
                header('Location: login.php?deleted=1');
                exit();
            } else {
                $error = '회원 탈퇴 중 오류가 발생했습니다.';
            }
        } else {
            $error = '비밀번호가 일치하지 않습니다.';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내 정보 - Company Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .mypage-container {
            max-width: 800px;
            margin: 2rem auto;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .profile-header h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .profile-header p {
            opacity: 0.95;
            font-size: 1.1rem;
        }

        .section-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .section-card h3 {
            color: #1f2937;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group input:disabled {
            background: #f3f4f6;
            cursor: not-allowed;
        }

        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .btn-danger:hover {
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .info-box {
            background: #fffbeb;
            border: 2px solid #fbbf24;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-box p {
            margin: 0;
            color: #92400e;
            line-height: 1.6;
        }

        .danger-zone {
            background: #fef2f2;
            border: 2px solid #fca5a5;
            border-radius: 15px;
            padding: 2rem;
        }

        .danger-zone h3 {
            color: #dc2626;
            border-bottom-color: #fca5a5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 2px solid #e5e7eb;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-card .label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .mypage-container {
                padding: 1rem;
            }

            .section-card {
                padding: 1.5rem;
            }

            .profile-header {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1>Company</h1>
            </div>
            <nav class="nav" id="nav">
                <a href="index.php">홈</a>
                <a href="study.php">스터디</a>
                <a href="jobs.php">채용 공고</a>
                <a href="board.php">익명 게시판</a>
                <a href="notice.php">공지사항</a>
                <a href="jobseeker.php">취준생 공간</a>
                
                <div class="user-info">
                    <a href="mypage.php" style="color: #667eea; text-decoration: none; font-weight: 600;">내 정보</a>
                    <span class="welcome-msg"><?php echo htmlspecialchars($user['username']); ?>님</span>
                    <span class="user-badge"><?php echo htmlspecialchars($user['user_type']); ?></span>
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

    <section class="hero">
        <div class="container">
            <h1>내 정보</h1>
            <p>프로필 정보를 관리하고 계정 설정을 변경하세요</p>
        </div>
    </section>

    <main class="main-content">
        <div class="container mypage-container">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- 프로필 헤더 -->
            <div class="profile-header">
                <div class="profile-avatar">👤</div>
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p><?php echo htmlspecialchars($user['user_type']); ?> • <?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <!-- 활동 통계 -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number">0</div>
                    <div class="label">작성한 글</div>
                </div>
                <div class="stat-card">
                    <div class="number">0</div>
                    <div class="label">댓글</div>
                </div>
                <div class="stat-card">
                    <div class="number">0</div>
                    <div class="label">참여 스터디</div>
                </div>
            </div>

            <!-- 프로필 정보 수정 -->
            <div class="section-card">
                <h3>프로필 정보 수정</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">이름</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="user_type">사용자 유형</label>
                        <select id="user_type" name="user_type" required>
                            <option value="직장인" <?php echo $user['user_type'] == '직장인' ? 'selected' : ''; ?>>직장인</option>
                            <option value="취준생" <?php echo $user['user_type'] == '취준생' ? 'selected' : ''; ?>>취업준비생</option>
                            <option value="학생" <?php echo $user['user_type'] == '학생' ? 'selected' : ''; ?>>학생</option>
                        </select>
                    </div>

                    <button type="submit" name="update_profile" class="btn-primary">프로필 저장</button>
                </form>
            </div>

            <!-- 비밀번호 변경 -->
            <div class="section-card">
                <h3>비밀번호 변경</h3>
                <div class="info-box">
                    <p>비밀번호는 최소 6자 이상이어야 합니다.</p>
                </div>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">현재 비밀번호</label>
                        <input type="password" id="current_password" name="current_password" placeholder="현재 비밀번호를 입력하세요" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">새 비밀번호</label>
                        <input type="password" id="new_password" name="new_password" placeholder="새 비밀번호 (최소 6자)" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">새 비밀번호 확인</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="새 비밀번호를 다시 입력하세요" required>
                    </div>

                    <button type="submit" name="change_password" class="btn-primary">🔑 비밀번호 변경</button>
                </form>
            </div>

            <!-- 회원 탈퇴 -->
            <div class="section-card danger-zone">
                <h3> 회원 탈퇴</h3>
                <div class="info-box" style="background: #fef2f2; border-color: #fca5a5;">
                    <p><strong>주의:</strong> 회원 탈퇴 시 모든 데이터가 삭제되며 복구할 수 없습니다.<br>
                    작성한 게시글, 댓글, 참여한 스터디 등 모든 정보가 영구적으로 삭제됩니다.</p>
                </div>
                <form method="POST" action="" onsubmit="return confirm('정말로 회원 탈퇴하시겠습니까? 이 작업은 되돌릴 수 없습니다.');">
                    <div class="form-group">
                        <label for="delete_password">비밀번호 확인</label>
                        <input type="password" id="delete_password" name="delete_password" placeholder="비밀번호를 입력하세요" required>
                    </div>

                    <button type="submit" name="delete_account" class="btn-primary btn-danger">🗑️ 회원 탈퇴</button>
                </form>
            </div>
        </div>
    </main>

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
                    <p>📞 010=2681-9540</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 Company Community Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>