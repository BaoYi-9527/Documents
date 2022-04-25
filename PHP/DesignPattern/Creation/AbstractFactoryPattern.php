<?php

/**
 * 抽象工厂模式:
 * 该模式是一种创建型设计模式， 它能创建一系列相关的对象， 而无需指定其具体类。
 *
 * 抽象工厂模式建议为每件产品明确声明接口，然后确保所有产品变体都继承这些接口。
 * 我们基于抽象工厂类创建不同的工厂类，每个工厂类只返回特定类别的产品。
 *
 * 应用场景：
 * ①如果代码需要与多个不同系列的相关产品交互，但是由于无法提前获取相关信息，或者处于对未来扩展性的考虑，不希望产品基于具体的类进行构建。
 * （抽象工厂为你提供了一个接口， 可用于创建每个系列产品的对象。 只要代码通过该接口创建对象， 那么你就不会生成与应用程序已生成的产品类型不一致的产品。）
 * ②如果你有一个基于一组抽象方法的类，且其主要功能因此变得不明确。
 * （在设计良好的程序中，每个类仅负责一件事情。如果一个类与多种类型产品交互， 就可以考虑将工厂方法抽取到独立的工厂类或具备完整功能的抽象工厂类中。）
 *
 * 实现方式：
 * ①以不同的产品类型与产品变体为维度绘制矩阵。
 * ②为所有产品声明抽象产品接口。然后让所有具体产品类实现这些接口。
 * ③声明抽象工厂接口，并且在接口中为所有抽象产品提供一组构建方法。
 * ④为每种产品变体实现一个具体工厂。
 * ⑤在应用程序中开发初始化代码。该代码根据应用程序配置或当前环境， 对特定具体工厂类进行初始化。 然后将该工厂对象传递给所有需要创建产品的类。
 * ⑥找出代码中所有对产品构造函数的直接调用， 将其替换为对工厂对象中相应构建方法的调用。
 *
 * 优缺点：
 * + 你可以确保同一工厂生成的产品相互匹配。
 * + 你可以避免客户端和具体产品代码的耦合。
 * + 单一职责原则。 你可以将产品生成代码抽取到同一位置， 使得代码易于维护。
 * + 开闭原则。 向应用程序中引入新产品变体时， 你无需修改客户端代码。
 * - 由于采用该模式需要向应用中引入众多接口和类， 代码可能会比之前更加复杂。
 *
 * 抽象工厂->具体工厂类(实现工厂类[接口]中的方法，并添加自己的风格)->具体产品类
 *
 */

namespace DesignPattern\Creation\TemplateFactory;

# 模板抽象工厂
interface TemplateFactory
{
    public function createTitleTemplate();

    public function createPageTemplate();

    public function getRenderer();
}

interface TitleTemplate
{
    public function getTemplateString(): string;
}

interface PageTemplate
{
    public function getTemplateString(): string;
}

interface RendererTemplate
{
    public function render(string $templateString, array $arguments = []): string;
}

class TwigTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1>{{ title }}}</h1>";
    }
}

class PHPTemplateTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1><?= \$title; ?></h1>";
    }
}

abstract class BasePageTemplate implements PageTemplate
{
    protected $titleTemplate;

    public function __construct(TitleTemplate $titleTemplate)
    {
        $this->titleTemplate = $titleTemplate;
    }
}

class TwigPageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $rendererTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
            <div class="page">
                $rendererTitle
                <article class="content">{{ content }}</article>
            </div>
HTML;
    }
}

class PHPTemplatePageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $rendererTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
            <div class="page">
                $rendererTitle
                <article class="content">{<?= \$content ?></article>
            </div>
HTML;
    }
}

class TwigRenderer implements RendererTemplate
{

    public function render(string $templateString, array $arguments = []): string
    {
        return \Twig::render($templateString, $arguments);
    }
}

class PHPTemplateRenderer implements RendererTemplate
{

    public function render(string $templateString, array $arguments = []): string
    {
        extract($arguments);

        ob_start();
        eval(' ?>' . $templateString . '<?php ');
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}

class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TwigTitleTemplate
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): TwigPageTemplate
    {
        return new TwigPageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TwigRenderer
    {
        return new TwigRenderer();
    }
}

class PHPTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): PHPTemplateTitleTemplate
    {
        return new PHPTemplateTitleTemplate();
    }

    public function createPageTemplate(): PHPTemplatePageTemplate
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): RendererTemplate
    {
        return new PHPTemplateRenderer();
    }
}

class Page
{
    public $title;
    public $content;

    public function __construct($title, $content)
    {
        $this->title   = $title;
        $this->content = $content;
    }

    public function render(TemplateFactory $factory)
    {
        $pageTemplate = $factory->createPageTemplate();
        $renderer     = $factory->getRenderer();

        return $renderer->render($pageTemplate->getTemplateString(), [
            'title'   => $this->title,
            'content' => $this->content
        ]);
    }
}

$page = new Page('Sample Page','This is a test!');

echo 'success!!! This is a PHP abstract factory pattern example:';
echo $page->render(new PHPTemplateFactory());


