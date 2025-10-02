// const promise = new Promise((resolve, reject) => {
//   resolve("성공");
// });
// promise.then((message) => {
//   console.log(message);
// });

// const getPromise1 = () => {
//   return new Promise((resolve, reject) => {
//     setTimeout(() => {
//       resolve("첫번째 작업 완료");
//     }, 1000);
//   });
// };

// const getPromise2 = (second) => {
//   return new Promise((resolve, reject) => {
//     setTimeout(() => {
//       resolve("두번째 작업 완료 :" + second);
//     }, 1000 * second);
//   });
// };
// getPromise2(3);

// const getPromise3 = (second) => {
//   return new Promise((resolve, reject) => {
//     setTimeout(() => {
//       resolve("세번째 작업 완료 :" + second);
//     }, 1000 * second);
//   });
// };

// getPromise1()
//   .then((message) => {
//     console.log(message);
//     return getPromise2(3);
//   })
//   .then((message) => {
//     console.log(message);
//     return getPromise3(3);
//   })
//   .then((message) => {
//     console.log(message);
//   });

const promiseFail = new Promise((resolve, reject) => {
  reject("에러");
});
promiseFail
  .then(() => {
    console.log("성공" + message);
  })
  .catch((message) => {
    console.log("실패" + message);
  });
