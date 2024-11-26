## async

+ `async` 函数是一个特殊的 `Generator` 函数，它内部使用 `await` 表达式来定义不同的内部状态。
+ `async` 函数自带执行器，`async` 函数的执行与普通函数一模一样。
+ `async` 表示函数中存在异步操作，`await` 表示紧跟在后面的表达式需要等待结果。
+ `async` 函数返回一个 `Promise` 对象，可以用 `then` 方法来指定回调函数。

```javascript
async function getStockPriceByName(name) {
    const symbol = await getStockSymbol(name)
    return await getStockPrice(symbol)
}

getStockPriceByName('goog').then(function (result) {
    console.log(result)
})
```