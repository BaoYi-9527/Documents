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

### 接口约定

