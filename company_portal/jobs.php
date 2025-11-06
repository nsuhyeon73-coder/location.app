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
    <title>채용 공고 - Company Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #333;
            background: #f8f9fa;
        }

        /* ==================== 헤더 스타일 ==================== */
        .header {
            background: #fff;
            border-bottom: 1px solid #e1e4e8;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .logo h1 {
            font-size: 1.8rem;
            color: #0066ff;
            font-weight: 700;
            cursor: pointer;
        }

        .logo h1 span {
            display: inline-block;
            animation: bounce 1.5s ease-in-out infinite;
        }

        .logo h1 span:nth-child(1) { animation-delay: 0s; }
        .logo h1 span:nth-child(2) { animation-delay: 0.1s; }
        .logo h1 span:nth-child(3) { animation-delay: 0.2s; }
        .logo h1 span:nth-child(4) { animation-delay: 0.3s; }
        .logo h1 span:nth-child(5) { animation-delay: 0.4s; }
        .logo h1 span:nth-child(6) { animation-delay: 0.5s; }
        .logo h1 span:nth-child(7) { animation-delay: 0.6s; }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            25% {
                transform: translateY(-15px) scale(1.1);
            }
            50% {
                transform: translateY(0) scale(1);
            }
        }

        .nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .nav a:hover,
        .nav a.active {
            color: #0066ff;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .welcome-msg {
            color: #333;
            font-weight: 600;
        }

        .user-badge {
            background: #0066ff;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-logout {
           
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

       

        /* ==================== 햄버거 메뉴 ==================== */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            z-index: 1001;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #333;
            border-radius: 3px;
            transition: all 0.3s;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        /* ==================== 배너 이미지 ==================== */
        .job-banner-image {
            width: 100%;
            height: 400px;
            background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1600&h=400&fit=crop');
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 40px;
        }

        .job-banner-image .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
        }

        .job-banner-image .banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }

        .job-banner-image .banner-text h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .job-banner-image .banner-text p {
            font-size: 18px;
            opacity: 0.95;
        }

        /* ==================== 메인 컨텐츠 ==================== */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        /* 신입 채용 + 달력 레이아웃 (상단) */
        .top-section {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 30px;
            margin-bottom: 50px;
        }

        /* IT 채용 레이아웃 (하단) */
        .bottom-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title::before {
            content: '';
            width: 5px;
            height: 36px;
            background: #3b82f6;
            border-radius: 3px;
        }

        .jobs-content {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .job-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .job-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #3b82f6;
            transform: scaleY(0);
            transition: transform 0.3s;
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-color: #3b82f6;
        }

        .job-card:hover::before {
            transform: scaleY(1);
        }

        .job-company {
            font-size: 16px;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 8px;
        }

        .job-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
        }

        .job-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .job-meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 13px;
            color: #6b7280;
        }

        .job-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ==================== 달력 스타일 ==================== */
        .calendar-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            height: fit-content;
            position: sticky;
            top: 90px;
        }

        .calendar-header {
            background: #f9fafb;
            padding: 30px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .calendar-time {
            font-size: 48px;
            font-weight: 300;
            color: #111827;
            margin-bottom: 12px;
            font-family: 'Segoe UI', system-ui, sans-serif;
            letter-spacing: -1px;
        }

        .calendar-date {
            font-size: 16px;
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .calendar-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .calendar-header p {
            font-size: 13px;
            color: #6b7280;
        }

        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: #fff;
        }

        .calendar-nav button {
            background: none;
            border: none;
            color: #111827;
            font-size: 22px;
            cursor: pointer;
            padding: 10px 14px;
            transition: all 0.2s;
            border-radius: 8px;
        }

        .calendar-nav button:hover {
            background: #f3f4f6;
        }

        .calendar-month {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .calendar-body {
            padding: 16px 20px 20px;
            background: #fff;
        }

        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }

        .calendar-weekday {
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: #9ca3af;
            padding: 6px 0;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            color: #374151;
            font-weight: 500;
            min-height: 40px;
            text-align: center;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        .calendar-day:not(.other-month):not(.today):hover {
            background: #f3f4f6;
            transform: scale(1.05);
        }

        .calendar-day:not(.other-month):not(.today):active {
            transform: scale(0.95);
            background: #e5e7eb;
        }

        .calendar-day.other-month {
            color: #d1d5db;
            cursor: default;
        }

        .calendar-day.today {
            background: #3b82f6;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .calendar-day.selected {
            background: #fff;
            color: #111827;
            font-weight: 700;
            border: 3px solid #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .calendar-day.job-start::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 0;
            height: 0;
            border-left: 10px solid #10b981;
            border-bottom: 10px solid transparent;
        }

        .calendar-day.job-end::after {
            content: '';
            position: absolute;
            bottom: 3px;
            right: 3px;
            width: 0;
            height: 0;
            border-right: 10px solid #ef4444;
            border-top: 10px solid transparent;
        }

        .calendar-day.has-event {
            font-weight: 700;
        }

        /* ==================== 모달 스타일 ==================== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
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
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-job-item {
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid #3b82f6;
        }

        .modal-job-item:last-child {
            margin-bottom: 0;
        }

        .modal-job-company {
            font-size: 14px;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 8px;
        }

        .modal-job-period {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: white;
            border-radius: 6px;
        }

        .modal-job-item h4 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .modal-job-item p {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .modal-job-meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 13px;
            color: #6b7280;
        }

        .no-jobs {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
            font-size: 16px;
            line-height: 1.8;
        }

        /* ==================== 반응형 디자인 ==================== */
        @media (max-width: 1200px) {
            .top-section {
                grid-template-columns: 1fr;
            }

            .calendar-card {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: flex !important;
            }

            .header .container > .user-info {
                display: none !important;
            }

            .nav {
                position: fixed !important;
                top: 0 !important;
                left: -100% !important;
                width: 85% !important;
                max-width: 400px !important;
                height: 100vh !important;
                background: white !important;
                flex-direction: column !important;
                align-items: stretch !important;
                padding: 0 !important;
                gap: 0 !important;
                box-shadow: 2px 0 15px rgba(0,0,0,0.3) !important;
                transition: left 0.3s ease-in-out !important;
                overflow-y: auto !important;
                z-index: 9999 !important;
            }

            .nav.active {
                left: 0 !important;
            }

            .nav::before {
                content: 'Company' !important;
                display: block !important;
                padding: 2rem 1.5rem !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                color: white !important;
                font-size: 1.8rem !important;
                font-weight: 700 !important;
                text-align: center !important;
                border-bottom: 3px solid rgba(255,255,255,0.3) !important;
            }

            .nav a {
                display: block !important;
                width: 100% !important;
                padding: 1.2rem 1.5rem !important;
                border-bottom: 1px solid #f0f0f0 !important;
                font-size: 1.1rem !important;
                color: #333 !important;
                text-decoration: none !important;
                transition: all 0.2s !important;
            }

            .nav a:hover,
            .nav a.active {
                background: #f8f9fa !important;
                color: #667eea !important;
                padding-left: 2rem !important;
            }

            .nav .user-info {
                display: flex !important;
                width: 100% !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1rem !important;
                padding: 1.5rem !important;
                background: #f8f9fa !important;
                border-top: 3px solid #e1e4e8 !important;
                margin-top: auto !important;
            }

            .nav .user-info .welcome-msg {
                font-size: 1.1rem !important;
                color: #333 !important;
                font-weight: 600 !important;
            }

            .nav .user-info .user-badge {
                font-size: 0.9rem !important;
                padding: 0.4rem 0.8rem !important;
                background: #667eea !important;
                color: white !important;
                border-radius: 12px !important;
            }

            .nav .user-info a {
                width: 100% !important;
                text-align: center !important;
                padding: 0.9rem !important;
               
                color: white !important;
                border-radius: 8px !important;
                font-weight: 600 !important;
                text-decoration: none !important;
                border: none !important;
                margin: 0 !important;
            }

            .job-banner-image {
                height: 250px;
            }

            .job-banner-image .banner-text h1 {
                font-size: 32px;
            }

            .job-banner-image .banner-text p {
                font-size: 14px;
            }

            .jobs-content {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- 헤더 -->
    <header class="header">
        <div class="container">
            <div class="logo" onclick="location.href='index.php'">
                <h1>
                    <span>C</span>
                    <span>o</span>
                    <span>m</span>
                    <span>p</span>
                    <span>a</span>
                    <span>n</span>
                    <span>y</span>
                </h1>
            </div>
            <nav class="nav" id="nav">
                <a href="index.php">홈</a>
                <a href="study.php">스터디</a>
                <a href="jobs.php" class="active">채용공고</a>
                <a href="board.php">익명게시판</a>
                <a href="notice.php">공지사항</a>
                <a href="jobseeker.php">취준생공간</a>
                
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

    <!-- 배너 이미지 -->
    <div class="job-banner-image">
        <div class="banner-overlay"></div>
        <div class="banner-text">
            <h1>채용 공고</h1>
            <p>최신 채용 정보를 한눈에 확인하세요</p>
        </div>
    </div>

    <!-- 메인 컨텐츠 -->
    <div class="main-content">
        <!-- 상단 섹션: 신입 채용 + 달력 -->
        <div class="top-section">
            <!-- 신입 채용 공고 -->
            <div>
                <h2 class="section-title">신입 채용</h2>
                <div class="jobs-content">
                    <div class="job-card" onclick="location.href='job_detail.php?id=1'" style="cursor: pointer;">
                        <div class="job-company">삼성전자</div>
                        <div class="job-title">SW 개발 신입사원</div>
                        <div class="job-desc">C/C++, Java 등 개발 경험자 우대, 학력 무관</div>
                        <div class="job-meta">
                            <span>📍 서울 서초</span>
                            <span>💰 회사내규</span>
                            <span>📅 ~12.31</span>
                        </div>
                    </div>

                    <div class="job-card" onclick="location.href='job_detail.php?id=2'" style="cursor: pointer;">
                        <div class="job-company">SK하이닉스</div>
                        <div class="job-title">반도체 설계 엔지니어</div>
                        <div class="job-desc">전자/전기 전공, Verilog/VHDL 사용 가능자</div>
                        <div class="job-meta">
                            <span>📍 경기 이천</span>
                            <span>💰 협의</span>
                            <span>📅 ~12.20</span>
                        </div>
                    </div>

                    <div class="job-card" onclick="location.href='job_detail.php?id=3'" style="cursor: pointer;">
                        <div class="job-company">LG전자</div>
                        <div class="job-title">AI 연구원</div>
                        <div class="job-desc">Python, TensorFlow/PyTorch 활용 가능자</div>
                        <div class="job-meta">
                            <span>📍 서울 강남</span>
                            <span>💰 회사내규</span>
                            <span>📅 ~11.30</span>
                        </div>
                    </div>

                    <div class="job-card" onclick="location.href='job_detail.php?id=4'" style="cursor: pointer;">
                        <div class="job-company">현대자동차</div>
                        <div class="job-title">자율주행 개발자</div>
                        <div class="job-desc">컴퓨터비전, 딥러닝 경험자 우대</div>
                        <div class="job-meta">
                            <span>📍 경기 화성</span>
                            <span>💰 협의</span>
                            <span>📅 ~12.15</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 채용 달력 -->
            <div class="calendar-card">
                <div class="calendar-header">
                    <div class="calendar-time" id="currentTime">00:00:00</div>
                    <div class="calendar-date" id="currentDate">2025년 11월 6일 수요일</div>
                    <h3>채용 일정</h3>
                    <p>마감일을 클릭하면 상세 정보를 확인할 수 있습니다</p>
                </div>
                <div class="calendar-nav">
                    <button onclick="prevMonth()">◀</button>
                    <span class="calendar-month" id="calendarMonth">2025년 11월</span>
                    <button onclick="nextMonth()">▶</button>
                </div>
                <div class="calendar-body">
                    <div class="calendar-weekdays">
                        <div class="calendar-weekday">일</div>
                        <div class="calendar-weekday">월</div>
                        <div class="calendar-weekday">화</div>
                        <div class="calendar-weekday">수</div>
                        <div class="calendar-weekday">목</div>
                        <div class="calendar-weekday">금</div>
                        <div class="calendar-weekday">토</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                </div>
            </div>
        </div>

        <!-- 하단 섹션: IT 채용 -->
        <div class="bottom-section">
            <h2 class="section-title">IT 개발 채용</h2>
            <div class="jobs-content">
                <div class="job-card" onclick="location.href='job_detail.php?id=1'" style="cursor: pointer;">
                    <div class="job-company">네이버</div>
                    <div class="job-title">백엔드 개발자</div>
                    <div class="job-desc">Java, Spring Boot, MSA 경험자</div>
                    <div class="job-meta">
                        <span>📍 경기 성남</span>
                        <span>💰 협의</span>
                        <span>📅 ~12.31</span>
                    </div>
                </div>

                <div class="job-card" onclick="location.href='job_detail.php?id=2'" style="cursor: pointer;">
                    <div class="job-company">카카오</div>
                    <div class="job-title">프론트엔드 개발자</div>
                    <div class="job-desc">React, Vue.js, TypeScript 경험</div>
                    <div class="job-meta">
                        <span>📍 경기 판교</span>
                        <span>💰 회사내규</span>
                        <span>📅 ~11.25</span>
                    </div>
                </div>

                <div class="job-card" onclick="location.href='job_detail.php?id=3'" style="cursor: pointer;">
                    <div class="job-company">쿠팡</div>
                    <div class="job-title">데이터 엔지니어</div>
                    <div class="job-desc">Python/Node.js + React 개발 경험자</div>
                    <div class="job-meta">
                        <span>📍 서울 송파</span>
                        <span>💰 협의</span>
                        <span>📅 ~12.10</span>
                    </div>
                </div>

                <div class="job-card" onclick="location.href='job_detail.php?id=1'" style="cursor: pointer;">
                    <div class="job-company">라인</div>
                    <div class="job-title">백엔드 개발자</div>
                    <div class="job-desc">Hadoop, Spark, Kafka 경험자</div>
                    <div class="job-meta">
                        <span>📍 경기 성남</span>
                        <span>💰 회사내규</span>
                        <span>📅 ~12.05</span>
                    </div>
                </div>

                <div class="job-card" onclick="location.href='job_detail.php?id=2'" style="cursor: pointer;">
                    <div class="job-company">배달의민족</div>
                    <div class="job-title">프론트엔드 개발자</div>
                    <div class="job-desc">iOS(Swift) 또는 Android(Kotlin) 개발</div>
                    <div class="job-meta">
                        <span>📍 서울 송파</span>
                        <span>💰 협의</span>
                        <span>📅 ~11.28</span>
                    </div>
                </div>

                <div class="job-card" onclick="location.href='job_detail.php?id=3'" style="cursor: pointer;">
                    <div class="job-company">토스</div>
                    <div class="job-title">데이터 엔지니어</div>
                    <div class="job-desc">보안 시스템 구축 및 관리 경험자</div>
                    <div class="job-meta">
                        <span>📍 서울 강남</span>
                        <span>💰 회사내규</span>
                        <span>📅 ~12.18</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 채용 공고 상세 모달 -->
    <div class="modal" id="jobModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">채용 공고</h2>
                <button class="modal-close" onclick="closeJobModal()">×</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- 동적으로 채워질 내용 -->
            </div>
        </div>
    </div>

    <script>
        // ============================================
        // 1. 햄버거 메뉴
        // ============================================
        document.getElementById('hamburger').addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('nav').classList.toggle('active');
        });

        // ============================================
        // 2. 시계 및 날짜 업데이트
        // ============================================
        function updateClock() {
            var now = new Date();
            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            var seconds = String(now.getSeconds()).padStart(2, '0');
            var timeStr = hours + ':' + minutes + ':' + seconds;
            
            var year = now.getFullYear();
            var month = now.getMonth() + 1;
            var date = now.getDate();
            var days = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];
            var dayOfWeek = days[now.getDay()];
            var dateStr = year + '년 ' + month + '월 ' + date + '일 ' + dayOfWeek;
            
            var timeElement = document.getElementById('currentTime');
            var dateElement = document.getElementById('currentDate');
            
            if (timeElement) {
                timeElement.textContent = timeStr;
            }
            if (dateElement) {
                dateElement.textContent = dateStr;
            }
        }
        
        // 페이지 로드 시 즉시 실행
        updateClock();
        // 1초마다 업데이트
        setInterval(updateClock, 1000);

        // ============================================
        // 3. 채용 공고 데이터
        // ============================================
        var jobSchedule = {
            '2025-11-25': {
                deadlines: [
                    {company: '카카오', title: '프론트엔드 개발자', desc: 'React, Vue.js, TypeScript 경험', location: '경기 판교', salary: '회사내규', start: '2025-10-25', end: '2025-11-25'}
                ]
            },
            '2025-11-28': {
                deadlines: [
                    {company: '배달의민족', title: '모바일 개발자', desc: 'iOS(Swift) 또는 Android(Kotlin) 개발', location: '서울 송파', salary: '협의', start: '2025-10-28', end: '2025-11-28'}
                ]
            },
            '2025-11-30': {
                deadlines: [
                    {company: 'LG전자', title: 'AI 연구원', desc: 'Python, TensorFlow/PyTorch 활용 가능자', location: '서울 강남', salary: '회사내규', start: '2025-10-30', end: '2025-11-30'}
                ]
            },
            '2025-12-05': {
                deadlines: [
                    {company: '라인', title: '데이터 엔지니어', desc: 'Hadoop, Spark, Kafka 경험자', location: '경기 성남', salary: '회사내규', start: '2025-11-05', end: '2025-12-05'}
                ]
            },
            '2025-12-10': {
                deadlines: [
                    {company: '쿠팡', title: '풀스택 개발자', desc: 'Python/Node.js + React 개발 경험자', location: '서울 송파', salary: '협의', start: '2025-11-10', end: '2025-12-10'}
                ]
            },
            '2025-12-15': {
                deadlines: [
                    {company: '현대자동차', title: '자율주행 개발자', desc: '컴퓨터비전, 딥러닝 경험자 우대', location: '경기 화성', salary: '협의', start: '2025-11-15', end: '2025-12-15'}
                ]
            },
            '2025-12-18': {
                deadlines: [
                    {company: '토스', title: '보안 엔지니어', desc: '보안 시스템 구축 및 관리 경험자', location: '서울 강남', salary: '회사내규', start: '2025-11-18', end: '2025-12-18'}
                ]
            },
            '2025-12-20': {
                deadlines: [
                    {company: 'SK하이닉스', title: '반도체 설계 엔지니어', desc: '전자/전기 전공, Verilog/VHDL 사용 가능자', location: '경기 이천', salary: '협의', start: '2025-11-20', end: '2025-12-20'}
                ]
            },
            '2025-12-31': {
                deadlines: [
                    {company: '삼성전자', title: 'SW 개발 신입사원', desc: 'C/C++, Java 등 개발 경험자 우대, 학력 무관', location: '서울 서초', salary: '회사내규', start: '2025-11-01', end: '2025-12-31'},
                    {company: '네이버', title: '백엔드 개발자', desc: 'Java, Spring Boot, MSA 경험자', location: '경기 성남', salary: '협의', start: '2025-11-10', end: '2025-12-31'}
                ]
            }
        };

        // ============================================
        // 4. 달력 관련 변수 및 함수
        // ============================================
        var currentYear, currentMonth;

        function formatDate(year, month, day) {
            return year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
        }

        function getDatesBetween(startDate, endDate) {
            var dates = [];
            var current = new Date(startDate);
            var end = new Date(endDate);
            
            while (current <= end) {
                dates.push(new Date(current));
                current.setDate(current.getDate() + 1);
            }
            return dates;
        }

        function generateCalendar(year, month) {
            var calendarDays = document.getElementById('calendarDays');
            var calendarMonth = document.getElementById('calendarMonth');
            if (!calendarDays || !calendarMonth) return;

            calendarMonth.textContent = year + '년 ' + (month + 1) + '월';

            var firstDay = new Date(year, month, 1);
            var lastDay = new Date(year, month + 1, 0);
            var prevLastDay = new Date(year, month, 0);
            var firstDayOfWeek = firstDay.getDay();
            var lastDate = lastDay.getDate();
            var prevLastDate = prevLastDay.getDate();

            var today = new Date();
            var isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
            var todayDate = today.getDate();

            calendarDays.innerHTML = '';

            // 채용 공고 기간 맵 생성
            var jobPeriodMap = {};
            for (var dateKey in jobSchedule) {
                var jobs = jobSchedule[dateKey].deadlines;
                for (var i = 0; i < jobs.length; i++) {
                    var job = jobs[i];
                    var dates = getDatesBetween(job.start, job.end);
                    for (var j = 0; j < dates.length; j++) {
                        var d = dates[j];
                        var key = formatDate(d.getFullYear(), d.getMonth(), d.getDate());
                        if (!jobPeriodMap[key]) {
                            jobPeriodMap[key] = {start: false, period: false, end: false, deadlines: []};
                        }
                        
                        if (j === 0) {
                            jobPeriodMap[key].start = true;
                        } else if (j === dates.length - 1) {
                            jobPeriodMap[key].end = true;
                            jobPeriodMap[key].deadlines.push(job);
                        } else {
                            jobPeriodMap[key].period = true;
                        }
                    }
                }
            }

            // 이전 달 날짜 채우기
            for (var i = firstDayOfWeek - 1; i >= 0; i--) {
                var day = prevLastDate - i;
                var dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day other-month';
                dayDiv.textContent = day;
                calendarDays.appendChild(dayDiv);
            }

            // 현재 달 날짜 채우기
            for (var day = 1; day <= lastDate; day++) {
                var dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day';
                dayDiv.textContent = day;

                var dateStr = formatDate(year, month, day);

                // 오늘 날짜 표시
                if (isCurrentMonth && day === todayDate) {
                    dayDiv.classList.add('today');
                }

                // 채용 공고 기간 표시
                if (jobPeriodMap[dateStr]) {
                    if (jobPeriodMap[dateStr].start) dayDiv.classList.add('job-start');
                    if (jobPeriodMap[dateStr].period) dayDiv.classList.add('job-period');
                    if (jobPeriodMap[dateStr].end) dayDiv.classList.add('job-end');
                    if (jobPeriodMap[dateStr].deadlines.length > 0) dayDiv.classList.add('has-event');
                }

                // 클릭 이벤트 추가
                (function(dateStr, dayDiv, jobPeriodMapCopy) {
                    dayDiv.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        console.log('클릭된 날짜:', dateStr);
                        
                        // 기존 선택 제거
                        var selected = document.querySelectorAll('.calendar-day.selected');
                        for (var i = 0; i < selected.length; i++) {
                            if (!selected[i].classList.contains('today')) {
                                selected[i].classList.remove('selected');
                            }
                        }
                        
                        // 새로운 선택 추가
                        if (!dayDiv.classList.contains('today')) {
                            dayDiv.classList.add('selected');
                        }
                        
                        // 마감일이 있으면 모달 표시
                        if (jobPeriodMapCopy[dateStr] && jobPeriodMapCopy[dateStr].deadlines.length > 0) {
                            console.log('마감 채용공고:', jobPeriodMapCopy[dateStr].deadlines.length + '개');
                            showJobModal(dateStr, jobPeriodMapCopy[dateStr].deadlines);
                        } else {
                            console.log('해당 날짜에 마감 채용공고 없음');
                        }
                    });
                })(dateStr, dayDiv, jobPeriodMap);

                calendarDays.appendChild(dayDiv);
            }

            // 다음 달 날짜 채우기
            var totalCells = calendarDays.children.length;
            var remainingCells = 42 - totalCells;
            for (var day = 1; day <= remainingCells; day++) {
                var dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day other-month';
                dayDiv.textContent = day;
                calendarDays.appendChild(dayDiv);
            }
        }

        function prevMonth() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentYear, currentMonth);
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentYear, currentMonth);
        }

        // ============================================
        // 5. 모달 관련 함수
        // ============================================
        function showJobModal(dateStr, jobs) {
            var modal = document.getElementById('jobModal');
            var modalTitle = document.getElementById('modalTitle');
            var modalBody = document.getElementById('modalBody');

            var date = new Date(dateStr);
            var dateFormatted = (date.getMonth() + 1) + '월 ' + date.getDate() + '일';
            modalTitle.textContent = dateFormatted + ' 마감 채용공고';

            if (jobs && jobs.length > 0) {
                var html = '';
                for (var i = 0; i < jobs.length; i++) {
                    var job = jobs[i];
                    var startDate = new Date(job.start);
                    var endDate = new Date(job.end);
                    html += '<div class="modal-job-item">';
                    html += '<div class="modal-job-company">' + job.company + '</div>';
                    html += '<div class="modal-job-period">';
                    html += '<strong>📅 접수기간:</strong> ' + (startDate.getMonth() + 1) + '월 ' + startDate.getDate() + '일 ~ ' + (endDate.getMonth() + 1) + '월 ' + endDate.getDate() + '일';
                    html += '</div>';
                    html += '<h4>' + job.title + '</h4>';
                    html += '<p>' + job.desc + '</p>';
                    html += '<div class="modal-job-meta">';
                    html += '<span>📍 ' + job.location + '</span>';
                    html += '<span>💰 ' + job.salary + '</span>';
                    html += '</div></div>';
                }
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="no-jobs">해당 날짜에 마감되는<br>채용공고가 없습니다.</div>';
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeJobModal() {
            var modal = document.getElementById('jobModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // 모달 외부 클릭 시 닫기
        document.getElementById('jobModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeJobModal();
            }
        });

        // ESC 키로 모달 닫기
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeJobModal();
            }
        });

        // ============================================
        // 6. 페이지 로드 시 초기화
        // ============================================
        window.addEventListener('DOMContentLoaded', function() {
            var today = new Date();
            currentYear = today.getFullYear();
            currentMonth = today.getMonth();
            generateCalendar(currentYear, currentMonth);
        });
    </script>
</body>
</html>