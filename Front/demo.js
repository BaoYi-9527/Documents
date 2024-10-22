// const promise = new Promise(function (resolve, reject) {
//     // ...some code
//     let value = true
//     let error = null
//
//     if (value) {
//         // resolve 将 Promise 对象由 pending -> resolved
//         // 并将异步操作的结果作为参数传递出去
//         resolve(value)
//     } else {
//         // reject 将 Promise 对象由 pending -> rejected
//         // 在异步操作失败时调用，将异步操作报出的错误，作为参数传递出去
//         reject(error)
//     }
//     // then 方法可以分别指定 resolved 和 rejected 的回调函数
//     // 第一个回调函数在 Promise 对象状态变为 resolved 时执行
//     // 第二个回调函数在 Promise 对象状态变为 rejected 时执行
//     // 俩个回调函数都是可选的，且都接受 Promise 对象传出的值作为参数
// }).then(function (value) {
//
// }, function (error) {
//
// })

// function timeout(ms) {
//     return new Promise(resolve => setTimeout(resolve, ms, 'done'))
// }
//
// timeout(100).then((value) => {
//     console.log(value)
// })

// let promise = new Promise((resolve, reject) => {
//     console.log('Promise start')
//     resolve()
// })
//
// promise.then(function () {
//     console.log('promise success')
// })
//
// console.log('Hi!')