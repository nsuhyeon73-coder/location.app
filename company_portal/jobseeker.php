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
    <title>취준생 공간 - Company Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* JOBKOREA 스타일 카드 그리드 */
        .jobseeker-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .section-header {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f2f3f5;
        }

        /* 서비스 카드 그리드 */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .service-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
        }

        .service-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            border-color: #3366ff;
        }

        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .service-card h4 {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .service-card p {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        /* 게시글/이벤트 카드 그리드 */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .content-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }

        .content-card h3 {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f2f3f5;
        }

        .post-item {
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .post-item:hover {
            background: #f9f9f9;
            margin: 0 -12px;
            padding: 16px 12px;
            border-radius: 6px;
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-item h4 {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .post-item p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .post-meta {
            display: flex;
            gap: 12px;
            font-size: 12px;
            color: #999;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }

        .badge.green {
            background: #e6f7ed;
            color: #1a7f3c;
        }

        .badge.blue {
            background: #e8f3ff;
            color: #0066ff;
        }

        .badge.yellow {
            background: #fff9e6;
            color: #ff9900;
        }

        /* 면접 후기 리스트 */
        .review-list {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }

        .review-item {
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .review-item:hover {
            border-color: #3366ff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .review-item:last-child {
            margin-bottom: 0;
        }

        .review-title {
            font-size: 15px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .review-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .stats {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: #999;
        }

        /* 반응형 */
        @media (max-width: 968px) {
            .service-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .service-grid {
                grid-template-columns: 1fr;
            }
        }

        /* 모달 스타일 (기존 유지) */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .modal-body h3 {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin: 24px 0 16px;
        }

        .modal-body p,
        .modal-body ul,
        .modal-body ol {
            line-height: 1.8;
            color: #555;
            font-size: 14px;
        }

        .modal-body strong {
            color: #333;
            font-weight: 700;
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
                    <a href="notice.php">공지사항</a>
                </div>
                <div class="nav-item dropdown">
                    <a href="jobseeker.php" class="active">취준생 공간</a>
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

    <main class="jobseeker-container">
        <!-- 취업 지원 서비스 -->
        <h2 class="section-header">🎯 취업 지원 서비스</h2>
        <div class="service-grid">
            <div class="service-card" onclick="showServiceAlert('이력서 첨삭')">
                <div class="service-icon">
                    <img src="1.이력서.png" alt="이력서 첨삭" onerror="this.style.display='none'; this.parentElement.innerHTML='📝';">
                </div>
                <h4>이력서 첨삭</h4>
                <p>무료로 이력서 첨삭을 받아보세요</p>
            </div>
            
            <div class="service-card" onclick="showServiceAlert('면접 후기')">
                <div class="service-icon">
                    <img src="2.면접후기.png" alt="면접 후기" onerror="this.style.display='none'; this.parentElement.innerHTML='💼';">
                </div>
                <h4>면접 후기</h4>
                <p>실제 면접 경험을 공유합니다</p>
            </div>
            
            <div class="service-card" onclick="showServiceAlert('기업 정보')">
                <div class="service-icon">
                    <img src="3.기업정보.png" alt="기업 정보" onerror="this.style.display='none'; this.parentElement.innerHTML='🏢';">
                </div>
                <h4>기업 정보</h4>
                <p>기업별 상세 정보를 확인하세요</p>
            </div>
            
            <div class="service-card" onclick="showServiceAlert('자기계발')">
                <div class="service-icon">
                    <img src="4.자기계발.png" alt="자기계발" onerror="this.style.display='none'; this.parentElement.innerHTML='📚';">
                </div>
                <h4>자기계발</h4>
                <p>취업 준비 노하우를 나눠요</p>
            </div>
            
            <div class="service-card" onclick="showServiceAlert('취업 트렌드')">
                <div class="service-icon">
                    <img src="5.취업트렌드.png" alt="취업 트렌드" onerror="this.style.display='none'; this.parentElement.innerHTML='📈';">
                </div>
                <h4>취업 트렌드</h4>
                <p>최신 취업 시장 동향 분석</p>
            </div>
            
            <div class="service-card" onclick="showServiceAlert('포트폴리오')">
                <div class="service-icon">
                    <img src="6.포트.png" alt="포트폴리오" onerror="this.style.display='none'; this.parentElement.innerHTML='💼';">
                </div>
                <h4>포트폴리오</h4>
                <p>합격 포트폴리오 갤러리</p>
            </div>
        </div>

        <!-- 인기 게시글 & 이벤트/공모전 -->
        <div class="content-grid">
            <div class="content-card">
                <h3>📌 인기 게시글</h3>
                <div class="post-item" onclick="showPostAlert('대기업 서류 합격 노하우')">
                    <span class="badge green">꿀팁</span>
                    <h4>대기업 서류 합격 노하우</h4>
                    <div class="post-meta">
                        <span>👤 합격자A</span>
                        <span>💬 45건</span>
                        <span>📅 1일 전</span>
                    </div>
                </div>
                <div class="post-item" onclick="showPostAlert('면접 예상 질문 100선')">
                    <span class="badge blue">면접</span>
                    <h4>면접 예상 질문 100선</h4>
                    <div class="post-meta">
                        <span>👤 취준생B</span>
                        <span>💬 67건</span>
                        <span>📅 2일 전</span>
                    </div>
                </div>
                <div class="post-item" onclick="showPostAlert('자기소개서 작성법')">
                    <span class="badge yellow">자소서</span>
                    <h4>자기소개서 작성법</h4>
                    <div class="post-meta">
                        <span>👤 멘토C</span>
                        <span>💬 34건</span>
                        <span>📅 3일 전</span>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <h3>🎉 이벤트 / 공모전</h3>
                <div class="post-item" onclick="showEventAlert('데이터 분석/AI 경진대회')">
                    <h4>데이터 분석/AI 경진대회</h4>
                    <p>알고리즘 실력으로 실생활 문제 해결 및 데이터 포트폴리오 확보</p>
                    <div class="post-meta">
                        <span>📅 마감일: 2025.11.15 16시까지</span>
                    </div>
                </div>
                <div class="post-item" onclick="showEventAlert('UI/UX/웹 개발 디자인 공모전')">
                    <h4>UI/UX/웹 개발 디자인 공모전</h4>
                    <p>디자이너/개발자 협업으로 완성도 높은 포트폴리오 프로젝트 제작</p>
                    <div class="post-meta">
                        <span>📅 마감일: 2025.12.30 18시까지</span>
                    </div>
                </div>
                <div class="post-item" onclick="showEventAlert('외국계 기업 인턴십/서포터즈')">
                    <h4>외국계 기업 인턴십/서포터즈</h4>
                    <p>글로벌 기업에서의 실무 경험 및 네트워킹 기회</p>
                    <div class="post-meta">
                        <span>📅 마감일: 2026.01.20 13시까지 서류 제출</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 최근 면접 후기 -->
        <h2 class="section-header">💼 최근 면접 후기</h2>
        <div class="review-list">
            <div class="review-item" onclick="showReviewAlert('네이버 신입 개발자 면접 후기')">
                <span class="badge blue">후기</span>
                <h4 class="review-title">네이버 신입 개발자 면접 후기</h4>
                <p class="review-desc">기술면접과 인성면접 질문 내용을 공유합니다. 준비하시는 분들께 도움이 되길 바랍니다.</p>
                <div class="stats">
                    <span>👍 523</span>
                    <span>💬 28건</span>
                    <span>❤️ 45건</span>
                </div>
            </div>
            <div class="review-item" onclick="showReviewAlert('카카오 마케터 면접 경험담')">
                <span class="badge blue">후기</span>
                <h4 class="review-title">카카오 마케터 면접 경험담</h4>
                <p class="review-desc">1차부터 최종면접까지의 과정을 상세히 적어봤습니다.</p>
                <div class="stats">
                    <span>👍 387</span>
                    <span>💬 19건</span>
                    <span>❤️ 32건</span>
                </div>
            </div>
            <div class="review-item" onclick="showReviewAlert('삼성전자 GSAT 후기 및 팁')">
                <span class="badge green">후기</span>
                <h4 class="review-title">삼성전자 GSAT 후기 및 팁</h4>
                <p class="review-desc">GSAT 준비 방법과 실제 시험 경험을 공유합니다.</p>
                <div class="stats">
                    <span>👍 612</span>
                    <span>💬 41건</span>
                    <span>❤️ 58건</span>
                </div>
            </div>
            <div class="review-item" onclick="showReviewAlert('쿠팡 물류센터 인턴 면접 후기')">
                <span class="badge yellow">후기</span>
                <h4 class="review-title">쿠팡 물류센터 인턴 면접 후기</h4>
                <p class="review-desc">인턴 면접 과정과 분위기에 대해 공유합니다.</p>
                <div class="stats">
                    <span>👍 289</span>
                    <span>💬 15건</span>
                    <span>❤️ 23건</span>
                </div>
            </div>
            <div class="review-item" onclick="showReviewAlert('현대자동차 신입 면접 경험')">
                <span class="badge green">후기</span>
                <h4 class="review-title">현대자동차 신입 면접 경험</h4>
                <p class="review-desc">자동차 업계 면접 준비하시는 분들께 도움되길 바랍니다.</p>
                <div class="stats">
                    <span>👍 456</span>
                    <span>💬 22건</span>
                    <span>❤️ 38건</span>
                </div>
            </div>
        </div>
    </main>

    <!-- 모달 창 -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-close" onclick="closeModal()">&times;</span>
                <h2 id="modal-title"></h2>
            </div>
            <div class="modal-body" id="modal-body"></div>
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
                    <p>📧 support@company.com</p>
                    <p>📞 02-1234-5678</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 Company Community Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // 기존 JavaScript 코드 그대로 유지
        console.log('✅ Jobseeker page JavaScript loaded!');

        const modal = document.getElementById('modal');
        const modalBody = document.getElementById('modal-body');
        const modalTitle = document.getElementById('modal-title');

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openModal(title, content) {
            modalTitle.textContent = title;
            modalBody.innerHTML = content;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        function showServiceAlert(serviceName) {
            alert('🚀 [' + serviceName + '] 서비스\n\n준비 중인 기능입니다.\n곧 오픈 예정이니 조금만 기다려주세요!');
        }

        const postData = {
            '대기업 서류 합격 노하우': {
                content: `
                    <h3>📝 서류 작성 핵심 포인트</h3>
                    <p><strong>1. 자기소개서는 구체적인 경험 중심으로</strong><br>
                    "열심히 했다"가 아니라 "어떤 문제를 어떻게 해결했고, 그 결과 무엇을 얻었는지" 구체적으로 작성하세요. 숫자와 데이터를 활용하면 더욱 신뢰도가 높아집니다.</p>
                    
                    <p><strong>2. 기업 맞춤형 작성이 필수</strong><br>
                    각 기업의 인재상과 핵심 가치를 파악하고, 본인의 경험을 그에 맞춰 풀어내세요. 같은 경험이라도 기업마다 다르게 어필할 수 있습니다.</p>
                `
            }
        };

        const eventData = {
            '데이터 분석/AI 경진대회': {
                content: `
                    <h3>🏆 대회 개요</h3>
                    <p>실생활 데이터를 활용한 AI 솔루션 개발 대회입니다. 알고리즘 실력을 뽐내고 실무 포트폴리오를 만들 수 있는 절호의 기회입니다!</p>
                    
                    <h3>🎯 참가 대상</h3>
                    <ul>
                        <li>대학생 및 대학원생 (휴학생 포함)</li>
                        <li>취업준비생</li>
                        <li>데이터 분석/AI에 관심 있는 모든 분</li>
                    </ul>
                `
            }
        };

        function showPostAlert(postTitle) {
            const data = postData[postTitle];
            if (data) {
                openModal(postTitle, data.content);
            } else {
                alert('게시글 정보를 찾을 수 없습니다.');
            }
        }

        function showEventAlert(eventTitle) {
            const data = eventData[eventTitle];
            if (data) {
                openModal(eventTitle, data.content);
            } else {
                alert('이벤트 정보를 찾을 수 없습니다.');
            }
        }

        function showReviewAlert(reviewTitle) {
            alert('💼 ' + reviewTitle + '\n\n면접 후기 상세 내용은 준비 중입니다.\n더 많은 정보를 곧 제공해드릴게요!');
        }
    </script>
</body>
</html>