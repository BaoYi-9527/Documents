#### Reference

1.[谈谈 PHP 的自动加载机制与 Laravel 中的具体实现](https://zhuanlan.zhihu.com/p/61729937)
2.[PHP 自动加载功能原理解析](https://blog.csdn.net/cangqiong_xiamen/article/details/107468364)

#### PHP自动加载机制

> 在没有自动加载机制前，使用外部类需要手动使用 `require()/include()` 进行文件的引入，对于大型项目而言一旦引入量大起来就会显得相当复杂。
> 这样的方式导致在需要很多类时容易造成遗漏或者引入不必要的文件，当为了避免重复引入时，使用 `require_once()/include_once()` 又会造成速度上比 `require()/include()` 慢2-3倍。

##### __autoload()

为了解决`require()/include()`的问题，PHP5中引入了自动加载函数 `__autoload()`，当使用一个没有加载过的类时，PHP会自动运行 `__autoload()` 函数。

```php
function __autoload($className) {
    require_once('path/'.$className.'.php');
}
```

*__autoload()需要完成的功能：*
1. 根据类名确定类文件名，即类名需要和类文件名一致（需要约定映射规则）；
2. 确定类的具体路径（需要约定映射规则）；
3. 加载类 `include()/require()` 的实现；

*__autoload()的优点：*
1. 摆脱了大量的 `include()/require()`；
2. 使用类时才会引入文件，实现了 `lazy loading`；
3. 无需知道类的实际文件地址，实现了逻辑和实体文件的分离（需要编写类的路径引入逻辑，就能自动加载到该类并实现实例化）；
*__autoload()的缺点：*
1. `__autolaod()` 作为全局函数，只能定义一次，不够灵活；
2. 类名和文件名的映射规则可能各不相同，这是要实现类库的自动加载就必须在 `__autoload()` 函数中将所有的映射规则实现，也就造成了 `__autoload()` 函数的复杂臃肿，效率低且难维护。

##### spl_autoload_register()

为了解决 `__autoload()` 作为全局函数带来的问题，PHP引入了 `spl_autoload`。`spl_autoload` 主要有以下几个函数：
1. `spl_autoload_register()`：注册 `__autoload()` 函数；
2. `spl_autoload_unregister()`：注销已注册的函数；
3. `spl_autoload_functions()`：返回所有已注册的函数；
4. `spl_autoload_call()`：尝试所有已注册的函数来加载类；
5. `spl_autoload()`：`__autoload()` 的默认实现；
6. `spl_autoload_extionsions()`：注册并返回 `spl_autoload()` 函数使用的默认文件扩展名；

简单来说，`spl_autoload` 就是SPL(标准PHP库)定义的 `__autoload()` 函数，其功能主要是去注册的目录(`set_include_path`设置)找与`className`同名的 `.php/.inc` 文件。而 `spl_autoload_register()` 函数就是 `__autoload()` 的调用堆栈，该函数可以注册多个 `__autolaod()` 函数。当PHP找不到类名时，便会调用该函数，遍历调用其中自定义的 `__autoload()` 函数，从而实现自动加载功能。若该函数不注册自定义的 `__autoload()` 函数，就会注册默认的 `spl_autoload()` 函数。
