<?php
require_once 'db.php';
requireLogin();

$user = getCurrentUser();

// 채용 공고 데이터
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$jobs = [
    1 => [
        'company' => '네이버',
        'logo' => '🟢',
        'position' => '백엔드 개발자',
        'type' => 'IT/개발',
        'experience' => '경력 3년 이상',
        'education' => '대졸 이상',
        'employment_type' => '정규직',
        'location' => '경기 성남시 분당구 정자동',
        'salary' => '연봉 협의 (경력에 따라 차등)',
        'deadline' => '2025.12.31',
        'tech_stack' => ['Java', 'Spring Boot', 'MySQL', 'Redis', 'Kafka', 'MSA', 'Docker', 'Kubernetes'],
        'description' => '네이버의 핵심 서비스를 개발하고 운영할 백엔드 개발자를 모집합니다. 대규모 트래픽을 처리하는 안정적인 서비스를 만들고, 최신 기술을 적용하며 성장할 수 있는 기회를 제공합니다.',
        'requirements' => [
            'Java, Spring Framework 기반 웹 서비스 개발 경험 3년 이상',
            'RDBMS(MySQL, PostgreSQL 등) 및 NoSQL 사용 경험',
            'RESTful API 설계 및 개발 경험',
            'MSA(Microservices Architecture)에 대한 이해',
            '우수한 커뮤니케이션 능력 및 협업 능력'
        ],
        'preferred' => [
            'Kubernetes, Docker 등 컨테이너 기술 실무 경험',
            '대용량 트래픽 처리 및 성능 최적화 경험',
            'Message Queue(Kafka, RabbitMQ 등) 사용 경험',
            '오픈소스 프로젝트 기여 경험',
            'CI/CD 파이프라인 구축 경험'
        ],
        'benefits' => [
            '연봉 외 성과급',
            '4대 보험',
            '자기계발비 연 200만원 지원',
            '유연근무제',
            '재택근무 주 2회 가능',
            '중식/석식 제공',
            '건강검진',
            '경조사 지원',
            '사내 카페테리아',
            '최신형 맥북 프로 지급',
            '컨퍼런스 참가 지원',
            '도서 구입비 무제한'
        ],
        'process' => [
            '1. 서류 전형',
            '2. 코딩 테스트',
            '3. 1차 면접 (기술)',
            '4. 2차 면접 (임원)',
            '5. 최종 합격'
        ],
        'jobkorea_url' => 'https://www.jobkorea.co.kr'
    ],
    2 => [
        'company' => '카카오',
        'logo' => '💛',
        'position' => '프론트엔드 개발자',
        'type' => 'IT/개발',
        'experience' => '경력 3년 이상',
        'education' => '대졸 이상',
        'employment_type' => '정규직',
        'location' => '경기 성남시 분당구 판교역로',
        'salary' => '연봉 5,000만원 ~ 7,000만원',
        'deadline' => '2025.11.30',
        'tech_stack' => ['React', 'TypeScript', 'Next.js', 'Redux', 'Webpack', 'Git'],
        'description' => '카카오의 다양한 웹 서비스를 개발하고 혁신할 프론트엔드 개발자를 찾습니다. 사용자 경험을 최우선으로 생각하며, 최신 기술 트렌드를 빠르게 적용하는 환경에서 일할 수 있습니다.',
        'requirements' => [
            'React 기반 웹 애플리케이션 개발 경력 3년 이상',
            'TypeScript 사용 경험 및 능숙한 활용',
            'HTML5, CSS3, JavaScript(ES6+) 능숙',
            'RESTful API 연동 및 상태 관리 경험',
            '크로스 브라우징 이슈 해결 능력'
        ],
        'preferred' => [
            'Next.js, Vue.js 등 다양한 프레임워크 경험',
            '웹 성능 최적화 및 모니터링 경험',
            'UI/UX 디자인에 대한 깊은 이해',
            '반응형 웹 및 모바일 웹 개발 경험',
            'GraphQL 사용 경험'
        ],
        'benefits' => [
            '업계 최고 수준 연봉',
            '스톡옵션(Stock Option)',
            '자유로운 연차 사용',
            '점심/저녁 제공 (식대 무제한)',
            '사내 카페 무료 이용',
            '피트니스 센터 무료',
            '도서 구입비 지원',
            '최신형 맥북 프로 지급',
            '업무용 듀얼 모니터',
            '컨퍼런스 참가비 전액 지원',
            '야근 택시비 지원',
            '생일 휴가'
        ],
        'process' => [
            '1. 서류 전형',
            '2. 온라인 과제',
            '3. 1차 면접 (실무진)',
            '4. 2차 면접 (팀장)',
            '5. 최종 합격'
        ],
        'jobkorea_url' => 'https://www.jobkorea.co.kr'
    ],
    3 => [
        'company' => '쿠팡',
        'logo' => '🔵',
        'position' => '데이터 엔지니어',
        'type' => 'IT/개발',
        'experience' => '경력 5년 이상',
        'education' => '대졸 이상 (전산/통계/수학 전공 우대)',
        'employment_type' => '정규직',
        'location' => '서울 송파구 송파대로',
        'salary' => '연봉 6,000만원 ~ 9,000만원',
        'deadline' => '2025.12.15',
        'tech_stack' => ['Python', 'Spark', 'Kafka', 'Airflow', 'AWS', 'Hadoop', 'Hive'],
        'description' => '쿠팡의 대규모 데이터 파이프라인을 설계하고 구축할 데이터 엔지니어를 모집합니다. 하루 수억 건의 데이터를 처리하며, 데이터 기반 의사결정을 지원하는 인프라를 만듭니다.',
        'requirements' => [
            'Python 기반 데이터 처리 및 분석 경험 5년 이상',
            'Spark, Hadoop 등 빅데이터 기술 실무 경험',
            'Kafka, RabbitMQ 등 메시지 큐 시스템 경험',
            'AWS 클라우드 서비스(S3, EMR, Redshift 등) 사용 경험',
            'SQL 및 데이터베이스 성능 최적화 능력'
        ],
        'preferred' => [
            'Airflow, Luigi 등 워크플로우 관리 도구 경험',
            '실시간 데이터 스트리밍 처리 시스템 구축 경험',
            'Data Lake, Data Warehouse 아키텍처 설계 경험',
            '머신러닝 파이프라인 구축 및 운영 경험',
            'Kubernetes 기반 데이터 플랫폼 운영 경험'
        ],
        'benefits' => [
            '경쟁력 있는 연봉 및 성과금',
            'RSU(주식보상) 제공',
            '무제한 간식/음료',
            '최고급 장비 지원 (맥북 프로 등)',
            '컨퍼런스 참가 전액 지원',
            '해외 연수 기회',
            '출퇴근 셔틀버스 운영',
            '사내 헬스장 무료',
            '자녀 학자금 지원',
            '주택 자금 대출 지원',
            '건강검진 프리미엄',
            '휴양시설 제공'
        ],
        'process' => [
            '1. 서류 전형',
            '2. 기술 과제',
            '3. 1차 면접 (기술 인터뷰)',
            '4. 2차 면접 (컬처핏)',
            '5. 레퍼런스 체크',
            '6. 최종 합격'
        ],
        'jobkorea_url' => 'https://www.jobkorea.co.kr'
    ],
    4 => [
        'company' => '현대자동차',
        'logo' => '🚗',
        'position' => '자율주행 개발자',
        'type' => 'IT/개발',
        'experience' => '경력 3년 이상',
        'education' => '대졸 이상',
        'employment_type' => '정규직',
        'location' => '경기 화성시',
        'salary' => '연봉 협의',
        'deadline' => '2025.12.15',
        'tech_stack' => ['Python', 'C++', 'ROS', 'TensorFlow', 'PyTorch', 'OpenCV'],
        'description' => '현대자동차의 자율주행 기술을 개발할 인재를 찾습니다.',
        'requirements' => [
            '컴퓨터비전 및 딥러닝 경험 3년 이상',
            'Python, C++ 능숙',
            'ROS 사용 경험'
        ],
        'preferred' => [
            '자율주행 프로젝트 경험',
            '센서 융합 기술 이해'
        ],
        'benefits' => [
            '연봉 외 성과급',
            '최신 장비 지원',
            '유연근무제'
        ],
        'process' => [
            '1. 서류 전형',
            '2. 기술 면접',
            '3. 최종 합격'
        ],
        'jobkorea_url' => 'https://www.jobkorea.co.kr'
    ]
];

$job = isset($jobs[$job_id]) ? $jobs[$job_id] : $jobs[1];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['company'] . ' ' . $job['position']); ?> - Company Portal</title>
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
            background: #f44336;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-logout:hover {
            background: #d32f2f;
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

        /* ==================== 메인 컨텐츠 ==================== */
        .main-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 20px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            color: #0066ff;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 2rem;
            border: 2px solid #0066ff;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #0066ff;
            color: white;
        }

        .job-detail-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .job-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
        }

        .company-logo {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .job-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .company-name {
            font-size: 1.3rem;
            opacity: 0.95;
            margin-bottom: 1.5rem;
        }

        .job-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .job-body {
            padding: 2rem;
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .info-item {
            background: #f9fafb;
            padding: 1.2rem;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
        }

        .info-label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
        }

        .tech-stack {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .tech-badge {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .description {
            line-height: 1.8;
            color: #4b5563;
            font-size: 1rem;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
        }

        .requirements-list li {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
        }

        .requirements-list li::before {
            content: '✓';
            color: #10b981;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .preferred-list li::before {
            content: '⭐';
            color: #f59e0b;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .benefit-card {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            font-size: 0.9rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
        }

        .benefit-card:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: translateY(-2px);
        }

        .process-timeline {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .process-item {
            background: #f9fafb;
            padding: 1.2rem;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
            font-size: 1rem;
        }

        .apply-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .apply-section h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .apply-section p {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 2rem;
        }

        .btn-apply {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 1rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .btn-apply:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        /* ==================== 반응형 디자인 ==================== */
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

            .info-grid {
                grid-template-columns: 1fr;
            }

            .job-meta {
                gap: 1rem;
            }

            .benefits-grid {
                grid-template-columns: 1fr;
            }

            .job-header {
                padding: 2rem 1.5rem;
            }

            .job-title {
                font-size: 1.5rem;
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

    <!-- 메인 컨텐츠 -->
    <main class="main-content">
        <a href="jobs.php" class="btn-back">← 목록으로 돌아가기</a>
        
        <div class="job-detail-container">
            <div class="job-header">
                <div class="company-logo"><?php echo $job['logo']; ?></div>
                <h1 class="job-title"><?php echo htmlspecialchars($job['position']); ?></h1>
                <p class="company-name"><?php echo htmlspecialchars($job['company']); ?></p>
                
                <div class="job-meta">
                    <div class="meta-item">
                        <span>📍</span>
                        <span><?php echo htmlspecialchars($job['location']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span>💼</span>
                        <span><?php echo htmlspecialchars($job['experience']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span>💰</span>
                        <span><?php echo htmlspecialchars($job['salary']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span>📅</span>
                        <span>마감: <?php echo htmlspecialchars($job['deadline']); ?></span>
                    </div>
                </div>
            </div>

            <div class="job-body">
                <!-- 기본 정보 -->
                <section class="section">
                    <h2 class="section-title">📋 기본 정보</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">고용 형태</div>
                            <div class="info-value"><?php echo htmlspecialchars($job['employment_type']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">학력</div>
                            <div class="info-value"><?php echo htmlspecialchars($job['education']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">경력</div>
                            <div class="info-value"><?php echo htmlspecialchars($job['experience']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">모집 분야</div>
                            <div class="info-value"><?php echo htmlspecialchars($job['type']); ?></div>
                        </div>
                    </div>
                </section>

                <!-- 기술 스택 -->
                <section class="section">
                    <h2 class="section-title">💻 기술 스택</h2>
                    <div class="tech-stack">
                        <?php foreach ($job['tech_stack'] as $tech): ?>
                            <span class="tech-badge"><?php echo htmlspecialchars($tech); ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 포지션 소개 -->
                <section class="section">
                    <h2 class="section-title">📢 포지션 소개</h2>
                    <div class="description">
                        <?php echo htmlspecialchars($job['description']); ?>
                    </div>
                </section>

                <!-- 자격 요건 -->
                <section class="section">
                    <h2 class="section-title">✅ 자격 요건</h2>
                    <ul class="requirements-list">
                        <?php foreach ($job['requirements'] as $req): ?>
                            <li><?php echo htmlspecialchars($req); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <!-- 우대 사항 -->
                <section class="section">
                    <h2 class="section-title">⭐ 우대 사항</h2>
                    <ul class="requirements-list preferred-list">
                        <?php foreach ($job['preferred'] as $pref): ?>
                            <li><?php echo htmlspecialchars($pref); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <!-- 복리후생 -->
                <section class="section">
                    <h2 class="section-title">🎁 복리후생</h2>
                    <div class="benefits-grid">
                        <?php foreach ($job['benefits'] as $benefit): ?>
                            <div class="benefit-card"><?php echo htmlspecialchars($benefit); ?></div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 전형 절차 -->
                <section class="section">
                    <h2 class="section-title">📝 전형 절차</h2>
                    <div class="process-timeline">
                        <?php foreach ($job['process'] as $step): ?>
                            <div class="process-item">
                                <?php echo htmlspecialchars($step); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <div class="apply-section">
                <h3>🚀 지금 바로 지원하세요!</h3>
                <p>잡코리아에서 더 자세한 정보를 확인하고 지원할 수 있습니다.</p>
                <a href="<?php echo htmlspecialchars($job['jobkorea_url']); ?>" target="_blank" class="btn-apply">
                    잡코리아에서 지원하기 →
                </a>
            </div>
        </div>
    </main>

    <script>
        // 햄버거 메뉴
        document.getElementById('hamburger').addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('nav').classList.toggle('active');
        });
    </script>
</body>
</html>