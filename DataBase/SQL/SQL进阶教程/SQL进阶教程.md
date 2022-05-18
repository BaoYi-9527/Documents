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
    ELSE '其他' END AS district,
    SUM(population)
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

-- 按人口数量等级划分都道府县
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
-- 可以对定义的列使用别称，这样可以方便在后续 GROUP BY 中直接调用
SELECT CASE perf_name
    WHEN '德岛' THEN '四国'
    WHEN '香川' THEN '四国'
    WHEN '爱媛' THEN '四国'
    WHEN '高知' THEN '四国'
    WHEN '福冈' THEN '九州'
    WHEN '佐贺' THEN '九州'
    WHEN '长崎' THEN '九州'
    ELSE '其他' END AS district,
    SUM(population)
FROM `poptbl`
GROUP BY district;
```

```sql
-- 男性人口
SELECT perf_name,
    SUM(population)
FROM `poptbl2`
WHERE `sex` = 1
GROUP BY perf_name;
-- 女性人口
SELECT perf_name,
    SUM(population)
FROM `poptbl2`
WHERE `sex` = 2
GROUP BY perf_name;

-- 简化上述写法
SELECT perf_name,
    -- 男性人口
    SUM(CASE WHEN `sex` = 1 THEN population ELSE 0 END) AS cnt_m,
    -- 女性人口
    SUM(CASE WHEN `sex` = 2 THEN population ELSE 0 END) AS cnt_f
FROM `poptbl2`
GROUP BY perf_name;
```

***用CHECK约束定义多个列的条件关系**

```sql
-- 逻辑与和蕴含式
CONSTRAINT check_salary CHECK
	(
		CASE WHEN sex = '2' 
			THEN CASE WHEN salary <= 200000
				THEN 1 ELSE 0 END
			ELSE 1 END = 1;
	);

CONSTRAINT check_salary CHECK
	(sex = '2' AND salary <= 200000);
```

**在UPDATE语句中使用条件分支**

```sql
-- 调薪
UPDATE `salaries`
    SET salary = CASE WHEN salary >= 300000
                      THEN salary * 0.9
                      WHEN salary >= 250000 AND salary < 280000
                      THEN salary * 1.2
                      ELSE salary END;

UPDATE `sometable`
    SET p_key = CASE WHEN p_key = 'a' THEN 'b'
                     WHEN p_key = 'b' THEN 'a'
                     ELSE p_key END
WHERE p_key IN ('a', 'b');
```

**表之间的数据匹配**

```sql
-- 表的匹配：使用IN谓词
SELECT course_name,
    CASE WHEN `course_id` IN
             (SELECT `course_id` FROM `open_course` WHERE `month` = 200706) THEN '○'
        ELSE '×' END AS '6月',
    CASE WHEN `course_id` IN
             (SELECT `course_id` FROM `open_course` WHERE `month` = 200707) THEN '○'
        ELSE '×' END AS '7月',
    CASE WHEN `course_id` IN
             (SELECT `course_id` FROM `open_course` WHERE `month` = 200708) THEN '○'
        ELSE '×' END AS '8月'
FROM `course_master`;
```

**在CASE表达式中使用聚合函数**

```sql
SELECT `std_id`,
	CASE WHEN COUNT(*) = 1
		THEN MAX(club_id)
		ELSE MAX(CASE WHEN main_club_flag = 'Y'
									THEN club_id
									ELSE NULL END)
	END AS main_club
FROM `student_club`
GROUP BY `std_id`;
```

**CASE表达式：**
1. 在 `GROUP BY` 子句中使用 `CASE表达式`，可以灵活地选择作为聚合的单位的编号或等级。
2. 在聚合函数中使用CASE表达式，可以轻松地将行结构的数据转换成列结构的数据。
3. 相反，聚合函数也可以嵌套进CASE表达式里使用。
4. 相比依赖于具体数据库的函数，CASE表达有更强大的表达能力和更好的可移植性。
5. CASE表达式是一种表达式而不是语句，在执行时会被判定为一个固定值。

### 2.自连接的用法



