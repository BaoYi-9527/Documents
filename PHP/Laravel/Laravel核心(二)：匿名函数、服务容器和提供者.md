**欢迎指正内容不严谨或有误的地方！**

#### Reference

1. [Laravel 5.8 中文文档](https://learnku.com/docs/laravel/5.8/container/3886)
2. [cxp1539/laravel-core-learn](https://github.com/cxp1539/laravel-core-learn)
3. [3-php/laravel底层核心代码分析之匿名函数](https://www.bilibili.com/video/BV1dC4y1a7Qp)
4. [4-php/laravel底层核心代码分析核心概念serviceContainer&serviceProvider](https://www.bilibili.com/video/BV1yz411i7AJ)

#### 1.匿名函数

1. **回调函数：** 不是直接执行的函数，而是通过其他方式(`call_user_func_array()/array_map()`)来调用的函数。
2. **匿名函数：** 允许临时创建一个没有指定名称的函数。最经常用作回调函数（callback）参数的值。
3. **闭包：** 创建时封装周围状态的函数，即使周围的环境不存在了，闭包中的状态也还存在(子函数使用父函数中的局部变量，这种行为叫做闭包)；闭包就是能够读取其他函数内部变量的函数(即匿名函数+`use`关键字)；

> ①闭包如果不结合 use 关键字使用，毫无意义，可以使用普通函数来代替该匿名函数。闭包常见的使用方式是：①当做参数传递；②当做返回值返回。
> ②可以说回调函数和闭包都是匿名函数的用法之一，本质上它们都是匿名函数。
> ③匿名函数只有在调用的时候才会真正的被解析。而调用匿名函数的时候值需要传递的参数是紧接着 `function` 后的变量，而不是 `use` 关键字；

```php
// ①回调函数
function backFunc($a) {
    return $a;
}
// 触发回调
call_user_func_array('backFunc',['a']);

// ②匿名函数
$anoFunc = function($var) {
    return $var;
}
$anoFunc('a');

// ③闭包
function parentFunc(){
    $a = 1;
    $b = 2;
    // 返回了一个匿名函数，这个函数调用了其父函数中的参数
    return function($date) use ($a, $b) {
        echo $date.":".$a.':'.$b
    }
}
$res = parentFunc();
$res('2021-11-12');
```

#### 2.服务容器和服务提供者

##### 2.1 概念理解

1. 我们使用框架的原因就是框架已经集成好了一些模块和功能(比如：路由、缓存、图片处理、支付模块...)，使得开发变得简单快速。
2. 传统框架的做法是将这些模块直接集成在框架中，如果框架没有某项服务(比如：短信服务、邮件服务、队列...)就需要开发者自己去开发，这些服务是作为框架的一部分的。
3. 而Laravel则将这些模块/服务提取出来，不再作为框架的一部分，而是以第三方的形式做了处理。不再是框架本身提供这些服务，而是由 `serviceProvider` 来提供，包括我们常见的数据库管理、图片管理都是第三方提供。
4. `serviceContainer` 则提供了注册机制，通过依赖注入可以将这些服务注册进服务容器本身，很好的解决了类与类之间的依赖关系。
5. 当开发者需要调用这些服务时，只需要通过 `serviceContainer` 实例化对象即可使用。

**serviceProvider 是有能力提供服务的服务提供者；而serviceContainer则是为了更加方便的管理这些服务，即一个类的实例化对象，在启动之初serviceContainer会对所有可能用到的服务进行加载，用到的时候再去解析调用其中的方法。**

> Laravel中除去基本的事件、异常处理、Facades外，其他服务全都是通过serviceProvider提供的，比如常见的路由、缓存、数据库管理等。
> 由于 serviceProvider 是被定义为第三方的存在，所以它们不作为 Laravel 的必须项目一定要加载，原则上开发者可以不加载任何用不到的非核心的 serviceProvider。

`config\app.php->providers` 下可以看到我们用到的所有 `serviceProvider`，服务提供者主要分为三类：
1. *Framework Service Providers：* 框架级别的服务提供者；
2. *Package Service Providers：* 第三包级别的服务提供者；
3. *Application Service Providers：* 应用程序级别的服务提供者；

*现实场景帮助理解：服务容器就像我们用到的手机，服务提供者就是手机里的APP，当我们需要使用某项功能时，我们就去调用APP(服务提供者)，而我们下载安装APP的过程就是服务提供者注册的过程。*

##### 2.2 解析：make

**make 方法：make方法可以用于从容器中解析类实例。**

```php
// 比如 public/index.php 文件中对 HTTP内核的实例化
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
// 使用 Facades 的方式从容器解析类实例
use App\Services\Transistor;
use Illuminates\Support\Facades\App;

$transistor = App::make(Transistor::class);
```

##### 2.3 容器事件

服务容器每次解析对象时都会触发一个事件，开发者可以使用 `resolving()` 方法监听这个事件；

```php
use App\Services\Transistor;

$this->app->resolving(Transistor::class, function ($transistor, $app) {
    // ...当容器解析类型为 Transistor 的对象时调用
})

$this->app->resolving(function ($object, $app) {
    // 当容器解析任何类型的对象时调用
})
```

##### 2.4 服务提供者

所有的服务提供者都会继承 `Illuminate\Support\ServiceProvider` 类，大部分的 ServiceProvider 都会包含一个 `register` 和一个 `boot` 方法。

1. `register` 方法中，可以将服务直接绑定到服务容器中(部分框架级的 ServiceProvider 是不需 register 的，因为它们在服务容器生成的时候就已经注册了)。此外不要尝试在 `register` 方法中注册任何监听器、路由或者任何功能；
2. `boot` 引导方法，该方法在所有服务提供者被注册后才会被调用(RouteServiceProvider实现了该方法调度路由)，也就是说可在该方法中访问框架已经注册的其他服务。

**服务提供者的bindings和singletons属性：**
1. bindings：所有需要注册的容器绑定；singletons：所有需要注册的容器单例；
2. 当ServiceProvider被框架加载时，将自动检查这些属性并注册相应的绑定；

#### 3. 补充：Facades和Contracts

##### 3.1 外观模式 Facade

1. Facades 为应用程序的服务容器中可用的类提供了**静态代理**。
2. Laravel 所有的 Facades 都在 `Illuminate\Support\Facades` 命名空间中定义。

**Facades的实现：**

1. 在Laravel中，Facade 就是一个可以从容器访问对象的类，其中核心的类就是 `Facade` 类；
2. 不管是 Laravel 自带的 Facade，还是自定义的 Facade 都继承自 `Illuminate\Support\Facades\Facade` 类；
3. `Facade` 基类使用了 `__callStatic()` 魔术方法，直到对象从容器中解析出来后，才会进行调用。

*使用Facades最主要的目的就是它提供了简单、易记的语法，从而无需手动注入或配置长长的类名。*

```php
// Illuminate\Support\Facades->App.php;
// 返回服务容器绑定的名称
protected static function getFacadeAccessor()
{
    return 'app';
}

// Illuminate\Support\Facades->Facade.php;
public static function __callStatic($method, $args)
{
    $instance = static::getFacadeRoot();    // 后期静态绑定
    if (! $instance) {
        throw new RuntimeException('A facade root has not been set.');
    }
    return $instance->$method(...$args);
}
```

##### 3.2 Contract 契约

1. Laravel 的契约是一组由框架提供，定义了核心服务的 `interface` 集合；`Illuminate\Foundation\Application.php->registerCoreContainerAliases` 中绑定了默认的接口实现；
2. 每个契约都拥有相应的框架提供的实现；
3. 契约就是面向接口编程。

