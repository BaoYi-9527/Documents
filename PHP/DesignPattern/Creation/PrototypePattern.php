<?php

/**
 * 原型模式：
 * 该模式是一种创建型设计模式， 使你能够复制已有对象， 而又无需使代码依赖它们所属的类。
 *
 * 原型模式将克隆过程委派给被克隆的实际对象。 模式为所有支持克隆的对象声明了一个通用接口， 该接口让你能够克隆对象， 同时又无需将代码和对象所属类耦合。
 * 通常情况下， 这样的接口中仅包含一个 克隆方法。
 * 创建一系列不同类型的对象并不同的方式对其进行配置。 如果所需对象与预先配置的对象相同， 那么你只需克隆原型即可， 无需新建一个对象。
 *
 * 应用场景：
 * + ①如果你需要复制一些对象， 同时又希望代码独立于这些对象所属的具体类， 可以使用原型模式。
 * + ②如果子类的区别仅在于其对象的初始化方式， 那么你可以使用该模式来减少子类的数量。 别人创建这些子类的目的可能是为了创建特定类型的对象。
 *
 * 实现方式：
 * + ①创建原型接口， 并在其中声明 克隆方法。 如果你已有类层次结构， 则只需在其所有类中添加该方法即可。
 * + ②原型类必须另行定义一个以该类对象为参数的构造函数。 构造函数必须复制参数对象中的所有成员变量值到新建实体中。
 *   如果你需要修改子类， 则必须调用父类构造函数， 让父类复制其私有成员变量值。
 *   如果编程语言不支持方法重载， 那么你可能需要定义一个特殊方法来复制对象数据。 在构造函数中进行此类处理比较方便， 因为它在调用 new运算符后会马上返回结果对象。
 * + ③克隆方法通常只有一行代码： 使用 new运算符调用原型版本的构造函数。
 * + ④你还可以创建一个中心化原型注册表， 用于存储常用原型。
 *
 * 优缺点：
 * + 你可以克隆对象， 而无需与它们所属的具体类相耦合。
 * + 你可以克隆预生成原型， 避免反复运行初始化代码。
 * + 你可以更方便地生成复杂对象。
 * + 你可以用继承以外的方式来处理复杂对象的不同配置。
 * - 克隆包含循环引用的复杂对象可能会非常麻烦。
 */

namespace DesignPattern\Creation\PrototypePattern;

class Page
{
    private $title;
    private $body;
    private $author;
    private $comments = [];
    private $date;

    public function __construct(string $title, string $body, Author $author)
    {
        $this->title  = $title;
        $this->body   = $body;
        $this->author = $author;
        $this->author->addToPage($this);
        $this->date = new \DateTime();
    }

    public function addComment(string $comment)
    {
        $this->comments[] = $comment;
    }

    # 当且仅当对象被 clone 时调用
    public function __clone()
    {
        $this->title = 'Copy of ' . $this->title;
        $this->author->addToPage($this);
        $this->comments = [];
        $this->date     = new \DateTime();
    }

}

class Author
{
    private $name;
    private $pages = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addToPage(Page $page)
    {
        $this->pages[] = $page;
    }
}

function clientCode()
{
    $author = new Author("John Smith");
    $page   = new Page('Tip of the day', 'Keep clam and carry on.', $author);

    //...

    $page->addComment('Nice Tip, thanks!');

    //...

    $draft = clone $page;
    echo 'Here is the clone Prototype pattern example:' . PHP_EOL;
    var_dump($draft);
}

clientCode();

