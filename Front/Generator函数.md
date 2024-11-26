## Generator

+ 生成器是一种特殊的函数，可以用来解决异步编程的问题。
+ 执行 Generator 函数会返回一个迭代器对象，迭代器对象可以被用来遍历 Generator 函数的返回值。
+ 形式上，Generator 函数是一个普通函数。

**Generator 函数的特征：**
+ `function` 关键字和函数名之间有一个 `*` 号；
+ 函数体内部使用 `yield` 表达式，定义不同的内部状态。

```javascript
function* helloWorldGenerator() {
    yield 'Hello';
    yield 'World';
    return 'Done';
}

var hw = helloWorldGenerator();

console.log(hw.next().value);
```
