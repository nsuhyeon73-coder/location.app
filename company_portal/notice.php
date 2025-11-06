<?php
require_once 'db.php';
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>공지사항 - Company Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .notice-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .notice-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .notice-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .notice-header {
            padding: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .notice-header.important {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .notice-header.normal {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .notice-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .notice-body {
            padding: 1.5rem;
        }

        .notice-card {
            padding: 1.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .notice-card:hover {
            border-color: #6366f1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        }

        .notice-card:last-child {
            margin-bottom: 0;
        }

        .notice-card.urgent {
            border-left: 4px solid #ef4444;
            background: #fef2f2;
        }

        .notice-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .badge-urgent {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-event {
            background: #dcfce7;
            color: #166534;
        }

        .badge-update {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-info {
            background: #fef3c7;
            color: #92400e;
        }

        .notice-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .notice-description {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            line-height: 1.6;
        }

        .notice-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.85rem;
            color: #9ca3af;
        }

        /* 모달 스타일 */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 2rem;
            border-bottom: 2px solid #e5e7eb;
            position: relative;
        }

        .modal-header.urgent {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-bottom-color: #fca5a5;
        }

        .modal-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(0, 0, 0, 0.1);
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #6b7280;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: rgba(0, 0, 0, 0.2);
            transform: rotate(90deg);
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-right: 3rem;
        }

        .modal-meta {
            display: flex;
            gap: 1.5rem;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-body p,
        .modal-body strong,
        .modal-body br {
            line-height: 1.8;
            color: #374151;
            font-size: 1rem;
        }

        .modal-footer {
            padding: 1.5rem 2rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .btn-confirm {
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        @media (max-width: 968px) {
            .notice-grid {
                grid-template-columns: 1fr;
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
                    <a href="board.php">익명 게시판</a>
                    <div class="dropdown-menu">
                        <a href="board.php?category=자유">자유 게시판</a>
                        <a href="board.php?category=질문">질문 게시판</a>
                        <a href="board.php?category=정보">정보 공유</a>
                    </div>
                </div>
                <div class="nav-item">
                    <a href="notice.php" class="active">공지사항</a>
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
<main class="notice-container">
        <div class="notice-grid">
            <!-- 일반 공지사항 (왼쪽) -->
            <div class="notice-section">
                <div class="notice-header normal">
                    <span style="font-size: 1.5rem;">
                    <h2>일반 공지사항</h2>
                </div>
                <div class="notice-body">
                    <div class="notice-card" onclick="openNoticeModal('이벤트', '회원 10,000명 돌파 기념 이벤트', 'Company Portal 회원 10,000명 돌파를 기념하여 추첨을 통해 50명에게 스타벅스 기프티콘을 드립니다!<br><br><strong>이벤트 기간:</strong> 2025.01.15 ~ 2025.01.31<br><strong>참여 방법:</strong> 공지사항 댓글로 응원 메시지 남기기<br><strong>당첨자 발표:</strong> 2025.02.05<br><br>많은 참여 부탁드립니다!', '2025.01.15', '5,678회', '234건', false)">
                        <span class="notice-badge badge-event">이벤트</span>
                        <h4 class="notice-title">회원 10,000명 돌파 기념 이벤트</h4>
                        <p class="notice-description">Company Portal 회원 10,000명 돌파를 기념하여 추첨을 통해 50명에게 스타벅스 기프티콘을 드립니다!</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.15</span>
                            <span>👁️ 5,678회</span>
                            <span>💬 234건</span>
                        </div>
                    </div>

                    <div class="notice-card" onclick="openNoticeModal('업데이트', '새로운 기능 추가 안내', '게시글 좋아요 기능, 댓글 알림 기능, 프로필 커스터마이징 기능이 추가되었습니다.<br><br><strong>주요 업데이트 내용:</strong><br>• 게시글에 좋아요를 누를 수 있습니다<br>• 내 게시글에 댓글이 달리면 알림을 받습니다<br>• 프로필 사진과 배경을 변경할 수 있습니다<br>• UI/UX가 개선되었습니다<br><br>더욱 편리해진 서비스를 경험해보세요!', '2025.01.10', '3,456회', '45건', false)">
                        <span class="notice-badge badge-update">업데이트</span>
                        <h4 class="notice-title">새로운 기능 추가 안내</h4>
                        <p class="notice-description">게시글 좋아요 기능, 댓글 알림 기능, 프로필 커스터마이징 기능이 추가되었습니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.10</span>
                            <span>👁️ 3,456회</span>
                            <span>💬 45건</span>
                        </div>
                    </div>

                    <div class="notice-card" onclick="openNoticeModal('안내', '모바일 앱 출시 예정', '더욱 편리하게 이용하실 수 있도록 Android/iOS 앱을 준비 중입니다.<br><br><strong>출시 일정:</strong> 2025년 3월 예정<br><strong>지원 플랫폼:</strong> Android 8.0 이상, iOS 13.0 이상<br><br>앱 출시 전 사전 등록 이벤트도 진행될 예정이니 많은 관심 부탁드립니다!', '2025.01.08', '4,123회', '89건', false)">
                        <span class="notice-badge badge-info">안내</span>
                        <h4 class="notice-title">모바일 앱 출시 예정</h4>
                        <p class="notice-description">더욱 편리하게 이용하실 수 있도록 Android/iOS 앱을 준비 중입니다. 2025년 3월 출시 예정입니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.08</span>
                            <span>👁️ 4,123회</span>
                            <span>💬 89건</span>
                        </div>
                    </div>

                    <div class="notice-card" onclick="openNoticeModal('공지', '무료 취업 특강 안내', '취업 준비생을 위한 무료 온라인 특강을 진행합니다.<br><br><strong>일시:</strong> 2025년 1월 30일(목) 19:00~21:00<br><strong>주제:</strong> 합격하는 자소서 작성법<br><strong>강사:</strong> 현직 인사담당자<br><strong>참여 방법:</strong> 사전 신청 (선착순 100명)<br><br>많은 참여 부탁드립니다!', '2025.01.05', '2,890회', '67건', false)">
                        <span class="notice-badge badge-info">공지</span>
                        <h4 class="notice-title">무료 취업 특강 안내</h4>
                        <p class="notice-description">취업 준비생을 위한 무료 온라인 특강을 진행합니다. 1/30(목) 저녁 7시, '합격하는 자소서 작성법' 주제입니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.05</span>
                            <span>👁️ 2,890회</span>
                            <span>💬 67건</span>
                        </div>
                    </div>

                    <div class="notice-card" onclick="openNoticeModal('이벤트', '새해 맞이 이벤트 당첨자 발표', '2025년 새해 맞이 이벤트에 참여해주신 모든 분들께 감사드립니다.<br><br><strong>당첨자 명단:</strong><br>1등 (아이패드): 홍길동님<br>2등 (에어팟): 김철수님, 이영희님<br>3등 (스타벅스): 박민수님 외 47명<br><br>당첨자분들께는 개별 연락드리겠습니다.', '2025.01.03', '6,234회', '156건', false)">
                        <span class="notice-badge badge-event">이벤트</span>
                        <h4 class="notice-title">새해 맞이 이벤트 당첨자 발표</h4>
                        <p class="notice-description">2025년 새해 맞이 이벤트에 참여해주신 모든 분들께 감사드립니다. 당첨자 명단을 확인하실 수 있습니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.03</span>
                            <span>👁️ 6,234회</span>
                            <span>💬 156건</span>
                        </div>
                    </div>

                    <div class="notice-card" onclick="openNoticeModal('업데이트', '보안 강화 업데이트', '회원님들의 개인정보 보호를 위해 2단계 인증 기능이 추가되었습니다.<br><br><strong>2단계 인증이란?</strong><br>로그인 시 비밀번호 외에 추가 인증 수단을 통해 보안을 강화하는 기능입니다.<br><br><strong>설정 방법:</strong><br>마이페이지 > 보안설정 > 2단계 인증 활성화<br><br>개인정보 보호를 위해 설정을 권장드립니다.', '2025.01.02', '1,567회', '23건', false)">
                        <span class="notice-badge badge-update">업데이트</span>
                        <h4 class="notice-title">보안 강화 업데이트</h4>
                        <p class="notice-description">회원님들의 개인정보 보호를 위해 2단계 인증 기능이 추가되었습니다. 설정 메뉴에서 활성화하실 수 있습니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.02</span>
                            <span>👁️ 1,567회</span>
                            <span>💬 23건</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 중요 공지 (오른쪽) -->
            <div class="notice-section">
                <div class="notice-header important">
                    <span style="font-size: 1.5rem;">
                    <h2>중요 공지</h2>
                </div>
                <div class="notice-body">
                    <div class="notice-card urgent" onclick="openNoticeModal('긴급', '개인정보 보호 정책 업데이트 안내', '개인정보 처리방침이 2025년 2월 1일부터 개정됩니다.<br><br><strong>주요 변경사항:</strong><br>• 개인정보 수집 및 이용 목적 명확화<br>• 개인정보 보유 기간 조정<br>• 제3자 제공 관련 규정 추가<br>• 회원 권리 강화<br><br><strong>시행일:</strong> 2025년 2월 1일<br><br>회원님의 권리와 의무에 관한 중요한 변경사항이 있으니 반드시 확인해주시기 바랍니다.', '2025.01.22', '2,345회', '12건', true)">
                        <span class="notice-badge badge-urgent">긴급</span>
                        <h4 class="notice-title">개인정보 보호 정책 업데이트 안내</h4>
                        <p class="notice-description">개인정보 처리방침이 2025년 2월 1일부터 개정됩니다. 회원님의 권리와 의무에 관한 중요한 변경사항이 있으니 반드시 확인해주시기 바랍니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.22</span>
                            <span>👁️ 2,345회</span>
                            <span>💬 12건</span>
                        </div>
                    </div>

                    <div class="notice-card urgent" onclick="openNoticeModal('긴급', '서버 정기 점검 안내', '안정적인 서비스 제공을 위한 정기 점검이 진행됩니다.<br><br><strong>점검 일시:</strong> 2025년 1월 25일(토) 02:00 ~ 06:00 (4시간)<br><strong>점검 내용:</strong><br>• 서버 시스템 업그레이드<br>• 데이터베이스 최적화<br>• 보안 패치 적용<br><br>점검 시간 동안 서비스 이용이 일시 중단되오니 양해 부탁드립니다.', '2025.01.20', '1,892회', '8건', true)">
                        <span class="notice-badge badge-urgent">긴급</span>
                        <h4 class="notice-title">서버 정기 점검 안내 (1/25 새벽 2시~6시)</h4>
                        <p class="notice-description">안정적인 서비스 제공을 위한 정기 점검이 진행됩니다. 점검 시간 동안 서비스 이용이 일시 중단되오니 양해 부탁드립니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.20</span>
                            <span>👁️ 1,892회</span>
                            <span>💬 8건</span>
                        </div>
                    </div>

                    <div class="notice-card urgent" onclick="openNoticeModal('중요', '서비스 이용약관 개정 안내', '서비스 이용약관이 2025년 2월 15일부터 개정됩니다.<br><br><strong>주요 변경사항:</strong><br>• 서비스 이용 제한 사유 구체화<br>• 게시물 관리 정책 강화<br>• 환불 규정 명확화<br><br>개정된 약관은 시행일부터 적용되며, 동의하지 않으실 경우 서비스 이용이 제한될 수 있습니다.', '2025.01.18', '1,234회', '5건', true)">
                        <span class="notice-badge badge-urgent">중요</span>
                        <h4 class="notice-title">서비스 이용약관 개정 안내</h4>
                        <p class="notice-description">서비스 이용약관이 2025년 2월 15일부터 개정됩니다. 주요 변경사항을 확인해주세요.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.18</span>
                            <span>👁️ 1,234회</span>
                            <span>💬 5건</span>
                        </div>
                    </div>

                    <div class="notice-card urgent" onclick="openNoticeModal('필독', '커뮤니티 이용 규칙 변경', '건전한 커뮤니티 문화 조성을 위해 이용 규칙이 일부 강화되었습니다.<br><br><strong>변경된 규칙:</strong><br>• 욕설, 비방 게시물 즉시 삭제 및 경고<br>• 허위 정보 유포 시 이용 정지<br>• 타인 사칭 시 영구 정지<br>• 광고성 게시물 작성 제한<br><br>위반 시 제재 조치가 있을 수 있으니 유의해주시기 바랍니다.', '2025.01.15', '987회', '3건', true)">
                        <span class="notice-badge badge-urgent">필독</span>
                        <h4 class="notice-title">커뮤니티 이용 규칙 변경</h4>
                        <p class="notice-description">건전한 커뮤니티 문화 조성을 위해 이용 규칙이 일부 강화되었습니다. 위반 시 제재 조치가 있을 수 있습니다.</p>
                        <div class="notice-meta">
                            <span>📅 2025.01.15</span>
                            <span>👁️ 987회</span>
                            <span>💬 3건</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ 섹션 -->
        <div class="card full-width">
            <div class="card-header">
                <h2>자주 묻는 질문 (FAQ)</h2>
            </div>
            <div class="card-body">
                <div class="board-item">
                    <h4>Q. 회원 탈퇴는 어떻게 하나요?</h4>
                    <p>A. 마이페이지 > 설정 > 회원 탈퇴 메뉴에서 탈퇴하실 수 있습니다. 탈퇴 시 모든 작성글과 활동 내역이 삭제됩니다.</p>
                </div>
                <div class="board-item">
                    <h4>Q. 비밀번호를 잊어버렸어요.</h4>
                    <p>A. 로그인 페이지의 '비밀번호 찾기'를 클릭하시면 가입하신 이메일로 임시 비밀번호가 발송됩니다.</p>
                </div>
                <div class="board-item">
                    <h4>Q. 게시글 작성 시 이미지 첨부가 안 돼요.</h4>
                    <p>A. 이미지는 최대 10MB까지 업로드 가능하며, jpg, png, gif 형식만 지원됩니다. 문제가 지속되면 고객센터로 문의해주세요.</p>
                </div>
                <div class="board-item">
                    <h4>Q. 모바일에서도 이용할 수 있나요?</h4>
                    <p>A. 네, 모바일 웹 브라우저에서도 이용 가능합니다. 2025년 3월에는 전용 앱도 출시될 예정입니다.</p>
                </div>
                <div class="board-item">
                    <h4>Q. 회원 등급은 어떻게 올라가나요?</h4>
                    <p>A. 활동 점수에 따라 자동으로 등급이 상승합니다. 게시글 작성, 댓글 작성, 좋아요 등의 활동으로 점수를 획득할 수 있습니다.</p>
                </div>
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
                    <p>📧 support@company.com</p>
                    <p>📞 02-1234-5678</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 Company Community Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- 공지사항 모달 -->
    <div id="noticeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" id="modalHeaderDiv">
                <button class="modal-close" onclick="closeNoticeModal()">&times;</button>
                <span class="notice-badge" id="modalBadge"></span>
                <h2 class="modal-title" id="modalTitle"></h2>
                <div class="modal-meta">
                    <span id="modalDate"></span>
                    <span id="modalViews"></span>
                    <span id="modalComments"></span>
                </div>
            </div>
            <div class="modal-body">
                <div id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button class="btn-confirm" onclick="closeNoticeModal()">확인</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // 공지사항 모달 열기
        function openNoticeModal(badgeText, title, content, date, views, comments, isUrgent) {
            console.log('모달 열기 시도:', title); // 디버깅용
            
            const modal = document.getElementById('noticeModal');
            const modalHeaderDiv = document.getElementById('modalHeaderDiv');
            const modalBadge = document.getElementById('modalBadge');
            const modalTitle = document.getElementById('modalTitle');
            const modalContent = document.getElementById('modalContent');
            const modalDate = document.getElementById('modalDate');
            const modalViews = document.getElementById('modalViews');
            const modalComments = document.getElementById('modalComments');

            // 배지 설정
            modalBadge.textContent = badgeText;
            modalBadge.className = 'notice-badge';
            
            if (badgeText === '긴급' || badgeText === '중요' || badgeText === '필독') {
                modalBadge.classList.add('badge-urgent');
            } else if (badgeText === '이벤트') {
                modalBadge.classList.add('badge-event');
            } else if (badgeText === '업데이트') {
                modalBadge.classList.add('badge-update');
            } else {
                modalBadge.classList.add('badge-info');
            }

            // 헤더 스타일
            if (isUrgent) {
                modalHeaderDiv.classList.add('urgent');
            } else {
                modalHeaderDiv.classList.remove('urgent');
            }

            // 내용 설정
            modalTitle.textContent = title;
            modalContent.innerHTML = content;
            modalDate.innerHTML = '📅 ' + date;
            modalViews.innerHTML = '👁️ ' + views;
            modalComments.innerHTML = '💬 ' + comments;

            // 모달 열기
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            console.log('모달 열림!'); // 디버깅용
        }

        // 공지사항 모달 닫기
        function closeNoticeModal() {
            const modal = document.getElementById('noticeModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // 모달 외부 클릭 시 닫기
        document.getElementById('noticeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNoticeModal();
            }
        });

        // ESC 키로 모달 닫기
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeNoticeModal();
            }
        });

        console.log('JavaScript 로드 완료!'); // 디버깅용
    </script>
</body>
</html>