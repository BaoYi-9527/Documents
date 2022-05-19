### Reference
1. [Laravel:从百草园到三味书屋](https://achais.github.io/fata4/)

### 1.依赖注入

> Laravel 框架的基础是一个功能强大的控制反转容器(IoC Container)。而控制反转只是一种用于方便实现“依赖注入”的工具。
> 要实现依赖注入不一定需要控制反转容器，只是用容器会更方便和容易一点。

**关注分离原则：** *每个类都应该有单独的职责，并且该职责应完全被这个类封装。*

1. 关注分离的好处及时能让Web控制器和数据访问解耦。这会使得实现存储迁移更容易，测试也会更容易。Web就仅仅是为了真正地应用做数据的传输。
2. 尽量不要将传输机制(控制器)和业务逻辑混在一起，将业务逻辑抽离出来形成独立的 `logic` 或 `service` 层。
3. 这样做的好处是很多其他的传输机制比如API调用、移动应用、脚本调用都可以访问同样的业务逻辑，方便维护和复用代码。

**建立约定**

```php
// 定义一个接口
interface UserRepositoryInterface
{
    public function all();
}
// 实现这个接口
class DbUserRepository implements UserRepositoryInterface
{
    public function all()
    {
        return User::all()->toArray();
    }
}
// 将该接口的实现注入控制器
class UserController extends BaseControler
{
    protected $users;

    // 注入该接口
    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    public function getIndex()
    {
        $users = $this->users->all();
        return View::make('users.index', compact('users'));
    }
}
```

> *严守边界：* 保持清晰的责任边界。控制器和路由是作为HTTP和你的应用程序之间的中间件来使用的。当编写大型应用程序时，不要将业务逻辑混杂其中(控制器、路由)。

```php
interface BillerInterface {
    public function bill(array $user, $amount);
}

interface BillingNotifierInterface {
    public function notify(array $user, $amount);
}

class StripeBiller implements BillerInterface
{

    protected $notifier;

    // 无需考虑 notifier 的实际业务逻辑，只需要调用实现的 notifier 的 notify 方法
    public function __construct(BillingNotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function bill(array $user, $amount)
    {
        $this->notifier->notify($user, $amount);
    }
}

// 实现一个 notifier
class SmsNotifier implements BillingNotifierInterface
{

    public function notify(array $user, $amount)
    {
        // TODO: Implement notify() method.
    }
}

// 依赖注入
$biller = new StripeBiller(new SmsNotifier);
```

> 如果你在写小程序的时候无法遵守接口原则，别觉得不好意思。 要记住我们写代码是要快乐的写。如果你不喜欢写接口，那就先简单的写代码吧。日后再精进即可。


### 2.控制反转容器

> IoC容器可以使得开发者更容易管理依赖注入，Laravel 框架拥有一个强大的IoC容器（Laravel核心），该容器使得框架各个组件能很好的在一起工作。
> 事实上 Laravel 的 Application 类就是继承自 Container 类。

#### 基础绑定

```php
class EmailNotify implements BillingNotifierInterface
{

    public function notify(array $user, $amount)
    {
        // TODO: Implement notify() method.
    }
}

// 依赖注入

$biller = new StripeBiller(new SmsNotifier);

// 将接口绑定至容器
App::bind('BillerInterface', function () {
    return new StripeBiller(App::make('BillingNotifierInterface'));
});

App::bind('BillingNotifierInterface', function () {
   return new EmailNotify();
});

// 只实例化一次
App::singleton('BillingNotifierInterface', function () {
   return new SmsNotifier();
});
```

#### 反射解决方案

> 用反射来自动处理依赖是 Laravel 容器的一个最强大的特性。反射是一种运行时探测类和方法的能力。
> 当 Laravel 的容器无法解决一个类型的明显绑定时，容器会试着使用反射来解决依赖。

```php
$reflection = new ReflectionClass(StripeBiller::class);
var_dump($reflection->getMethods());
var_dump($reflection->getConstants());
```

*反射解决依赖绑定的流程：*
1. 若未实现 `StripeBiller` 的绑定，则使用反射探测该类，看看实现该类都需要什么依赖。
2. 解决 `StripeBiller` 依赖的所有依赖(递归处理)。
3. 使用 `$reflection->newInstanceArgs()` 实例化 `StripeBiller`。

```php
// 将 StripeBiller 绑定到接口 BillerInterface 上
// 告诉容器 从事使用 StripeBiller 作为 BillerInterface 的实现类
App::bind('BillerInterface', 'StripeBiller');
App::singleton('BillerInterface', 'StripeBiller');  // 单例模式
```

### 3.接口约定

> PHP 是一种鸭子类型的语言。也就是一个对象可用的方法取决于使用方式，而非这个方法从哪儿继承或实现。
> 强类型弱类型各有优劣。在强类型语言中，编译器通常能提供编译时错误检查的功能。但强类型的特性也使得程序僵化。

```php
// 在这里并没有定义这个参数的类型 
// 也就是传递任何对象都可以 只要该对象能够响应 getId 的调用
public function billUser($user)
{
    $this->biller->bill($user->getId(), $this->>amount)
}

// 强类型写法 强制要求传入的参数是一个 User 类型的实例
public function billUser(User $user)
{
    $this->biller->bill($user->getId(), $this->>amount)
}
```

> 接口就是约定。接口不包含任何代码实现，只是定义了一个对象应该实现的一系列方法。
> 如果一个对象实现了一个接口，那么该接口所定义的一系列方法都能在这个对象上使用。
> 因为对象在实现一个接口时，必须实现接口中的所有方法。

**多态**
1. 多态能使类型安全的语言变得更加灵活；
2. 在PHP中，多态是一个接口的多种实现；

```php
class User {
    public function bookLocation(ProviderInterface $provider, $location)
    {
        $amountCharged = $provider->book($location);
        $this->logBookedLocation($location, $amountCharged);
    }
}

$location = 'Hilton, Dallas';
$cheapestProvider = $this->findCheapest($location, array(
    new PricelineProvider(),
    new OrbitzProvider(),
));
$user->bookLocation($cheapestProvider, $location);
```

*接口实际上不做任何实际的业务逻辑，它只是简单定义了类必须实现的一系列方法。*


### 4.服务提供者

> 一个Laravel服务提供者就是一个用来提供 IoC 绑定的类。 Laravel 有好几十个服务提供者，用于管理框架核心组件的容器绑定。
> 几乎框架中的每一个组件的 IoC 绑定都是靠服务提供者来实现的。`app/config/app.php` 下可查看目前框架提供的服务提供者。

**服务提供者**
1. 一个服务提供者必须有一个 `register` 方法，该方法中可以实现 IoC 绑定。
2. 程序框架刚启动时，所有在配置文件中的服务提供者的 `register` 方法会被调用。
3. 该过程在程序周期的初始阶段就会被执行，入口文件中 `$app = require_once __DIR__.'/../bootstrap/app.php';`。


**Register VS Boot**

> 永远不要在 `register` 方法里面使用任何服务。该方法只是用来进行 IoC 绑定。所有关于绑定类的后续判断、交互都要在 `boot` 方法里进行。
> 服务提供者只是一个用来自动初始化服务组件的地方，一个方便管理引导代码和容器绑定的地方。


```php
interface EventPusherInterface
{
    public function push($message,array $data = []);
}

interface PusherSdkInterface
{

}

class PusherEventPusher implements EventPusherInterface
{
    protected $pusher;

    public function __construct(PusherSdkInterface $pusher)
    {
        $this->pusher = $pusher;
    }


    public function push($message, array $data = [])
    {
        // 通过 Pusher SDK 推送消息
    }
}

class EventPusherServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 是否使用单例的方式取决于一次请求周期中该类是否只需要有一个实例
        $this->app->singleton(PusherSdkInterface::class, function () {
            return new Pusher('app_key', 'secret_key', 'app_id');
        });
        $this->app->singleton(EventPusherInterface::class, PusherEventPusher::class);
    }
}
```

*服务提供者仅仅是应用里启动代码和管理代码的工具。* 

**服务提供者的启动过程**
1. 在所有服务提供者都注册以后，提供者们就进入了“启动”过程。该过程会触发每个服务提供者的 `boot` 方法。
2. 由于在 `register` 方法中不能保证所有其他服务都已经被加载，所以在该方法中调用其他的服务有可能会出错。
3. 若需要在服务提供者中调用其他服务，可以在 `boot` 方法中进行调用，`register` 方法仅专注于实现容器注册。
4. 在启动方法中可以实现：注册事件监听、引入路由文件、注册过滤器等等...

```php
public function boot()
{
    require_once __DIR__.'/events.php';
    require_once __DIR__.'/routes.php';
}
```

### 5.应用结构

> 不要惧怕建立目录来管理应用。应该尽量将应用切割成小组件，每个组件都应该有十分专注的职责。
> 优化应用的设计结构的关键就是责任划分，或者说是创建不同的责任层次。
> 编写高可维护性应用程序的关键之一就是责任分隔。

### 6.解耦处理函数
















