<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company_portal";

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Portal 설치</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            line-height: 1.8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 700px;
            width: 100%;
        }

        h1 {
            color: #667eea;
            margin-bottom: 2rem;
            font-size: 2rem;
            text-align: center;
        }

        .step {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }

        .step.success {
            border-left-color: #10b981;
            background: #d1fae5;
        }

        .step.error {
            border-left-color: #ef4444;
            background: #fee2e2;
        }

        .step-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .step-content {
            color: #4b5563;
        }

        .credentials {
            background: #fffbeb;
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px solid #fbbf24;
            margin: 2rem 0;
        }

        .credentials h3 {
            color: #92400e;
            margin-bottom: 1rem;
        }

        .credential-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .credential-label {
            font-weight: 600;
            color: #78350f;
            min-width: 80px;
        }

        .credential-value {
            color: #1f2937;
            font-family: 'Courier New', monospace;
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
        }

        .btn-container {
            text-align: center;
            margin-top: 2rem;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 3rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .icon {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Company Portal 설치</h1>
        
        <?php
        try {
            $conn = new mysqli($servername, $username, $password);
            
            if ($conn->connect_error) {
                throw new Exception("연결 실패: " . $conn->connect_error);
            }
            
            // 데이터베이스 생성
            echo '<div class="step">';
            echo '<div class="step-title">📦 데이터베이스 생성 중...</div>';
            $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if ($conn->query($sql) === TRUE) {
                echo '<div class="step-content">✅ 데이터베이스가 성공적으로 생성되었습니다.</div>';
                echo '</div>';
            } else {
                throw new Exception("데이터베이스 생성 실패");
            }
            
            $conn->select_db($dbname);
            
            // 사용자 테이블
            echo '<div class="step">';
            echo '<div class="step-title">👥 사용자 테이블 생성 중...</div>';
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                user_type ENUM('직장인', '취준생', '학생', '관리자') DEFAULT '직장인',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($conn->query($sql) === TRUE) {
                echo '<div class="step-content">✅ 사용자 테이블이 생성되었습니다.</div>';
                echo '</div>';
            } else {
                throw new Exception("사용자 테이블 생성 실패");
            }
            
            // 게시글 테이블
            echo '<div class="step">';s
            echo '<div class="step-title"> 게시글 테이블 생성 중...</div>';
            $sql = "CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(200) NOT NULL,
                content TEXT NOT NULL,
                category VARCHAR(50) DEFAULT '자유',
                views INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_category (category),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($conn->query($sql) === TRUE) {
                echo '<div class="step-content">✅ 게시글 테이블이 생성되었습니다.</div>';
                echo '</div>';
            }
            
            // 댓글 테이블
            echo '<div class="step">';
            echo '<div class="step-title">댓글 테이블 생성 중...</div>';
            $sql = "CREATE TABLE IF NOT EXISTS comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_post_id (post_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($conn->query($sql) === TRUE) {
                echo '<div class="step-content">✅ 댓글 테이블이 생성되었습니다.</div>';
                echo '</div>';
            }
            
            // 좋아요 테이블
            echo '<div class="step">';
            echo '<div class="step-title">좋아요 테이블 생성 중...</div>';
            $sql = "CREATE TABLE IF NOT EXISTS likes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_like (post_id, user_id),
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($conn->query($sql) === TRUE) {
                echo '<div class="step-content">✅ 좋아요 테이블이 생성되었습니다.</div>';
                echo '</div>';
            }
            
            // 관리자 계정 생성
            echo '<div class="step">';
            echo '<div class="step-title">👨관리자 계정 생성 중...</div>';
            $admin_username = "관리자";
            $admin_email = "admin@company.com";
            $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $admin_email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, '관리자')");
                $stmt->bind_param("sss", $admin_username, $admin_email, $admin_password);
                if ($stmt->execute()) {
                    echo '<div class="step-content">✅ 관리자 계정이 생성되었습니다.</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="step-content">관리자 계정이 이미 존재합니다.</div>';
                echo '</div>';
            }
            
            // 관리자 계정 정보
            echo '<div class="credentials">';
            echo '<h3>관리자 계정 정보</h3>';
            echo '<div class="credential-item">';
            echo '<span class="credential-label">이메일:</span>';
            echo '<span class="credential-value">admin@company.com</span>';
            echo '</div>';
            echo '<div class="credential-item">';
            echo '<span class="credential-label">비밀번호:</span>';
            echo '<span class="credential-value">admin123</span>';
            echo '</div>';
            echo '<p style="margin-top: 1rem; color: #92400e; font-size: 0.9rem;">보안을 위해 로그인 후 반드시 비밀번호를 변경하세요.</p>';
            echo '</div>';
            
            echo '<div class="step success">';
            echo '<div class="step-title">설치 완료!</div>';
            echo '<div class="step-content">Company Portal이 성공적으로 설치되었습니다. 로그인하여 서비스를 이용하세요.</div>';
            echo '</div>';
            
            echo '<div class="btn-container">';
            echo '<a href="login.php" class="btn"><span class="icon">🚀</span>로그인 페이지로 이동</a>';
            echo '</div>';
            
            $conn->close();
            
        } catch (Exception $e) {
            echo '<div class="step error">';
            echo '<div class="step-title">오류 발생</div>';
            echo '<div class="step-content">' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>