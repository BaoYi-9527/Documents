<?php
/**
 * 工厂模式:
 * 该模式建议使用特殊的工厂方法代替对于对象构造函数的直接调用 （即使用 new运算符）。 
 *
 * 应用场景：
 * ①当你在编写代码的过程中， 如果无法预知对象确切类别及其依赖关系时， 可使用工厂方法。
 * ②如果你希望用户能扩展你软件库或框架的内部组件， 可使用工厂方法。
 * ③如果你希望复用现有对象来节省系统资源， 而不是每次都重新创建对象， 可使用工厂方法。
 *
 * 优缺点：
 * + 你可以避免创建者和具体产品之间的紧密耦合。
 * + 单一职责原则。 你可以将产品创建代码放在程序的单一位置， 从而使得代码更容易维护。
 * + 开闭原则。 无需更改现有客户端代码， 你就可以在程序中引入新的产品类型。
 * - 应用工厂方法模式需要引入许多新的子类， 代码可能会因此变得更复杂。 最好的情况是将该模式引入创建者类的现有层次结构中。
 *
 */


namespace DesignPattern\Creation\FactoryPattern;

# 网上购物工厂
abstract class ShoppingOnline
{
    # 加入购物车
    abstract public function addToShoppingCart();
    # 下订单
    abstract public function order();
    # 付款
    abstract public function payment();
    # 配送
    abstract public function delivery();
    # 售后
    abstract public function afterSale();
}

# 淘宝购物
class ShoppingOnTaoBao extends ShoppingOnline
{
    # 淘宝购物的具体实现
    public function addToShoppingCart()
    {
        // TODO: Implement addToShoppingCart() method.
    }

    public function order()
    {
        // TODO: Implement order() method.
    }

    public function payment()
    {
        // TODO: Implement payment() method.
    }

    public function delivery()
    {
        // TODO: Implement delivery() method.
    }

    public function afterSale()
    {
        // TODO: Implement afterSale() method.
    }
}

# 京东购物
class ShoppingOnJD extends ShoppingOnline
{
    # 京东购物的具体实现
    public function addToShoppingCart()
    {
        // TODO: Implement addToShoppingCart() method.
    }

    public function order()
    {
        // TODO: Implement order() method.
    }

    public function payment()
    {
        // TODO: Implement payment() method.
    }

    public function delivery()
    {
        // TODO: Implement delivery() method.
    }

    public function afterSale()
    {
        // TODO: Implement afterSale() method.
    }
}

# 应用场景
class ShoppingService
{
    # 购物流程
    public function shopping(ShoppingOnline $shoppingOnline)
    {
        $shoppingOnline->addToShoppingCart();
        $shoppingOnline->order();
        $shoppingOnline->payment();
        $shoppingOnline->delivery();
        $shoppingOnline->afterSale();
    }

    # 实现
    public function goShopping()
    {
        # 淘宝购物
        $this->shopping(new ShoppingOnTaoBao());
        # 京东购物
        $this->shopping(new ShoppingOnJD());
    }
}