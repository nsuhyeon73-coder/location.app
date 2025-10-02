//동기방식
console.log("====동기방식====");
console.log("1. 라면 장보기");
console.log("2. 물 끓이기");
console.log("3. 끓는 물에 라면, 스프 넣고 익히기");
console.log("4. 완성");

//비동기 방식
console.log("====비동기 방식====");
setTimeout(() => {
  console.log("a. 라면장보기");
}, 3000);
console.log("b. 물 끓이기");
console.log("c. 끓는 물에 라면, 스프 넣고 익히기");
console.log("d. 완성");
