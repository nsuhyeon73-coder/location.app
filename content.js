let diaryItems = [];

// 1. 크롬스토리지에서 데이터 불러오는 함수 선언
function getDiaryItems() {
  // 2. 크롬스토리지에서 데이터 가져오기
  chrome.storage.local.get("diary", (result) => {
    if (result.diary) {
      console.log("성공", result.diary);
      diaryItems = result.diary;
      // forEach()
      // 배열의 개수만큼 반복실행
      diaryItems.forEach((item) => {
        // item => diary{}(다이어리객체)
        // 1. li태그 만들기
        const $li = $("<li>");
        // 1. 속성의 이름
        // 2. 속성의 값
        $li.attr("id", item.id);

        // 2. p태그 만들기
        const $p = $("<p>");
        // p태그안에 작성한 내용 넣기
        $p.text(`${item.date} ${item.text}`);
        // 삭제 버튼 생성
        const $removeBtn = $("<button>")
          .text("삭제")
          .on("click", function () {
            //삭제버튼의 부모(li) 삭제
            $(this).parent().remove();
            //1. li요소의 id속성 가져오기
            const id = $(this).parent().attr("id");
            // 2. 가져온 id를 제외한 새로운 배열 생성
            let newDiaryItems = diaryItems.filter((item) => {
              return item.id != id;
            });

            diaryItems = newDiaryItems;

            chrome.storage.local.set({ diary: newDiaryItems }, () => {
              console.log("데이터 저장 성공");
            });
          });

        // li요소에 p,button 추가
        $li.append($p);
        $li.append($removeBtn);
        //리스트에 li요소 추가
        $(".diary-list").append($li);
      });
    } else {
      console.log("데이터 없음");
    }
  });
}

// 제이쿼리 불러오기
$(function () {
  // dialog 모달창 띄우기
  console.log("제이쿼리 성공");
  // 데이터 불러오는 함수 호출
  getDiaryItems();
  //1. add-btn 태그 가져오기
  //2. 클릭이벤트 등록
  $(".add-btn").on("click", () => {
    let modal = $("#modal");
    // console.log(modal);
    // <dialog>
    // showModal() 메서드 : dialog태그 화면에 표시
    modal[0].showModal();
  });

  //overlay 클릭시 모달 닫기
  $("#modal").on("click", function (e) {
    let modal = $("#modal");
    // console.log(modal);
    // <dialog>
    // close() 메서드 : dialog태그 종료

    // 이벤트가 발생한 요소가 #modal일 경우
    // 모달창 종료
    //
    if (e.target === this) {
      // console.log(this);
      modal[0].close();
    }
  });

  // 폼 제출 이벤트로 데이터 가져오기
  $("#modal form").on("submit", () => {
    // 1. textArea태그에 적은 값 가져오기
    const diaryText = $("#modal textarea").val();
    // 2. 현재 날짜를 가져오기
    const formatDate = dayjs().format("YYYY-MM-DD");
    // console.log(diaryText, formatDate);

    // 1. Diary데이터 객체화
    const diary = {
      date: formatDate,
      text: diaryText,
      id: Date.now() + "",
    };
    // 배열에 새로 생성된 diary 객체 넣기
    diaryItems.push(diary);

    chrome.storage.local.set({ diary: diaryItems }, () => {
      console.log("데이터 저장 성공");
    });

    // 1. li태그 만들기
    const $li = $("<li>");
    // 1. 속성의 이름
    // 2. 속성의 값
    $li.attr("id", diary.id);

    // 2. p태그 만들기
    const $p = $("<p>");
    // p태그안에 작성한 내용 넣기
    $p.text(`${diary.date} ${diary.text}`);
    // 삭제 버튼 생성
    const $removeBtn = $("<button>")
      .text("삭제")
      .on("click", function () {
        //삭제버튼의 부모(li) 삭제
        $(this).parent().remove();
        //1. li요소의 id속성 가져오기
        const id = $(this).parent().attr("id");
        // 2. 가져온 id를 제외한 새로운 배열 생성
        let newDiaryItems = diaryItems.filter((item) => {
          return item.id != id;
        });
        diaryItems = newDiaryItems;
        chrome.storage.local.set({ diary: newDiaryItems }, () => {
          console.log("데이터 저장 성공");
        });
      });

    // li요소에 p,button 추가
    $li.append($p);
    $li.append($removeBtn);
    //리스트에 li요소 추가
    $(".diary-list").append($li);
    // textArea내용 비우기
    $("#modal textarea").val("");
  });
});

var options = {
  enableHighAccuracy: true,
  maximumAge: 0,
};

async function success(pos) {
  var crd = pos.coords;
  let latitude = crd.latitude;
  let longitude = crd.longitude;
  let apiKey = "8db55fc21a695d9d1bc4a050faaa8af9";

  try {
    const response = await fetch(
      `https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=${apiKey}&units=metric&lang=kr`
    );
    const data = await response.json();
    const weather = data.weather[0].description;
    const icon = data.weather[0].icon;
    const imageUrl = `https://openweathermap.org/img/wn/${icon}@2x.png`;
    console.log(weather);
    // Math.round() : 소수점 버리기
    const temp = Math.round(data.main.temp);
    console.log(temp);
    $(".weather").text(`${weather} ${temp}℃`);
    $(".weather-icon").attr("src", imageUrl);
  } catch (error) {
    console.log(error.message);
  }

  // .then((response) => {
  //   //console.log(response);
  //   if (response.ok) {
  //     return response.json();
  //   } else {
  //     console.log("호출실패");
  //   }
  // })
  // .then((data) => {
  //   console.log(data);
  //   const weather = data.weather[0].description;
  //   const icon = data.weather[0].icon;
  //   const imageUrl = `https://openweathermap.org/img/wn/${icon}@2x.png`;
  //   console.log(weather);
  //   // Math.round() : 소수점 버리기
  //   const temp = Math.round(data.main.temp);
  //   console.log(temp);
  //   $(".weather").text(`${weather} ${temp}℃`);
  //   $(".weather-icon").attr("src", imageUrl);
  // });
}

function error(err) {
  console.warn(`ERROR(${err.code}): ${err.message}`);
}

navigator.geolocation.getCurrentPosition(success, error);
