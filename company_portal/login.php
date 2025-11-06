<?php
session_start();
require_once 'db.php';

// 이미 로그인되어 있으면 메인으로
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// 로그인 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = '이메일과 비밀번호를 입력해주세요.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, username, email, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
                
                header('Location: index.php');
                exit();
            } else {
                $error = '이메일 또는 비밀번호가 일치하지 않습니다.';
            }
        } else {
            $error = '이메일 또는 비밀번호가 일치하지 않습니다.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// 회원가입 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = cleanInput($_POST['user_type']);
    
    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error = '모든 항목을 입력해주세요.';
    } elseif ($password !== $confirm_password) {
        $error = '비밀번호가 일치하지 않습니다.';
    } elseif (strlen($password) < 6) {
        $error = '비밀번호는 최소 6자 이상이어야 합니다.';
    } elseif (!preg_match('/^01[0-9]-[0-9]{3,4}-[0-9]{4}$/', $phone)) {
        $error = '전화번호 형식이 올바르지 않습니다. (예: 010-1234-5678)';
    } else {
        $conn = getDBConnection();
        
        // 이메일 중복 체크
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = '이미 등록된 이메일입니다.';
        } else {
            // 전화번호 중복 체크
            $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = '이미 등록된 전화번호입니다.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password, user_type) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $email, $phone, $hashed_password, $user_type);
                
                if ($stmt->execute()) {
                    $success = '회원가입이 완료되었습니다! 로그인해주세요.';
                } else {
                    $error = '회원가입 중 오류가 발생했습니다: ' . $stmt->error;
                }
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

// 이메일 찾기 (이름 + 전화번호)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['find_email'])) {
    $username = cleanInput($_POST['find_username']);
    $phone = cleanInput($_POST['find_phone']);
    
    if (empty($username) || empty($phone)) {
        $error = '이름과 전화번호를 모두 입력해주세요.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT email FROM users WHERE username = ? AND phone = ?");
        $stmt->bind_param("ss", $username, $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $email = $user['email'];
            $masked_email = substr($email, 0, 3) . '***' . substr($email, strpos($email, '@'));
            $success = "회원님의 이메일은 <strong>{$masked_email}</strong> 입니다.";
        } else {
            $error = '일치하는 회원정보가 없습니다.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// 비밀번호 재설정
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $email = cleanInput($_POST['reset_email']);
    $username = cleanInput($_POST['reset_username']);
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    
    if (empty($email) || empty($username) || empty($new_password) || empty($confirm_new_password)) {
        $error = '모든 항목을 입력해주세요.';
    } elseif ($new_password !== $confirm_new_password) {
        $error = '새 비밀번호가 일치하지 않습니다.';
    } elseif (strlen($new_password) < 6) {
        $error = '비밀번호는 최소 6자 이상이어야 합니다.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? AND username = ?");
            $stmt->bind_param("sss", $hashed_password, $email, $username);
            
            if ($stmt->execute()) {
                $success = '비밀번호가 성공적으로 변경되었습니다! 로그인해주세요.';
            } else {
                $error = '비밀번호 변경 중 오류가 발생했습니다.';
            }
        } else {
            $error = '일치하는 회원정보가 없습니다.';
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
    <title>로그인 - Company Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans KR', sans-serif;
            background: linear-gradient(135deg,rgb(230, 230, 230) 0%,rgb(249, 255, 218) 100%);
            min-height: 100vh;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .auth-box {
            background: #fff;
            border-radius: 20px;
            padding: 3rem;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            color:rgb(0, 0, 0);
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .auth-header p {
            color:rgb(141, 143, 146);
            font-size: 0.8rem;
        }
        
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .tab {
            flex: 1;
            padding: 1rem;
            border: none;
            background: none;
            color: #6b7280;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }
        
        .tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }
        
        .form-content {
            display: none;
        }
        
        .form-content.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .password-hint {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-error {
            background-color: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background-color: #efe;
            color: #080;
            border: 1px solid #cfc;
            transition: opacity 0.3s ease-out;
        }

        .find-links {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        .find-links a {
            color: #3b82f6;
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .find-links a:hover {
            text-decoration: underline;
        }

        .find-links span {
            color: #d1d5db;
        }

        @media (max-width: 480px) {
            .auth-box {
                padding: 2rem;
            }

            .auth-header h1 {
                font-size: 1.5rem;
            }

            .tab {
                font-size: 0.8rem;
                padding: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Company Portal</h1>
                <p>직장인과 취업준비생을 위한 커뮤니티</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" id="successAlert"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="tabs">
                <button class="tab active" onclick="switchTab('login')">로그인</button>
                <button class="tab" onclick="switchTab('register')">회원가입</button>
            </div>
            
            <!-- 로그인 폼 -->
            <div id="login-form" class="form-content active">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="login-email">이메일</label>
                        <input type="email" id="login-email" name="email" required placeholder="example@company.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password">비밀번호</label>
                        <input type="password" id="login-password" name="password" required placeholder="비밀번호를 입력하세요">
                    </div>
                    
                    <button type="submit" name="login" class="btn-submit">로그인</button>
                    
                    <div class="find-links">
                        <a href="#" onclick="switchTab('find-email'); return false;">이메일 찾기</a>
                        <span>|</span>
                        <a href="#" onclick="switchTab('reset-password'); return false;">비밀번호 찾기</a>
                    </div>
                </form>
            </div>
            
            <!-- 회원가입 폼 -->
            <div id="register-form" class="form-content">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">사용자 이름</label>
                        <input type="text" id="username" name="username" required placeholder="홍길동">
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email">이메일</label>
                        <input type="email" id="register-email" name="email" required placeholder="example@company.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">전화번호</label>
                        <input type="tel" id="phone" name="phone" required placeholder="010-1234-5678" pattern="01[0-9]-[0-9]{3,4}-[0-9]{4}">
                        <p class="password-hint">※ 하이픈(-)을 포함하여 입력하세요 (예: 010-1234-5678)</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="user-type">사용자 유형</label>
                        <select id="user-type" name="user_type" required>
                            <option value="직장인">직장인</option>
                            <option value="취준생">취업준비생</option>
                            <option value="학생">학생</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password">비밀번호</label>
                        <input type="password" id="register-password" name="password" required placeholder="비밀번호 (최소 6자)">
                        <p class="password-hint">※ 최소 6자 이상 입력해주세요</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">비밀번호 확인</label>
                        <input type="password" id="confirm-password" name="confirm_password" required placeholder="비밀번호를 다시 입력하세요">
                    </div>
                    
                    <button type="submit" name="register" class="btn-submit">회원가입</button>
                </form>
            </div>
            
            <!-- 이메일 찾기 폼 -->
            <div id="find-email-form" class="form-content">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="find-username">👤 사용자 이름</label>
                        <input type="text" id="find-username" name="find_username" required placeholder="홍길동">
                        <p class="password-hint">※ 가입시 입력한 이름을 입력하세요</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="find-phone">📱 전화번호</label>
                        <input type="tel" id="find-phone" name="find_phone" required placeholder="010-1234-5678" pattern="01[0-9]-[0-9]{3,4}-[0-9]{4}">
                        <p class="password-hint">※ 가입시 입력한 전화번호를 입력하세요</p>
                    </div>
                    
                    <button type="submit" name="find_email" class="btn-submit">이메일 찾기</button>
                    
                    <div class="find-links">
                        <a href="#" onclick="switchTab('login'); return false;">로그인으로 돌아가기</a>
                    </div>
                </form>
            </div>
            
            <!-- 비밀번호 재설정 폼 -->
            <div id="reset-password-form" class="form-content">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="reset-email">📧 이메일</label>
                        <input type="email" id="reset-email" name="reset_email" required placeholder="example@company.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="reset-username">👤 사용자 이름</label>
                        <input type="text" id="reset-username" name="reset_username" required placeholder="홍길동">
                        <p class="password-hint">※ 가입시 입력한 이름을 입력하세요</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="new-password">🔒 새 비밀번호</label>
                        <input type="password" id="new-password" name="new_password" required placeholder="새 비밀번호 (최소 6자)">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-new-password">🔒 새 비밀번호 확인</label>
                        <input type="password" id="confirm-new-password" name="confirm_new_password" required placeholder="새 비밀번호를 다시 입력하세요">
                    </div>
                    
                    <button type="submit" name="reset_password" class="btn-submit">비밀번호 변경</button>
                    
                    <div class="find-links">
                        <a href="#" onclick="switchTab('login'); return false;">로그인으로 돌아가기</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const findEmailForm = document.getElementById('find-email-form');
            const resetPasswordForm = document.getElementById('reset-password-form');
            const tabs = document.querySelectorAll('.tab');
            
            // 모든 폼 숨기기
            loginForm.classList.remove('active');
            registerForm.classList.remove('active');
            findEmailForm.classList.remove('active');
            resetPasswordForm.classList.remove('active');
            
            // 모든 탭 비활성화
            tabs.forEach(t => t.classList.remove('active'));
            
            // 선택된 탭 활성화
            if (tab === 'login') {
                loginForm.classList.add('active');
                tabs[0].classList.add('active');
            } else if (tab === 'register') {
                registerForm.classList.add('active');
                tabs[1].classList.add('active');
            } else if (tab === 'find-email') {
                findEmailForm.classList.add('active');
            } else if (tab === 'reset-password') {
                resetPasswordForm.classList.add('active');
            }
        }
        
        // 전화번호 자동 하이픈 추가
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                let formattedValue = '';
                
                if (value.length <= 3) {
                    formattedValue = value;
                } else if (value.length <= 7) {
                    formattedValue = value.slice(0, 3) + '-' + value.slice(3);
                } else if (value.length <= 11) {
                    formattedValue = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7);
                } else {
                    formattedValue = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
                }
                
                e.target.value = formattedValue;
            });
        });
        
        <?php if ($success && (isset($_POST['register']) || isset($_POST['reset_password']))): ?>
        // 회원가입 또는 비밀번호 변경 성공시 2초 후 로그인 탭으로 자동 전환
        setTimeout(() => {
            switchTab('login');
        }, 2000);
        <?php endif; ?>
        
        <?php if ($success): ?>
        // 성공 메시지 7초 후 자동 제거
        setTimeout(() => {
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 300);
            }
        }, 7000);
        <?php endif; ?>
    </script>
</body>
</html>