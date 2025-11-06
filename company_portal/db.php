<?php
// 데이터베이스 연결 설정
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'company_portal');

/**
 * 데이터베이스 연결 함수
 * @return mysqli 데이터베이스 연결 객체
 */
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("DB 연결 실패: " . $conn->connect_error);
            die("데이터베이스 연결에 실패했습니다. 관리자에게 문의하세요.");
        }
        
        // UTF-8 문자셋 설정
        $conn->set_charset("utf8mb4");
        return $conn;
        
    } catch (Exception $e) {
        error_log("DB 연결 오류: " . $e->getMessage());
        die("데이터베이스 연결 오류가 발생했습니다.");
    }
}

/**
 * XSS 공격 방지를 위한 입력값 정리
 * @param string $data 입력 데이터
 * @return string 정리된 데이터
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * 세션 시작 함수
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * 로그인 상태 확인
 * @return bool 로그인 여부
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

/**
 * 로그인 필수 체크 (로그인하지 않은 경우 로그인 페이지로 리다이렉트)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * 현재 로그인한 사용자 정보 가져오기
 * @return array 사용자 정보 배열
 */
function getCurrentUser() {
    startSession();
    if (isLoggedIn()) {
        return [
            'id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            'username' => isset($_SESSION['username']) ? $_SESSION['username'] : '게스트',
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '',
            'user_type' => isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '방문자'
        ];
    }
    return [
        'id' => null,
        'username' => '게스트',
        'email' => '',
        'user_type' => '방문자'
    ];
}

/**
 * 관리자 권한 체크
 * @return bool 관리자 여부
 */
function isAdmin() {
    $user = getCurrentUser();
    return $user['user_type'] === '관리자';
}

/**
 * 관리자 권한 필수 체크
 */
function requireAdmin() {
    if (!isAdmin()) {
        die("접근 권한이 없습니다.");
    }
}
?>