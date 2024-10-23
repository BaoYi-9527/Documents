function* helloWorldGenerator() {
    yield 'Hello';
    yield 'World';
    return 'Done';
}

var hw = helloWorldGenerator();

console.log(hw.next().value);
console.log(hw.next().value);
console.log(hw.next().value);
