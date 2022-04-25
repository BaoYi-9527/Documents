<?php

/**
 * 单例模式：
 * 该模式是一种创建型设计模式， 让你能够保证一个类只有一个实例， 并提供一个访问该实例的全局节点。
 *
 * 应用场景：
 * + ①如果程序中的某个类对于所有客户端只有一个可用的实例， 可以使用单例模式。
 * + ②如果你需要更加严格地控制全局变量， 可以使用单例模式。
 *
 * 实现方式：
 * + ①在类中添加一个私有静态成员变量用于保存单例实例。
 * + ②声明一个公有静态构建方法用于获取单例实例。
 * + ③在静态方法中实现"延迟初始化"。
 *    该方法会在首次被调用时创建一个新对象， 并将其存储在静态成员变量中。 此后该方法每次被调用时都返回该实例。
 * + ④将类的构造函数设为私有。 类的静态方法仍能调用构造函数， 但是其他对象不能调用。
 * + ⑤检查客户端代码， 将对单例的构造函数的调用替换为对其静态构建方法的调用。
 *
 * 优缺点：
 * + 你可以保证一个类只有一个实例。
 * + 你获得了一个指向该实例的全局访问节点。
 * + 仅在首次请求单例对象时对其进行初始化。
 * - 违反了_单一职责原则_。 该模式同时解决了两个问题。
 * - 单例模式可能掩盖不良设计， 比如程序各组件之间相互了解过多等。
 * - 该模式在多线程环境下需要进行特殊处理， 避免多个线程多次创建单例对象。
 * - 单例的客户端代码单元测试可能会比较困难， 因为许多测试框架以基于继承的方式创建模拟对象。
 *   由于单例类的构造函数是私有的， 而且绝大部分语言无法重写静态方法， 所以你需要想出仔细考虑模拟单例的方法。 要么干脆不编写测试代码， 或者不使用单例模式。
 */


namespace DesignPattern\Creation\SingletonPattern;

class Singleton
{
    private static $instances = [];

    # 单例不允许通过构造函数或者克隆生成
    protected function __construct() {}
    protected function __clone() {}

    /**
     * 单例不允许反序列化
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        $subClass = static::class; # 后期静态绑定
        if (!isset(self::$instances[$subClass])) {
            # 此处使用 static 关键字而不是确切的类名
            # 是因为我们使用的是当前运行环境中的类，而不是基类，这样当继承自基类的子类被调用时就可以保证单例的实现
            self::$instances[$subClass] = new static();
        }
        return self::$instances[$subClass];
    }

}

class Logger extends Singleton
{
    private $fileHandle;

    protected function __construct()
    {
        parent::__construct();
        $this->fileHandle = fopen('php://stdout', 'w');
    }

    public function writeLog(string $message)
    {
        $date = date('Y-m-d H:i:s');
        fwrite($this->fileHandle, "[$date]" . ': ' . $message . PHP_EOL);
    }

    public static function log(string $message)
    {
        $logger = self::getInstance();
        $logger->writeLog($message);
    }
}

class Config extends Singleton
{
    private $hashMap = [];

    public function getValue(string $key): string
    {
        return $this->hashMap[$key];
    }

    public function setValue(string $key, string $value)
    {
        $this->hashMap[$key] = $value;
    }
}

Logger::log('Started!!!');

# 比较俩个日志单例的差异
$l1 = Logger::getInstance();
$l2 = Logger::getInstance();
if ($l1 === $l2) {
    Logger::log('Logger has a single Instance.');
} else {
    Logger::log('Loggers are different.');
}

# 配置单例类存储数据
$config1 = Config::getInstance();
$login = 'test_login';
$password = 'test_password';
$config1->setValue('login', $login);
$config1->setValue('password', $password);
# 取
$config2 = Config::getInstance();
if ($login == $config2->getValue('login') &&
    $password == $config2->getValue('password')
) {
    Logger::log('Config singleton works fine.');
}

Logger::log('Finished!!!');

