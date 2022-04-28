## SQL进阶教程

### 1. 神奇的SQL

#### 1.1 CASE表达式

*CASE表达式的写法：*

```sql
-- 简单CASE表达式
CASE sex
    WHEN 1 THEN 'male'
    WHNE 2 THEN 'female'
ELSE 'unknown' END
-- 搜索表达式
CASE
    WHEN sex = 1 THEN 'male'
    WHEN sex = 2 THEN 'female'
ELSE 'unknown' END 
```

> 我们在编写SQL语句的时候需要注意，在发现为真的WHEN子句时，CASE表达式的真假值判断就会中止，而剩余的WHEN子句会被忽略。
> 为了避免引起不必要的混乱，使用WHEN子句时要注意条件的排他性。

```sql
-- 剩余的 WHEN 子句被忽略的写法
CASE 
    WHEN col_1 IN ('a', 'b') THEN 'The first'
    WHEN col_2 IN ('a') THEN 'The second'   -- 该条件被忽略 不会执行
ELSE 'others' END
```

**使用CASE表达式时需注意：**
1. 统一各分支返回的数据类型
2. 不要忘记写 `END`
3. 养成写 ELSE 子句的习惯

> `GROUP BY` 在某些情况下(PostgreSQL/MySQL)中可以使用 `SELECT` 子句中定义的列的别称。
> 但是这种写法是违反标准 SQL 规则的。因为 `GROUP BY` 语句是优先于 `SELECT` 语句先执行的，所以在 `GROUP BY` 子句中引用 `SELECT` 子句里定义的别称是不被允许的。

```sql
-- 将县编号转换成地区编号
SELECT CASE perf_name
    WHEN '德岛' THEN '四国'
    WHEN '香川' THEN '四国'
    WHEN '爱媛' THEN '四国'
    WHEN '高知' THEN '四国'
    WHEN '福冈' THEN '九州'
    WHEN '佐贺' THEN '九州'
    WHEN '长崎' THEN '九州'
ELSE '其他' END AS district, SUM(population)
FROM `poptbl`
GROUP BY CASE perf_name
    WHEN '德岛' THEN '四国'
    WHEN '香川' THEN '四国'
    WHEN '爱媛' THEN '四国'
    WHEN '高知' THEN '四国'
    WHEN '福冈' THEN '九州'
    WHEN '佐贺' THEN '九州'
    WHEN '长崎' THEN '九州'
ELSE '其他' END;

SELECT CASE
    WHEN population < 100 THEN '01'
    WHEN population >= 100 AND population < 200 THEN '02'
    WHEN population >= 200 AND population < 300 THEN '03'
    WHEN population >= 300 THEN '04'
    ELSE NULL END AS pop_class,
    COUNT(*) AS cnt
FROM `poptbl`
GROUP BY CASE
    WHEN population < 100 THEN '01'
    WHEN population >= 100 AND population < 200 THEN '02'
    WHEN population >= 200 AND population < 300 THEN '03'
    WHEN population >= 300 THEN '04'
    ELSE NULL END;

-- 简化上述写法
SELECT CASE perf_name
           WHEN '德岛' THEN '四国'
           WHEN '香川' THEN '四国'
           WHEN '爱媛' THEN '四国'
           WHEN '高知' THEN '四国'
           WHEN '福冈' THEN '九州'
           WHEN '佐贺' THEN '九州'
           WHEN '长崎' THEN '九州'
           ELSE '其他' END AS district, SUM(population)
FROM `poptbl`
GROUP BY district;
```