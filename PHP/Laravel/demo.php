<?php
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

//    public function billUser($user)
//    {
//        $this->biller->bill($user->getId(), $this->>amount)
//    }
}

// 实现一个 notifier
class SmsNotifier implements BillingNotifierInterface
{

    public function notify(array $user, $amount)
    {
        // TODO: Implement notify() method.
    }
}

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

$reflection = new ReflectionClass(StripeBiller::class);
var_dump($reflection->getMethods());
var_dump($reflection->getConstants());

// 将 StripeBiller 绑定到接口 BillerInterface 上
// 告诉容器 从事使用 StripeBiller 作为 BillerInterface 的实现类
App::bind('BillerInterface', 'StripeBiller');
App::singleton('BillerInterface', 'StripeBiller');  // 单例模式

interface ProviderInterface {
    public function getLowestPrice($location);
    public function book($location);
}

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



