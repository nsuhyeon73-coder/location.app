// Hamburger Menu
const hamburger = document.getElementById("hamburger");
const nav = document.getElementById("nav");

if (hamburger && nav) {
  hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    nav.classList.toggle("active");
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

// Banner Slider - 수정된 버전
let currentSlide = 0;
let slideInterval;

function showSlide(index) {
  const slides = document.querySelectorAll(".banner-slide");
  const indicators = document.querySelectorAll(".indicator");

  if (!slides.length) return;

  // 인덱스 범위 조정
  if (index >= slides.length) {
    currentSlide = 0;
  } else if (index < 0) {
    currentSlide = slides.length - 1;
  } else {
    currentSlide = index;
  }

  // 모든 슬라이드와 인디케이터 비활성화
  slides.forEach((slide) => slide.classList.remove("active"));
  indicators.forEach((indicator) => indicator.classList.remove("active"));

  // 현재 슬라이드와 인디케이터 활성화
  slides[currentSlide].classList.add("active");
  indicators[currentSlide].classList.add("active");
}

function nextSlide() {
  currentSlide++;
  showSlide(currentSlide);
  resetSlideInterval();
}

function prevSlide() {
  currentSlide--;
  showSlide(currentSlide);
  resetSlideInterval();
}

function goToSlide(index) {
  currentSlide = index;
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

// 검색 기능 - 탭으로 이동
function performSearch() {
  const searchInput = document.getElementById("mainSearch");
  const query = searchInput.value.trim();

  if (query) {
    // 검색어에 따라 해당 페이지로 이동
    const lowerQuery = query.toLowerCase();

    if (lowerQuery.includes("스터디") || lowerQuery.includes("study")) {
      window.location.href = "study.php";
    } else if (
      lowerQuery.includes("채용") ||
      lowerQuery.includes("job") ||
      lowerQuery.includes("공고")
    ) {
      window.location.href = "jobs.php";
    } else if (
      lowerQuery.includes("익명") ||
      lowerQuery.includes("게시판") ||
      lowerQuery.includes("board")
    ) {
      window.location.href = "board.php";
    } else if (lowerQuery.includes("공지") || lowerQuery.includes("notice")) {
      window.location.href = "notice.php";
    } else if (
      lowerQuery.includes("취준") ||
      lowerQuery.includes("취업") ||
      lowerQuery.includes("jobseeker")
    ) {
      window.location.href = "jobseeker.php";
    } else {
      // 기본적으로 스터디 페이지로 이동
      alert(`"${query}" 검색 결과를 스터디 페이지에서 확인하세요.`);
      window.location.href = "study.php";
    }
  } else {
    alert("검색어를 입력해주세요.");
  }
}

function searchTag(tag) {
  // 태그 클릭시 해당 카테고리 페이지로 이동
  const tagMap = {
    React: "study.php",
    Python: "study.php",
    면접: "jobseeker.php",
    이력서: "jobseeker.php",
    영어: "study.php",
  };

  if (tagMap[tag]) {
    window.location.href = tagMap[tag];
  } else {
    window.location.href = "study.php";
  }
}

// Active navigation
const currentPage = window.location.pathname.split("/").pop();
const navLinks = document.querySelectorAll(".nav a");
navLinks.forEach((link) => {
  const linkPage = link.getAttribute("href");
  if (
    linkPage === currentPage ||
    (currentPage === "" && linkPage === "index.php")
  ) {
    link.classList.add("active");
  }
});

// 페이지 로드 애니메이션 제거 - 즉시 표시
document.body.style.opacity = "1";

console.log("✅ Company Portal JavaScript loaded!");

// Jobseeker page - Service card animation
if (document.querySelector(".jobseeker-service-card")) {
  const serviceCards = document.querySelectorAll(".jobseeker-service-card");

  serviceCards.forEach((card) => {
    card.addEventListener("click", function (e) {
      e.preventDefault();
      alert("준비 중인 기능입니다. 곧 오픈됩니다! 🚀");
    });
  });
}

console.log("✅ Jobseeker page scripts loaded!");

// 실시간 시계 업데이트
function updateClock() {
  const now = new Date();
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");

  const clockElement = document.getElementById("digitalClock");
  if (clockElement) {
    clockElement.textContent = `${hours}:${minutes}:${seconds}`;
  }
}

// 달력 생성 변수
let currentYear, currentMonth;

// 달력 생성 함수
function generateCalendar(year, month) {
  const calendarDays = document.getElementById("calendarDays");
  const calendarYearMonth = document.getElementById("calendarYearMonth");

  if (!calendarDays || !calendarYearMonth) return;

  // 년/월 표시
  calendarYearMonth.textContent = `${year}년 ${month + 1}월`;

  // 해당 월의 첫 날과 마지막 날
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const prevLastDay = new Date(year, month, 0);

  const firstDayOfWeek = firstDay.getDay(); // 0 (일요일) ~ 6 (토요일)
  const lastDate = lastDay.getDate();
  const prevLastDate = prevLastDay.getDate();

  // 오늘 날짜
  const today = new Date();
  const isCurrentMonth =
    today.getFullYear() === year && today.getMonth() === month;
  const todayDate = today.getDate();

  // 달력 초기화
  calendarDays.innerHTML = "";

  // 이전 달의 날짜들
  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const day = prevLastDate - i;
    const dayDiv = createDayElement(day, "prev");
    calendarDays.appendChild(dayDiv);
  }

  // 현재 달의 날짜들
  for (let day = 1; day <= lastDate; day++) {
    const isToday = isCurrentMonth && day === todayDate;
    const dayDiv = createDayElement(day, "current", isToday);
    calendarDays.appendChild(dayDiv);
  }

  // 다음 달의 날짜들 (6주 채우기)
  const totalCells = calendarDays.children.length;
  const remainingCells = 42 - totalCells; // 6주 * 7일 = 42칸

  for (let day = 1; day <= remainingCells; day++) {
    const dayDiv = createDayElement(day, "next");
    calendarDays.appendChild(dayDiv);
  }
}

// 날짜 요소 생성 - 모든 애니메이션 제거
function createDayElement(day, type, isToday = false) {
  const dayDiv = document.createElement("div");
  dayDiv.textContent = day;
  dayDiv.style.cssText = `
    text-align: center;
    padding: 0.6rem;
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
  `;

  // 이전/다음 달 날짜 스타일
  if (type === "prev" || type === "next") {
    dayDiv.style.color = "#d1d5db";
    dayDiv.style.cursor = "default";
  } else {
    dayDiv.style.color = "#1f2937";
    dayDiv.style.fontWeight = "500";
  }

  // 오늘 날짜 강조 - hover 효과 제거
  if (isToday) {
    dayDiv.style.background =
      "linear-gradient(135deg, #667eea 0%, #764ba2 100%)";
    dayDiv.style.color = "white";
    dayDiv.style.fontWeight = "700";
    dayDiv.style.boxShadow = "0 4px 15px rgba(102, 126, 234, 0.4)";
  }

  return dayDiv;
}

// 이전 달로 이동
function prevMonth() {
  currentMonth--;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  }
  generateCalendar(currentYear, currentMonth);
}

// 다음 달로 이동
function nextMonth() {
  currentMonth++;
  if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  generateCalendar(currentYear, currentMonth);
}

// 페이지 로드시 실행
document.addEventListener("DOMContentLoaded", function () {
  // 현재 날짜로 달력 초기화
  const today = new Date();
  currentYear = today.getFullYear();
  currentMonth = today.getMonth();

  // 달력 생성
  if (document.getElementById("calendarDays")) {
    generateCalendar(currentYear, currentMonth);

    // 이전/다음 버튼 이벤트
    const prevBtn = document.getElementById("prevMonth");
    const nextBtn = document.getElementById("nextMonth");

    if (prevBtn) prevBtn.addEventListener("click", prevMonth);
    if (nextBtn) nextBtn.addEventListener("click", nextMonth);
  }

  // 시계 시작
  if (document.getElementById("digitalClock")) {
    updateClock();
    setInterval(updateClock, 1000);
  }

  // 스터디 카드 클릭시 study.php로 이동
  const studyCard = document.querySelector(".card.large");
  if (studyCard) {
    studyCard.style.cursor = "pointer";
    studyCard.addEventListener("click", function (e) {
      if (!e.target.closest(".post-item")) {
        window.location.href = "study.php";
      }
    });
  }

  // 익명 게시판, 공지사항 카드 네비게이션
  const cards = document.querySelectorAll(".card");
  cards.forEach((card) => {
    const header = card.querySelector(".card-header h2");
    if (header && header.textContent.includes("익명 게시판")) {
      card.style.cursor = "pointer";
      card.addEventListener("click", function (e) {
        if (!e.target.closest(".board-item")) {
          window.location.href = "board.php";
        }
      });
    }

    if (header && header.textContent.includes("공지사항")) {
      card.style.cursor = "pointer";
      card.addEventListener("click", function (e) {
        if (!e.target.closest(".notice-item")) {
          window.location.href = "notice.php";
        }
      });
    }
  });

  // 검색창 엔터키 이벤트
  const searchInput = document.getElementById("mainSearch");
  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        performSearch();
      }
    });
  }

  // 슬라이더 자동 재생 시작
  if (document.querySelector(".banner-slide")) {
    showSlide(0); // 첫 슬라이드 표시
    resetSlideInterval(); // 자동 재생 시작
  }
});

console.log("✅ Calendar and real-time clock loaded!");

// 사용자 드롭다운 메뉴
function toggleUserDropdown() {
  const dropdown = document.getElementById("userDropdown");
  if (dropdown) {
    dropdown.classList.toggle("active");
  }
}

// 드롭다운 외부 클릭시 닫기
document.addEventListener("click", function (e) {
  const dropdown = document.getElementById("userDropdown");
  const welcomeMsg = document.querySelector(".welcome-msg");

  if (dropdown && welcomeMsg) {
    if (!dropdown.contains(e.target) && !welcomeMsg.contains(e.target)) {
      dropdown.classList.remove("active");
    }
  }
});
