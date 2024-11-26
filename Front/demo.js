// async function getStockPriceByName(name) {
//     const symbol = await getStockSymbol(name)
//     return await getStockPrice(symbol)
// }
//
// getStockPriceByName('goog').then(function (result) {
//     console.log(result)
// })

async function timeout(ms) {
    return new Promise((resolve) => {
        setTimeout(resolve, ms)
    })
}


async function asyncPrint(value, ms) {
    await timeout(ms)
    console.log('1-' + value)
    await Promise.reject('error message')
    return value
}

const res = asyncPrint('hello world', 1000)

res.then((value) => {
    console.log('2-' + value)
}).catch(e => console.log(e))

console.log(res)