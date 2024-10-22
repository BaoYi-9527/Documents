## Promise

+ Promise 是一个容器，其保存了某个未来才会结束的事件（通常是一个异步操作）的结果。
+ Promise 是一个对象，其可以获取异步操作的消息。

**Promise 的一些特点：**
1. Promise 对象有三种状态：pending（等待中），resolved（已完成）和 rejected（已拒绝）。
2. Promise 对象是一个构造函数，用于生成 Promise 实例，该实例是一个对象，包含 then 方法，用于指定不同状态的回调函数。
3. Promise 对象的状态只能改变一次，一旦改变，就不可以改变了。
4. Promise 对象的状态不受外界影响，只有异步操作可以决定当前是那一种状态。
5. Promise 对象无法取消，一旦创建就会立即执行，无法中途取消。
6. 如果不设置回调函数，Promise 内部抛出的错误不会反应到外部。
7. Promise 对象处于 pending 状态时，无法得知当前具体的执行阶段。

### Promise 的基本使用

```javascript
const promise = new Promise(function (resolve, reject) {
    // ...some code
    let value = true
    let error = null

    if (value) {
        // resolve 将 Promise 对象由 pending -> resolved
        // 并将异步操作的结果作为参数传递出去
        resolve(value)
    } else {
        // reject 将 Promise 对象由 pending -> rejected
        // 在异步操作失败时调用，将异步操作报出的错误，作为参数传递出去
        reject(error)
    }
    // then 方法可以分别指定 resolved 和 rejected 的回调函数
    // 第一个回调函数在 Promise 对象状态变为 resolved 时执行
    // 第二个回调函数在 Promise 对象状态变为 rejected 时执行
    // 俩个回调函数都是可选的，且都接受 Promise 对象传出的值作为参数
}).then(function (value) {
    // ...
}, function (error) {
    // ...
})
```

**Promise 的异步特点：**

```javascript
let promise = new Promise((resolve, reject) => {
    console.log('Promise start')
    resolve()
})

promise.then(function () {
    console.log('promise success')
})

console.log('Hi!')

// Promise start
// Hi!
// promise success
```