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
    <title>Company Portal - 직장인과 취준생을 위한 커뮤니티</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #333;
        }

        /* 헤더 스타일 */
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

       
        /* 검색 섹션 */
        .search-section {
            background: white;
            padding: 3rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .search-box {
            display: flex;
            max-width: 800px;
            margin: 0 auto 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 50px;
            overflow: hidden;
            border: 3px solid #0066ff;
        }

        .search-input {
            flex: 1;
            padding: 1.2rem 2rem;
            border: none;
            font-size: 1.1rem;
            outline: none;
        }

        .search-input::placeholder {
            color: #999;
        }

        .search-btn {
            padding: 1.2rem 2.5rem;
            background: #0066ff;
            border: none;
            color: white;
            font-weight: 600;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .search-btn:hover {
            background: #0052cc;
        }

        .quick-keywords {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .keyword {
            padding: 0.6rem 1.5rem;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .keyword:hover {
            background: #0066ff;
            color: white;
            border-color: #0066ff;
        }

        /* 컨텐츠 섹션 */
        .content-section {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 20px;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #333;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 2rem;
            transition: all 0.3s;
            border: 1px solid #e1e4e8;
        }

        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f5f5f5;
        }

        .card-header h3 {
            font-size: 1.3rem;
            color: #333;
            font-weight: 700;
        }

        .card-link {
            color: #0066ff;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .card-item {
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .card-item:last-child {
            border-bottom: none;
        }

        .card-item:hover {
            background: #f9f9f9;
            margin: 0 -1rem;
            padding: 1rem;
            border-radius: 8px;
        }

        .item-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .item-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #999;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .badge-new {
            background: #ff4d4d;
            color: white;
        }

        .badge-hot {
            background: #ffa500;
            color: white;
        }

        /* 푸터 */
        .footer {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            color: white;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .footer-section a {
            display: block;
            color: #bdc3c7;
            text-decoration: none;
            margin-bottom: 0.5rem;
        }

        .footer-section a:hover {
            color: white;
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #34495e;
            color: #95a5a6;
        }

        /* 반응형 */
        @media (max-width: 768px) {
            .nav {
                display: none;
            }

            .main-slider {
                height: 300px;
            }

            .slide-content h2 {
                font-size: 1.8rem;
            }

            .slide-content p {
                font-size: 1rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .search-box {
                border-radius: 10px;
            }
        }
    
        /* 햄버거 메뉴 */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            z-index: 1001;
            position: relative;
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

        /* 반응형 - 모바일 */
        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav {
                position: fixed;
                top: 0;
                left: -100%;
                width: 80%;
                max-width: 350px;
                height: 100vh;
                background: white;
                flex-direction: column;
                align-items: flex-start;
                padding: 0;
                gap: 0;
                box-shadow: 2px 0 10px rgba(0,0,0,0.2);
                transition: left 0.3s ease;
                overflow-y: auto;
                z-index: 1000;
            }

            .nav.active {
                left: 0;
            }

            /* 메뉴 상단 헤더 */
            .nav::before {
                content: 'Company';
                display: block;
                width: 100%;
                padding: 1.5rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-size: 1.5rem;
                font-weight: 700;
                border-bottom: 2px solid rgba(255,255,255,0.2);
            }

            .nav a {
                width: 100%;
                padding: 1.2rem 1.5rem;
                border-bottom: 1px solid #f0f0f0;
                font-size: 1rem;
                color: #333;
                transition: background 0.2s;
            }

            .nav a:hover,
            .nav a.active {
                background: #f8f9fa;
                color: #667eea;
            }

            .nav .user-info {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.5rem;
                background: #f8f9fa;
                border-top: 2px solid #e1e4e8;
                margin-top: auto;
            }

            .nav .user-info .welcome-msg {
                font-size: 1rem;
                color: #333;
            }

            .nav .user-info .user-badge {
                font-size: 0.85rem;
                background: #667eea;
            }

            .nav .user-info .btn-logout {
                width: 100%;
                text-align: center;
                padding: 0.8rem;
                background: #f44336;
                color: white !important;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                transition: background 0.2s;
            }

            .nav .user-info .btn-logout:hover {
                background: #d32f2f;
            }

            /* 오버레이 */
            .nav.active::after {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: -1;
            }
        }

        /* 반응형 - 모바일 */
        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav {
                position: fixed;
                top: 70px;
                right: -100%;
                width: 280px;
                height: calc(100vh - 70px);
                background: white;
                flex-direction: column;
                align-items: flex-start;
                padding: 2rem;
                gap: 0;
                box-shadow: -2px 0 10px rgba(0,0,0,0.1);
                transition: right 0.3s ease;
                overflow-y: auto;
            }

            .nav.active {
                right: 0;
            }

            .nav a {
                width: 100%;
                padding: 1rem 0;
                border-bottom: 1px solid #f0f0f0;
                font-size: 1.1rem;
            }

            .nav .user-info {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.5rem 0;
                border-top: 2px solid #e1e4e8;
                margin-top: 1rem;
            }

            .nav .user-info .welcome-msg {
                font-size: 1.1rem;
            }

            .nav .user-info .user-badge {
                font-size: 0.9rem;
            }

            .nav .user-info .btn-logout {
                width: 100%;
                text-align: center;
                padding: 0.8rem;
                background: #f44336;
                color: white !important;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
            }
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

/* ==================== 모바일 반응형 ==================== */
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
        background: #f44336 !important;
        color: white !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        border: none !important;
        margin: 0 !important;
    }
}

</style>
</head>
<body>
    <!-- 헤더 -->
    <header class="header">
        <div class="container">
            <div class="logo" onclick="location.href='index.php'" style="cursor:pointer">
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
                <a href="index.php" class="active">홈</a>
                <a href="study.php">스터디</a>
                <a href="jobs.php">채용공고</a>
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

    <!-- 메인 슬라이더 -->
    <section class="main-slider">
        <div class="slider-container">
            <!-- 첫 번째 슬라이드 - 1.공부.jpg -->
            <div class="slide active slide-1">
                <div class="slide-content">
                    <h2>Company Portal에 오신 것을 환영합니다</h2>
                    <p>직장인과 취업준비생을 위한 종합 커뮤니티 플랫폼</p>
                    <button class="slide-btn" onclick="location.href='study.php'">스터디 참여하기</button>
                </div>
            </div>
            <!-- 두 번째 슬라이드 - 2.면접.jpg -->
            <div class="slide slide-2">
                <div class="slide-content">
                    <h2>"최신 채용 정보를 한눈에"</h2>
                    <p>실시간 업데이트되는 채용 공고를 확인하세요</p>
                    <button class="slide-btn" onclick="location.href='jobs.php'">채용공고 보기</button>
                </div>
            </div>
            <!-- 세 번째 슬라이드 - 3.스터디.jpg -->
            <div class="slide slide-3">
                <div class="slide-content">
                    <h2>함께 성장하는 스터디</h2>
                    <p>다양한 분야의 스터디 모임에 참여하세요</p>
                    <button class="slide-btn" onclick="location.href='study.php'">스터디 둘러보기</button>
                </div>
            </div>
        </div>
        <button class="slider-arrow prev" onclick="changeSlide(-1)">❮</button>
        <button class="slider-arrow next" onclick="changeSlide(1)">❯</button>
        <div class="slider-controls">
            <span class="slider-dot active" onclick="goToSlide(0)"></span>
            <span class="slider-dot" onclick="goToSlide(1)"></span>
            <span class="slider-dot" onclick="goToSlide(2)"></span>
        </div>
    </section>

    <!-- 검색 섹션 -->
    <section class="search-section">
        <div class="search-container">
            <div class="search-box">
                <input type="text" class="search-input" id="mainSearch" placeholder="스터디, 채용공고, 게시글을 검색해보세요" onkeypress="if(event.key==='Enter') performSearch()">
                <button class="search-btn" onclick="performSearch()">검색</button>
            </div>
            <div class="quick-keywords">
                <span class="keyword" onclick="searchKeyword('스터디')">스터디</span>
                <span class="keyword" onclick="searchKeyword('네이버')">네이버</span>
                <span class="keyword" onclick="searchKeyword('카카오')">카카오</span>
                <span class="keyword" onclick="searchKeyword('면접')">면접</span>
                <span class="keyword" onclick="searchKeyword('이력서')">이력서</span>
                <span class="keyword" onclick="searchKeyword('React')">React</span>
                <span class="keyword" onclick="searchKeyword('Python')">Python</span>
            </div>
        </div>
    </section>

    <!-- 컨텐츠 섹션 -->
    <div class="content-section">
        <h2 class="section-title">인기 스터디</h2>
        <div class="cards-grid">
            <div class="content-card">
                <div class="card-header">
                    <h3>개발 스터디</h3>
                    <a href="study.php" class="card-link">더보기 →</a>
                </div>
                <div class="card-item" onclick="location.href='study.php'">
                    <div class="item-title">
                        <span class="badge badge-hot">HOT</span>
                        React 프론트엔드 스터디 (주 2회)
                    </div>
                    <div class="item-meta">
                        <span>👥 8/10명</span>
                        <span>📍 온라인</span>
                    </div>
                </div>
                <div class="card-item" onclick="location.href='study.php'">
                    <div class="item-title">
                        <span class="badge badge-new">NEW</span>
                        알고리즘 코딩테스트 준비반
                    </div>
                    <div class="item-meta">
                        <span>👥 5/10명</span>
                        <span>📍 강남역</span>
                    </div>
                </div>
                <div class="card-item" onclick="location.href='study.php'">
                    <div class="item-title">
                        Python 데이터 분석 스터디
                    </div>
                    <div class="item-meta">
                        <span>👥 6/8명</span>
                        <span>📍 온라인</span>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h3>최신 채용공고</h3>
                    <a href="jobs.php" class="card-link">더보기 →</a>
                </div>
                <div class="card-item" onclick="location.href='jobs.php'">
                    <div class="item-title">
                        <span class="badge badge-new">NEW</span>
                        네이버 백엔드 개발자
                    </div>
                    <div class="item-meta">
                        <span>📍 경기 성남</span>
                        <span>💰 협의</span>
                    </div>
                </div>
                <div class="card-item" onclick="location.href='jobs.php'">
                    <div class="item-title">
                        <span class="badge badge-new">NEW</span>
                        카카오 프론트엔드 개발자
                    </div>
                    <div class="item-meta">
                        <span>📍 경기 판교</span>
                        <span>💰 5000~7000</span>
                    </div>
                </div>
                <div class="card-item" onclick="location.href='jobs.php'">
                    <div class="item-title">
                        쿠팡 데이터 엔지니어
                    </div>
                    <div class="item-meta">
                        <span>📍 서울 송파</span>
                        <span>💰 6000~9000</span>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h3>익명게시판</h3>
                    <a href="board.php" class="card-link">더보기 →</a>
                </div>
                <div class="card-item" onclick="location.href='board.php'">
                    <div class="item-title">
                        <span class="badge badge-hot">HOT</span>
                        연봉 공개 상담합니다
                    </div>
                    <div class="item-meta">
                        <span>👍 243</span>
                        <span>💬 12건</span>
                    </div>
                </div>
                <div class="card-item" onclick="location.href='board.php'">
                    <div class="item-title">
                        <span class="badge badge-hot">HOT</span>
                        면접 준비 팁 공유합니다
                    </div>
                    <div class="item-meta">
                        <span>👍 182</span>
                        <span>💬 8건</span>
                    </div>
                </div>
                <div class="card-item" onclick="location.href='board.php'">
                    <div class="item-title">
                        직장에서 스트레스 해소법
                    </div>
                    <div class="item-meta">
                        <span>👍 493</span>
                        <span>💬 32건</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 푸터 -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Company Portal</h4>
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
                <p>📞 010-2681-9540</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 Company Community Portal. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // 슬라이더 기능
        let currentSlide = 0;
        let slideInterval;

        function showSlide(n) {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slider-dot');
            
            if (n >= slides.length) currentSlide = 0;
            if (n < 0) currentSlide = slides.length - 1;
            
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function changeSlide(n) {
            currentSlide += n;
            showSlide(currentSlide);
            resetSlideInterval();
        }

        function goToSlide(n) {
            currentSlide = n;
            showSlide(currentSlide);
            resetSlideInterval();
        }

        function resetSlideInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(() => {
                currentSlide++;
                showSlide(currentSlide);
            }, 5000);
        }

        // 자동 슬라이드 시작
        resetSlideInterval();

        // 검색 기능
        function performSearch() {
            const searchInput = document.getElementById('mainSearch');
            const query = searchInput.value.trim().toLowerCase();
            
            if (!query) {
                alert('검색어를 입력해주세요.');
                return;
            }
            
            // 스터디 관련 검색
            if (query.includes('스터디') || query.includes('study') || 
                query.includes('react') || query.includes('python') || 
                query.includes('알고리즘') || query.includes('코딩')) {
                window.location.href = 'study.php';
                return;
            }
            
            // 채용공고 관련 검색
            if (query.includes('채용') || query.includes('job') || 
                query.includes('공고') || query.includes('네이버') || 
                query.includes('카카오') || query.includes('쿠팡') ||
                query.includes('삼성') || query.includes('lg')) {
                window.location.href = 'jobs.php';
                return;
            }
            
            // 게시판 관련 검색
            if (query.includes('익명') || query.includes('게시판') || 
                query.includes('board') || query.includes('연봉') ||
                query.includes('면접') || query.includes('이력서')) {
                window.location.href = 'board.php';
                return;
            }
            
            // 취준생 공간 관련 검색
            if (query.includes('취준') || query.includes('취업') || 
                query.includes('jobseeker') || query.includes('자소서') ||
                query.includes('포트폴리오')) {
                window.location.href = 'jobseeker.php';
                return;
            }
            
            // 공지사항 관련 검색
            if (query.includes('공지') || query.includes('notice') ||
                query.includes('이벤트') || query.includes('업데이트')) {
                window.location.href = 'notice.php';
                return;
            }
            
            // 기본: 스터디 페이지로 이동
            alert(`"${query}" 검색 결과를 스터디 페이지에서 확인하세요.`);
            window.location.href = 'study.php';
        }

        // 키워드 검색
        function searchKeyword(keyword) {
            document.getElementById('mainSearch').value = keyword;
            performSearch();
        }

        
        // 햄버거 메뉴
        const hamburger = document.getElementById("hamburger");
        const nav = document.getElementById("nav");

        if (hamburger && nav) {
            hamburger.addEventListener("click", (e) => {
                e.stopPropagation();
                hamburger.classList.toggle("active");
                nav.classList.toggle("active");
                console.log("햄버거 클릭됨!");
            });

            const navLinks = document.querySelectorAll(".nav a");
            navLinks.forEach((link) => {
                link.addEventListener("click", () => {
                    if (window.innerWidth <= 768) {
                        hamburger.classList.remove("active");
                        nav.classList.remove("active");
                    }
                });
            });

            document.addEventListener("click", (e) => {
                if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
                    hamburger.classList.remove("active");
                    nav.classList.remove("active");
                }
            });
        }

console.log('✅ Company Portal loaded successfully!');
    </script>
</body>
</html>