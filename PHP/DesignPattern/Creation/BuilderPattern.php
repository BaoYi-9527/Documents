<?php

/**
 * 生成器模式：
 * 该模式是一种创建型设计模式， 使你能够分步骤创建复杂对象。 该模式允许你使用相同的创建代码生成不同类型和形式的对象。
 *
 * 应用场景：
 * + ①使用生成器模式可避免 “重叠构造函数 （telescopic constructor）” 的出现。
 * + ②当你希望使用代码创建不同形式的产品 （例如石头或木头房屋） 时， 可使用生成器模式。
 * + ③使用生成器构造组合树或其他复杂对象。
 *
 * 实现方法：
 * + ①清晰地定义通用步骤， 确保它们可以制造所有形式的产品。 否则你将无法进一步实施该模式。
 * + ②在基本生成器接口中声明这些步骤。
 * + ③为每个形式的产品创建具体生成器类， 并实现其构造步骤。
 * + ④考虑创建主管类。 它可以使用同一生成器对象来封装多种构造产品的方式。
 * + ⑤客户端代码会同时创建生成器和主管对象。 构造开始前， 客户端必须将生成器对象传递给主管对象。
 * 通常情况下， 客户端只需调用主管类构造函数一次即可。 主管类使用生成器对象完成后续所有制造任务。
 * 还有另一种方式， 那就是客户端可以将生成器对象直接传递给主管类的制造方法。
 * + ⑥只有在所有产品都遵循相同接口的情况下， 构造结果可以直接通过主管类获取。 否则， 客户端应当通过生成器获取构造结果。
 *
 * 优缺点：
 * + 你可以分步创建对象， 暂缓创建步骤或递归运行创建步骤。
 * + 生成不同形式的产品时， 你可以复用相同的制造代码。
 * + 单一职责原则。 你可以将复杂构造代码从产品的业务逻辑中分离出来。
 * - 由于该模式需要新增多个类， 因此代码整体复杂程度会有所增加。
 *
 */

# 生成器最好的应用方式就是SQL查询生成器
interface SQLQueryBuilder
{
    public function select(string $table, array $fields): SQLQueryBuilder;

    public function where(string $field, string $value, string $operator = '='): SQLQueryBuilder;

    public function limit(int $start, int $offset): SQLQueryBuilder;

    //...

    public function getSQl(): string;
}

class MySQLQueryBuilder implements SQLQueryBuilder
{
    protected $query;

    protected function reset(): void
    {
        $this->query = new \stdClass();
    }

    public function select(string $table, array $fields): SQLQueryBuilder
    {
        $this->reset();;
        $this->query->base = "SELECT " . implode(',', $fields) . ' FROM ' . $table;
        $this->query->type = "query";

        return $this;
    }

    /**
     * @throws Exception
     */
    public function where(string $field, string $value, string $operator = '='): SQLQueryBuilder
    {
        if (!in_array($this->query->type, ['select', 'update', 'delete'])) {
            throw new \Exception("WHERE can only be added to SELECT、UPDATE or DELETE");
        }
        $this->query->where[] = "$field $operator '$value'";

        return $this;
    }

    /**
     * @throws Exception
     */
    public function limit(int $start, int $offset): SQLQueryBuilder
    {
        if ($this->query->type != 'select') {
            throw new \Exception("LIMIT can only be added to SELECT");
        }
        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

    public function getSQl(): string
    {
        $query = $this->query;
        $sql   = $this->query->base;
        if (!empty($this->query->where)) {
            $sql .= " WHERE " . implode(" AND ", $query->where);
        }
        if (isset($query->limit)) {
            $sql .= $query->limit;
        }
        $sql .= ';';
        return $sql;
    }
}

class PostgresQueryBuilder extends MySQLQueryBuilder
{
    # Postgres limit 与 MySQL 的 limit SQL语法不同 重写覆盖该语法
    public function limit(int $start, int $offset): SQLQueryBuilder
    {
        parent::limit($start, $offset);

        $this->query->limit = " LIMIT " . $start . " OFFSET " . $offset;

        return $this;
    }

    //...other codes
}

function clientCode(SQLQueryBuilder $queryBuilder)
{
    $query = $queryBuilder
        ->select("users", ["name", "email", "password"])
        ->where("age", 18, ">")
        ->where("age", 30, "<")
        ->limit(10, 20)
        ->getSQL();

    echo $query;
}