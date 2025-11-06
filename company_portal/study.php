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
    <title>스터디 모집 - Company Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 스터디 레이아웃 */
        .study-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            padding: 30px 0;
        }

        /* 왼쪽 사이드바 */
        .study-sidebar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 20px;
            height: fit-content;
            position: sticky;
            top: 90px;
        }

        .sidebar-section {
            margin-bottom: 30px;
        }

        .sidebar-section:last-child {
            margin-bottom: 0;
        }

        .sidebar-section h3 {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }

        .category-list {
            list-style: none;
        }

        .category-list li {
            margin-bottom: 8px;
        }

        .category-list a {
            display: block;
            padding: 8px 12px;
            color: #666;
            font-size: 14px;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .category-list a:hover,
        .category-list a.active {
            background: #f8f9fa;
            color: #3366ff;
            font-weight: 500;
        }

        /* 사이드바 배너 */
        .sidebar-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar-banner:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .banner-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .sidebar-banner h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .sidebar-banner p {
            font-size: 13px;
            opacity: 0.9;
        }

        /* 오른쪽 컨텐츠 */
        .study-content {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 25px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f2f3f5;
        }

        .content-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .sort-options select {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 13px;
            color: #666;
            cursor: pointer;
        }

        /* 스터디 카드 그리드 */
        .study-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .study-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .study-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .study-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .study-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .study-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .study-desc {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .study-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: #666;
        }

        .study-info {
            font-size: 12px;
            color: #999;
        }

        /* 페이지네이션 */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #f2f3f5;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #e5e7eb;
            background: white;
            color: #666;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .page-btn:hover {
            border-color: #3366ff;
            color: #3366ff;
        }

        .page-btn.active {
            background: #3366ff;
            color: white;
            border-color: #3366ff;
        }

        /* 반응형 */
        @media (max-width: 1024px) {
            .study-layout {
                grid-template-columns: 1fr;
            }
            
            .study-sidebar {
                position: static;
            }
            
            .study-grid {
                grid-template-columns: 1fr;
            }
        }

        /* 모달 팝업 */
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

        .modal-study-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .modal-study-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .modal-subtitle {
            font-size: 15px;
            opacity: 0.95;
        }

        .modal-body {
            padding: 30px;
        }

        .info-section {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #f2f3f5;
        }

        .info-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-section h3 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .study-description {
            line-height: 1.8;
            color: #666;
            font-size: 14px;
        }

        /* 신청 폼 */
        .application-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-label .required {
            color: #ff6b6b;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3366ff;
            box-shadow: 0 0 0 3px rgba(51, 102, 255, 0.1);
        }

        textarea.form-input {
            min-height: 120px;
            resize: vertical;
            font-family: inherit;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-cancel {
            background: #e5e7eb;
            color: #666;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #d1d5db;
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
                    <a href="study.php" class="active">스터디</a>
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

    <main class="main-content">
        <div class="container">
            <div class="study-layout">
                <!-- 왼쪽 사이드바 -->
                <aside class="study-sidebar">
                    <div class="sidebar-section">
                        <h3>스터디 분야</h3>
                        <ul class="category-list">
                            <li><a href="study.php" class="active">전체 스터디</a></li>
                            <li><a href="study.php?type=개발">💻 개발 스터디</a></li>
                            <li><a href="study.php?type=어학">🌏 어학 스터디</a></li>
                            <li><a href="study.php?type=자격증">📜 자격증 스터디</a></li>
                            <li><a href="study.php?type=면접">🎯 면접 준비</a></li>
                            <li><a href="study.php?type=알고리즘">🧮 알고리즘</a></li>
                        </ul>
                    </div>
                    
                    <div class="sidebar-section">
                        <h3>지역</h3>
                        <ul class="category-list">
                            <li><a href="study.php?location=online">🌐 온라인</a></li>
                            <li><a href="study.php?location=서울">서울</a></li>
                            <li><a href="study.php?location=강남">강남</a></li>
                            <li><a href="study.php?location=홍대">홍대</a></li>
                            <li><a href="study.php?location=경기">경기</a></li>
                        </ul>
                    </div>
                    
                    <div class="sidebar-section">
                        <h3>모집 현황</h3>
                        <ul class="category-list">
                            <li><a href="study.php?status=모집중">모집중</a></li>
                            <li><a href="study.php?status=마감">모집완료</a></li>
                        </ul>
                    </div>
                    
                    <!-- 스터디톡톡 배너 -->
                    <div class="sidebar-banner" onclick="location.href='mailto:nosu0320@naver.com'">
                        <div class="banner-icon">🐥</div>
                        <h4>스터디 고민은<br>스터디톡톡</h4>
                        <p>현직 멘토에게 질문하세요!</p>
                    </div>
                </aside>
                
                <!-- 오른쪽 컨텐츠 영역 -->
                <div class="study-content">
                    <div class="content-header">
                        <h2>📚 인기 스터디</h2>
                        <div class="sort-options">
                            <select onchange="location.href=this.value">
                                <option value="study.php?sort=recent">최근 등록순</option>
                                <option value="study.php?sort=popular">인기순</option>
                                <option value="study.php?sort=deadline">마감임박순</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- 스터디 카드 그리드 -->
                    <div class="study-grid">
                        <!-- ========== 페이지 1: 모집 중 (8개) ========== -->
                        
                        <!-- 모집중 1 -->
                        <div class="study-card" data-page="1" onclick="openModal('react')">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg" alt="React">
                            </div>
                            <h3 class="study-title">React 프론트엔드 스터디</h3>
                            <p class="study-desc">주말 오전 스터디로 React 스킬업!</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 5/8명</span>
                            </div>
                            <div class="study-info">
                                📍 강남역 • 📅 주 1회
                            </div>
                        </div>
                        
                        <!-- 모집중 2 -->
                        <div class="study-card" data-page="1" onclick="openModal('python')">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg" alt="Python">
                            </div>
                            <h3 class="study-title">Python 데이터 분석 스터디</h3>
                            <p class="study-desc">데이터 분석가를 위한 실전 스터디</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 6/10명</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 주 2회
                            </div>
                        </div>
                        
                        <!-- 모집중 3 -->
                        <div class="study-card" data-page="1" onclick="openModal('algorithm')">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="알고리즘">
                            </div>
                            <h3 class="study-title">알고리즘 코딩테스트 준비반</h3>
                            <p class="study-desc">백준/프로그래머스 문제 풀이 스터디</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 5/10명</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 주 3회
                            </div>
                        </div>
                        
                        <!-- 모집중 4 -->
                        <div class="study-card" data-page="1" onclick="openModal('java')">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java">
                            </div>
                            <h3 class="study-title">Java 백엔드 개발 스터디</h3>
                            <p class="study-desc">Spring Boot 프로젝트 실습 중심</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 7/8명</span>
                            </div>
                            <div class="study-info">
                                📍 판교 • 📅 주 2회
                            </div>
                        </div>
                        
                        <!-- 모집중 5 -->
                        <div class="study-card" data-page="1" onclick="openModal('typescript')">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg" alt="TypeScript">
                            </div>
                            <h3 class="study-title">TypeScript 마스터 스터디</h3>
                            <p class="study-desc">실전 프로젝트로 배우는 타입스크립트</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 6/8명</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 주 2회
                            </div>
                        </div>
                        
                        <!-- 모집중 6 -->
                        <div class="study-card" data-page="1" onclick="openModal('kotlin')">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/kotlin/kotlin-original.svg" alt="Kotlin">
                            </div>
                            <h3 class="study-title">Kotlin 안드로이드 개발</h3>
                            <p class="study-desc">Android 앱 개발 실습</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 7/10명</span>
                            </div>
                            <div class="study-info">
                                📍 홍대 스터디카페 • 📅 주 2회
                            </div>
                        </div>

                        <!-- 모집중 7 -->
                        <div class="study-card" data-page="1" onclick="openModal('git')">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg" alt="Git">
                            </div>
                            <h3 class="study-title">Git/GitHub 협업 스터디</h3>
                            <p class="study-desc">Git 브랜치 전략과 협업 워크플로우</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 8/15명</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 주 1회
                            </div>
                        </div>

                        <!-- 모집중 8 -->
                        <div class="study-card" data-page="1" onclick="openModal('toeic900')">
                            <div class="study-logo" style="font-size: 40px; color: #10b981;">
                                📚
                            </div>
                            <h3 class="study-title">토익 900+ 달성반</h3>
                            <p class="study-desc">토익 고득점 집중 스터디</p>
                            <div class="study-meta">
                                <span class="badge badge-hot">모집중</span>
                                <span>👥 9/12명</span>
                            </div>
                            <div class="study-info">
                                📍 강남역 스터디카페 • 📅 주 3회
                            </div>
                        </div>

                        <!-- ========== 페이지 2: 모집 예정 (8개) ========== -->
                        
                        <!-- 모집예정 1 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg" alt="Vue.js">
                            </div>
                            <h3 class="study-title">Vue.js 3 실전 스터디</h3>
                            <p class="study-desc">최신 Vue 3 Composition API 마스터</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 12월 시작 예정
                            </div>
                        </div>

                        <!-- 모집예정 2 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #ff9900;">
                                ☁️
                            </div>
                            <h3 class="study-title">AWS 클라우드 자격증 스터디</h3>
                            <p class="study-desc">AWS Solutions Architect 자격증 준비</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 1월 시작 예정
                            </div>
                        </div>

                        <!-- 모집예정 3 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/flutter/flutter-original.svg" alt="Flutter">
                            </div>
                            <h3 class="study-title">Flutter 크로스플랫폼 개발</h3>
                            <p class="study-desc">하나의 코드로 iOS/Android 동시 개발</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 12월 중순 시작
                            </div>
                        </div>

                        <!-- 모집예정 4 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #0db7ed;">
                                🔧
                            </div>
                            <h3 class="study-title">DevOps 엔지니어링 스터디</h3>
                            <p class="study-desc">CI/CD 파이프라인 구축</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 2025년 2월 시작
                            </div>
                        </div>

                        <!-- 모집예정 5 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #000;">
                                ▲
                            </div>
                            <h3 class="study-title">Next.js 풀스택 개발</h3>
                            <p class="study-desc">Next.js 14 App Router 완전 정복</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 1월 초 시작
                            </div>
                        </div>

                        <!-- 모집예정 6 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #e535ab;">
                                📊
                            </div>
                            <h3 class="study-title">GraphQL API 개발 스터디</h3>
                            <p class="study-desc">GraphQL로 효율적인 API 만들기</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 12월 말 시작
                            </div>
                        </div>

                        <!-- 모집예정 7 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg" alt="Docker">
                            </div>
                            <h3 class="study-title">Docker & Kubernetes 실전</h3>
                            <p class="study-desc">컨테이너 오케스트레이션 마스터</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 1월 중순 시작
                            </div>
                        </div>

                        <!-- 모집예정 8 -->
                        <div class="study-card" data-page="2" onclick="alert('모집 예정인 스터디입니다. 나중에 다시 이용해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #3b82f6;">
                                🗣️
                            </div>
                            <h3 class="study-title">오픽 AL 목표 스터디</h3>
                            <p class="study-desc">3개월 완성 오픽 고득점 프로그램</p>
                            <div class="study-meta">
                                <span class="badge badge-new">모집예정</span>
                                <span>👥 곧 모집</span>
                            </div>
                            <div class="study-info">
                                📍 강남 스터디카페 • 📅 1월 시작
                            </div>
                        </div>

                        <!-- ========== 페이지 3: 모집 마감 (8개) ========== -->
                        
                        <!-- 모집마감 1 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg" alt="Node.js">
                            </div>
                            <h3 class="study-title">Node.js 백엔드 심화</h3>
                            <p class="study-desc">Express와 NestJS 실전 프로젝트</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 10/10명</span>
                            </div>
                            <div class="study-info">
                                📍 홍대 • 📅 매주 토요일
                            </div>
                        </div>

                        <!-- 모집마감 2 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg" alt="Figma">
                            </div>
                            <h3 class="study-title">UI/UX 디자인 포트폴리오</h3>
                            <p class="study-desc">Figma 실무 프로젝트 진행</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 5/5명</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 주 1회
                            </div>
                        </div>

                        <!-- 모집마감 3 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/swift/swift-original.svg" alt="Swift">
                            </div>
                            <h3 class="study-title">Swift iOS 앱 개발</h3>
                            <p class="study-desc">SwiftUI로 만드는 모던 iOS 앱</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 8/8명</span>
                            </div>
                            <div class="study-info">
                                📍 강남 • 📅 주 2회
                            </div>
                        </div>

                        <!-- 모집마감 4 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #336791;">
                                🗄️
                            </div>
                            <h3 class="study-title">SQL 데이터베이스 마스터</h3>
                            <p class="study-desc">MySQL/PostgreSQL 실전 활용</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 12/12명</span>
                            </div>
                            <div class="study-info">
                                📍 강남역 스터디카페 • 📅 주 2회
                            </div>
                        </div>

                        <!-- 모집마감 5 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #e74c3c;">
                                🇯🇵
                            </div>
                            <h3 class="study-title">JLPT N1 합격반</h3>
                            <p class="study-desc">일본어 능력시험 최고 등급 도전</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 10/10명</span>
                            </div>
                            <div class="study-info">
                                📍 홍대 • 📅 주 3회
                            </div>
                        </div>

                        <!-- 모집마감 6 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #ff6b6b;">
                                📜
                            </div>
                            <h3 class="study-title">정보처리기사 자격증</h3>
                            <p class="study-desc">2025년 상반기 자격증 합격 목표</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 15/15명</span>
                            </div>
                            <div class="study-info">
                                📍 강남역 • 📅 주 3회
                            </div>
                        </div>

                        <!-- 모집마감 7 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo">
                                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/rust/rust-plain.svg" alt="Rust">
                            </div>
                            <h3 class="study-title">Rust 시스템 프로그래밍</h3>
                            <p class="study-desc">안전하고 빠른 시스템 개발</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 6/6명</span>
                            </div>
                            <div class="study-info">
                                📍 온라인 • 📅 주 1회
                            </div>
                        </div>

                        <!-- 모집마감 8 -->
                        <div class="study-card" data-page="3" onclick="alert('모집이 마감되었습니다! 모집 중인 스터디를 확인해주세요.'); return false;">
                            <div class="study-logo" style="font-size: 40px; color: #3366ff;">
                                🌏
                            </div>
                            <h3 class="study-title">토익 스피킹 집중반</h3>
                            <p class="study-desc">토익 스피킹 레벨6 이상 목표</p>
                            <div class="study-meta">
                                <span class="badge badge-closed">모집마감</span>
                                <span>👥 6/6명</span>
                            </div>
                            <div class="study-info">
                                📍 홍대입구 • 📅 주 2회
                            </div>
                        </div>
                    </div>

                    <!-- 페이지네이션 -->
                    <div class="pagination">
                        <button class="page-btn active" onclick="changePage(1)">1</button>
                        <button class="page-btn" onclick="changePage(2)">2</button>
                        <button class="page-btn" onclick="changePage(3)">3</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 스터디 상세 모달 -->
    <div id="studyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <button class="modal-close" onclick="closeModal()">&times;</button>
                <div class="modal-study-logo" id="modalLogo"></div>
                <h2 class="modal-title" id="modalTitle"></h2>
                <p class="modal-subtitle" id="modalSubtitle"></p>
            </div>
            
            <div class="modal-body">
                <!-- 스터디 정보 -->
                <div class="info-section">
                    <h3>📋 스터디 정보</h3>
                    <div class="info-row">
                        <span class="info-label">모집 인원</span>
                        <span class="info-value" id="modalMembers"></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">진행 장소</span>
                        <span class="info-value" id="modalLocation"></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">진행 일정</span>
                        <span class="info-value" id="modalSchedule"></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">모집 상태</span>
                        <span class="info-value" id="modalStatus"></span>
                    </div>
                </div>

                <!-- 스터디 상세 설명 -->
                <div class="info-section">
                    <h3>상세 설명</h3>
                    <div class="study-description" id="modalDescription"></div>
                </div>

                <!-- 신청 폼 -->
                <div class="application-form">
                    <h3 style="margin-bottom: 20px; font-size: 18px; color: #333;">스터디 신청하기</h3>
                    
                    <form id="applicationForm" onsubmit="submitApplication(event)">
                        <div class="form-group">
                            <label class="form-label">이름 <span class="required">*</span></label>
                            <input type="text" class="form-input" name="name" placeholder="이름을 입력하세요" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">이메일 <span class="required">*</span></label>
                            <input type="email" class="form-input" name="email" placeholder="email@example.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">전화번호 <span class="required">*</span></label>
                            <input type="tel" class="form-input" name="phone" placeholder="010-1234-5678" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">자기소개 <span class="required">*</span></label>
                            <textarea class="form-input" name="introduction" placeholder="스터디에 참여하고 싶은 이유와 각오를 작성해주세요. (최소 50자)" required></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="closeModal()">취소</button>
                            <button type="submit" class="btn-submit">신청하기</button>
                        </div>
                    </form>
                </div>
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
                    <p>📞 010-2681-9540</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 Company Community Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // 스터디 데이터
        const studyData = {
            // ========== 모집 중 (8개) ==========
            react: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg" alt="React">',
                title: 'React 프론트엔드 스터디',
                subtitle: '주말 오전 스터디로 React 스킬업!',
                members: '5/8명',
                location: '강남역 스터디카페',
                schedule: '주 1회 (토요일 오전 10시)',
                status: '모집중',
                description: `React 프론트엔드 개발 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • React Hooks 심화<br>
                • 상태 관리 (Redux, Recoil)<br>
                • 성능 최적화 기법<br>
                • 실전 프로젝트 진행<br><br>
                <strong>📌 참여 대상:</strong><br>
                • React 기초를 아시는 분<br>
                • 실무 수준의 프로젝트 경험을 쌓고 싶으신 분`
            },
            python: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg" alt="Python">',
                title: 'Python 데이터 분석 스터디',
                subtitle: '데이터 분석가를 위한 실전 스터디',
                members: '6/10명',
                location: '온라인 (Zoom)',
                schedule: '주 2회 (화, 목 오후 8시)',
                status: '모집중',
                description: `Python을 활용한 데이터 분석 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Pandas, NumPy 마스터<br>
                • 데이터 시각화 (Matplotlib, Seaborn)<br>
                • 머신러닝 기초<br>
                • 실제 데이터셋 분석 프로젝트<br><br>
                <strong>📌 참여 대상:</strong><br>
                • Python 기초 문법을 아시는 분<br>
                • 데이터 분석 실무 역량을 키우고 싶으신 분`
            },
            algorithm: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="알고리즘">',
                title: '알고리즘 코딩테스트 준비반',
                subtitle: '백준/프로그래머스 문제 풀이 스터디',
                members: '5/10명',
                location: '온라인',
                schedule: '주 3회 (월, 수, 금 오후 9시)',
                status: '모집중',
                description: `코딩테스트 합격을 위한 알고리즘 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • 자료구조 완벽 정리<br>
                • 필수 알고리즘 패턴 학습<br>
                • 매일 1문제 이상 풀이<br>
                • 주간 모의 코딩테스트<br><br>
                <strong>📌 참여 대상:</strong><br>
                • 취업/이직을 준비하시는 분<br>
                • 알고리즘 기초부터 탄탄히 다지고 싶으신 분`
            },
            java: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java">',
                title: 'Java 백엔드 개발 스터디',
                subtitle: 'Spring Boot 프로젝트 실습 중심',
                members: '7/8명',
                location: '판교 스타트업캠퍼스',
                schedule: '주 2회 (수, 토 오후 2시)',
                status: '모집중',
                description: `Java Spring Boot 백엔드 개발 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Spring Boot 핵심 개념<br>
                • JPA & 데이터베이스 설계<br>
                • RESTful API 개발<br>
                • 실전 프로젝트 (쇼핑몰 구축)<br><br>
                <strong>📌 참여 대상:</strong><br>
                • Java 기본 문법을 아시는 분<br>
                • 백엔드 개발자로 취업을 준비하시는 분`
            },
            typescript: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg" alt="TypeScript">',
                title: 'TypeScript 마스터 스터디',
                subtitle: '실전 프로젝트로 배우는 타입스크립트',
                members: '6/8명',
                location: '온라인',
                schedule: '주 2회 (화, 목 오후 8시)',
                status: '모집중',
                description: `TypeScript를 제대로 배우는 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • TypeScript 기초부터 고급까지<br>
                • 타입 시스템 완벽 이해<br>
                • Generic & Utility Types<br>
                • React + TypeScript 프로젝트<br><br>
                <strong>📌 참여 대상:</strong><br>
                • JavaScript에 익숙하신 분<br>
                • TypeScript를 실무에 활용하고 싶으신 분`
            },
            kotlin: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/kotlin/kotlin-original.svg" alt="Kotlin">',
                title: 'Kotlin 안드로이드 개발',
                subtitle: 'Android 앱 개발 실습',
                members: '7/10명',
                location: '홍대 스터디카페',
                schedule: '주 2회 (월, 수 오후 8시)',
                status: '모집중',
                description: `Kotlin으로 안드로이드 앱을 개발하는 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Kotlin 언어 마스터<br>
                • Jetpack Compose UI<br>
                • MVVM 아키텍처 패턴<br>
                • Play Store 앱 출시<br><br>
                <strong>📌 참여 대상:</strong><br>
                • 안드로이드 개발에 관심 있으신 분<br>
                • 앱 개발 포트폴리오를 만들고 싶으신 분`
            },
            git: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg" alt="Git">',
                title: 'Git/GitHub 협업 스터디',
                subtitle: 'Git 브랜치 전략과 협업 워크플로우',
                members: '8/15명',
                location: '온라인',
                schedule: '주 1회 (토요일 오후 2시)',
                status: '모집중',
                description: `Git/GitHub 협업 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Git 명령어 완벽 마스터<br>
                • 브랜치 전략 (Git Flow, GitHub Flow)<br>
                • Pull Request & Code Review<br>
                • GitHub Actions CI/CD<br><br>
                <strong>📌 참여 대상:</strong><br>
                • Git 기초를 아시는 분<br>
                • 팀 프로젝트 협업 능력을 키우고 싶으신 분`
            },
            toeic900: {
                logo: '<div style="font-size: 50px; color: #10b981;">📚</div>',
                title: '토익 900+ 달성반',
                subtitle: '토익 고득점 집중 스터디',
                members: '9/12명',
                location: '강남역 스터디카페',
                schedule: '주 3회 (월, 수, 금 오후 7시)',
                status: '모집중',
                description: `토익 900점 이상을 목표로 하는 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • 파트별 전략 학습<br>
                • 매일 단어 테스트<br>
                • 주 2회 실전 모의고사<br>
                • 오답 노트 공유 및 분석<br><br>
                <strong>📌 참여 대상:</strong><br>
                • 토익 800점 이상이신 분<br>
                • 900점 이상 고득점을 목표로 하시는 분`
            },

            // ========== 모집 예정 (8개) ==========
            vue: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg" alt="Vue.js">',
                title: 'Vue.js 3 실전 스터디',
                subtitle: '최신 Vue 3 Composition API 마스터',
                members: '곧 모집',
                location: '온라인 (Discord)',
                schedule: '주 2회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `Vue.js 3 프론트엔드 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Vue 3 Composition API<br>
                • Pinia 상태 관리<br>
                • Vue Router 심화<br>
                • 실전 웹 애플리케이션 개발<br><br>
                <strong>📅 시작 예정일: 2024년 12월</strong>`
            },
            aws: {
                logo: '<div style="font-size: 50px; color: #ff9900;">☁️</div>',
                title: 'AWS 클라우드 자격증 스터디',
                subtitle: 'AWS Solutions Architect 자격증 준비',
                members: '곧 모집',
                location: '온라인',
                schedule: '주 2회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `AWS Solutions Architect Associate 자격증 준비 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • AWS 핵심 서비스 학습 (EC2, S3, RDS 등)<br>
                • 아키텍처 설계 패턴<br>
                • 모의고사 및 문제 풀이<br>
                • 실습 프로젝트<br><br>
                <strong>📅 시작 예정일: 2025년 1월</strong>`
            },
            flutter: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/flutter/flutter-original.svg" alt="Flutter">',
                title: 'Flutter 크로스플랫폼 개발',
                subtitle: '하나의 코드로 iOS/Android 동시 개발',
                members: '곧 모집',
                location: '온라인',
                schedule: '주 2회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `Flutter 모바일 앱 개발 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Flutter 위젯 마스터<br>
                • 상태 관리 (Provider, Riverpod)<br>
                • Firebase 연동<br>
                • 앱 스토어/플레이스토어 출시<br><br>
                <strong>📅 시작 예정일: 2024년 12월 중순</strong>`
            },
            devops: {
                logo: '<div style="font-size: 50px; color: #0db7ed;">🔧</div>',
                title: 'DevOps 엔지니어링 스터디',
                subtitle: 'CI/CD 파이프라인 구축',
                members: '곧 모집',
                location: '온라인',
                schedule: '주 2회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `DevOps 실무 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Jenkins/GitHub Actions<br>
                • Docker & Kubernetes<br>
                • AWS/GCP 배포 자동화<br>
                • 모니터링 & 로깅 시스템<br><br>
                <strong>📅 시작 예정일: 2025년 2월</strong>`
            },
            nextjs: {
                logo: '<div style="font-size: 50px; color: #000;">▲</div>',
                title: 'Next.js 풀스택 개발',
                subtitle: 'Next.js 14 App Router 완전 정복',
                members: '곧 모집',
                location: '온라인',
                schedule: '주 2회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `Next.js 풀스택 개발 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Next.js 14 App Router<br>
                • Server Components & Actions<br>
                • Prisma ORM & 데이터베이스<br>
                • Vercel 배포 및 최적화<br><br>
                <strong>📅 시작 예정일: 2025년 1월 초</strong>`
            },
            graphql: {
                logo: '<div style="font-size: 50px; color: #e535ab;">📊</div>',
                title: 'GraphQL API 개발 스터디',
                subtitle: 'GraphQL로 효율적인 API 만들기',
                members: '곧 모집',
                location: '온라인 (Discord)',
                schedule: '주 1회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `GraphQL API 개발 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • GraphQL 기초 개념<br>
                • Schema & Resolver 설계<br>
                • Apollo Server/Client<br>
                • 실전 프로젝트<br><br>
                <strong>📅 시작 예정일: 2024년 12월 말</strong>`
            },
            docker: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg" alt="Docker">',
                title: 'Docker & Kubernetes 실전',
                subtitle: '컨테이너 오케스트레이션 마스터',
                members: '곧 모집',
                location: '온라인',
                schedule: '주 2회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `Docker와 Kubernetes 실전 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Docker 컨테이너 기초<br>
                • Docker Compose 활용<br>
                • Kubernetes 클러스터 구축<br>
                • 마이크로서비스 배포<br><br>
                <strong>📅 시작 예정일: 2025년 1월 중순</strong>`
            },
            opic: {
                logo: '<div style="font-size: 50px; color: #3b82f6;">🗣️</div>',
                title: '오픽 AL 목표 스터디',
                subtitle: '3개월 완성 오픽 고득점 프로그램',
                members: '곧 모집',
                location: '강남 스터디카페',
                schedule: '주 2회 (예정)',
                status: '모집예정',
                statusClass: 'badge-new',
                description: `오픽 AL 등급을 목표로 하는 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • 주제별 답변 템플릿 구축<br>
                • 실전 스피킹 연습<br>
                • 모의 오픽 시험<br>
                • 발음 교정 및 피드백<br><br>
                <strong>📅 시작 예정일: 2025년 1월</strong>`
            },

            // ========== 모집 마감 (8개) ==========
            nodejs: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg" alt="Node.js">',
                title: 'Node.js 백엔드 심화',
                subtitle: 'Express와 NestJS 실전 프로젝트',
                members: '10/10명',
                location: '홍대 스터디카페',
                schedule: '매주 토요일 오후 2시',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `Node.js 백엔드 심화 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Express & NestJS 프레임워크<br>
                • RESTful API 설계<br>
                • MongoDB/PostgreSQL<br>
                • JWT 인증 & 보안<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            },
            figma: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg" alt="Figma">',
                title: 'UI/UX 디자인 포트폴리오',
                subtitle: 'Figma 실무 프로젝트 진행',
                members: '5/5명',
                location: '온라인',
                schedule: '주 1회 (일요일 오후 3시)',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `UI/UX 디자인 포트폴리오 제작 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Figma 기초부터 고급까지<br>
                • UX 리서치 & 와이어프레임<br>
                • 프로토타이핑<br>
                • 실전 프로젝트 및 포트폴리오<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            },
            swift: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/swift/swift-original.svg" alt="Swift">',
                title: 'Swift iOS 앱 개발',
                subtitle: 'SwiftUI로 만드는 모던 iOS 앱',
                members: '8/8명',
                location: '강남 스터디카페',
                schedule: '주 2회 (화, 목 오후 7시)',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `iOS Swift 앱 개발 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Swift 언어 기초<br>
                • SwiftUI 프레임워크<br>
                • 앱스토어 출시 과정<br>
                • 실전 프로젝트<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            },
            sql: {
                logo: '<div style="font-size: 50px; color: #336791;">🗄️</div>',
                title: 'SQL 데이터베이스 마스터',
                subtitle: 'MySQL/PostgreSQL 실전 활용',
                members: '12/12명',
                location: '강남역 스터디카페',
                schedule: '주 2회 (화, 목 오후 7시)',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `SQL 데이터베이스 심화 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • SQL 고급 쿼리<br>
                • Index 최적화<br>
                • 트랜잭션 & 동시성 제어<br>
                • 실무 프로젝트<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            },
            jlpt: {
                logo: '<div style="font-size: 50px; color: #e74c3c;">🇯🇵</div>',
                title: 'JLPT N1 합격반',
                subtitle: '일본어 능력시험 최고 등급 도전',
                members: '10/10명',
                location: '홍대 스터디카페',
                schedule: '주 3회 (월, 수, 금 오후 7시)',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `JLPT N1 합격을 목표로 하는 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • N1 필수 한자/어휘<br>
                • 문법 완벽 정리<br>
                • 독해/청해 실전 연습<br>
                • 주간 모의고사<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            },
            engineer: {
                logo: '<div style="font-size: 50px; color: #ff6b6b;">📜</div>',
                title: '정보처리기사 자격증',
                subtitle: '2025년 상반기 자격증 합격 목표',
                members: '15/15명',
                location: '강남역 스터디카페',
                schedule: '주 3회 (월, 수, 금 오후 7시)',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `정보처리기사 자격증 취득 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • 전과목 이론 정리<br>
                • 기출문제 풀이<br>
                • 실기 프로그래밍 연습<br>
                • 모의고사<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            },
            rust: {
                logo: '<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/rust/rust-plain.svg" alt="Rust">',
                title: 'Rust 시스템 프로그래밍',
                subtitle: '안전하고 빠른 시스템 개발',
                members: '6/6명',
                location: '온라인',
                schedule: '주 1회 (일요일 오후 8시)',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `Rust 시스템 프로그래밍 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • Rust 소유권 시스템<br>
                • 메모리 안전성<br>
                • 동시성 프로그래밍<br>
                • CLI 도구 개발<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            },
            toeic_speaking: {
                logo: '<div style="font-size: 50px; color: #3366ff;">🌏</div>',
                title: '토익 스피킹 집중반',
                subtitle: '토익 스피킹 레벨6 이상 목표',
                members: '6/6명',
                location: '홍대입구 스터디카페',
                schedule: '주 2회 (화, 목 오후 7시)',
                status: '모집마감',
                statusClass: 'badge-closed',
                description: `토익 스피킹 고득점 스터디입니다.<br><br>
                <strong>📌 스터디 내용:</strong><br>
                • 파트별 답변 템플릿<br>
                • 실전 스피킹 연습<br>
                • 발음 교정<br>
                • 모의고사<br><br>
                <strong>⚠️ 현재 모집이 마감되었습니다.</strong>`
            }
        };

        // 모달 열기
        function openModal(studyId) {
            const study = studyData[studyId];
            if (!study) return;

            document.getElementById('modalLogo').innerHTML = study.logo;
            document.getElementById('modalTitle').textContent = study.title;
            document.getElementById('modalSubtitle').textContent = study.subtitle;
            document.getElementById('modalMembers').textContent = study.members;
            document.getElementById('modalLocation').textContent = study.location;
            document.getElementById('modalSchedule').textContent = study.schedule;
            
            // 상태 배지 설정
            const statusClass = study.statusClass || 'badge-hot';
            document.getElementById('modalStatus').innerHTML = '<span class="badge ' + statusClass + '">' + study.status + '</span>';
            
            document.getElementById('modalDescription').innerHTML = study.description;

            document.getElementById('studyModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // 모달 닫기
        function closeModal() {
            document.getElementById('studyModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('applicationForm').reset();
        }

        // 모달 외부 클릭시 닫기
        document.getElementById('studyModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // 신청 폼 제출
        function submitApplication(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = {
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                introduction: formData.get('introduction')
            };

            // 자기소개 글자수 체크
            if (data.introduction.length < 50) {
                alert('자기소개는 최소 50자 이상 작성해주세요.');
                return;
            }

            // 여기서 실제로는 서버에 데이터를 전송해야 합니다
            console.log('신청 데이터:', data);
            
            alert(`✅ 스터디 신청이 완료되었습니다!\n\n담당자가 확인 후 ${data.email}로 연락드리겠습니다.`);
            closeModal();
        }

        // 페이지 변경
        function changePage(page) {
            // 모든 카드 숨기기
            document.querySelectorAll('.study-card').forEach(card => {
                card.style.display = 'none';
            });
            
            // 해당 페이지 카드만 보이기
            document.querySelectorAll(`[data-page="${page}"]`).forEach(card => {
                card.style.display = 'block';
            });
            
            // 페이지 버튼 활성화 상태 변경
            document.querySelectorAll('.page-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // 스크롤 맨 위로
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        // 페이지 로드시 1페이지 카드만 표시
        document.addEventListener('DOMContentLoaded', function() {
            changePage(1);
            document.querySelector('.page-btn').classList.add('active');
        });

        console.log('✅ Study modal loaded!');
    </script>
</body>
</html>