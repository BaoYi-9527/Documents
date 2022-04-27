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