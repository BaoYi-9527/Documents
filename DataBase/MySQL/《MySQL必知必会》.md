[TOC]

## MySQL必知必会

> 文档约定：
>
> 1. SQL语句为大写，数据库、表、字段名为小写；

### 1. 常用概念

#### 1.1 主键(primary key)

一列（或一组列），其值能够唯一区分表中每一行。

**作为主键的列应该满足的条件：**

1. 任意俩行都不具有相同的主键值；
2. 每个行都必须具有一个主键值（主键不允许 NULL 值）；

**主键的使用：**

+ 不更新主键列中的值；
+ 补重用主键列的值；
+ 不再主键列中使用可能会更改的值；

***

### 2. MySQL简介

一种DBMS（数据库管理系统）。

**MySQL命令行简单使用：**

+ 连接MySQL：mysql -u root -p -h localhost -P 9999
+ MySQL命令行选项和参数列表：msyql --help
+ 命令输入在 msyql>之后；
+ 命令用 ; 或 \g 结束；
+ 输入 help 或 \h 获得帮助；
+ 输入 quit 或 exit 退出命令行实用程序；

***

### 3. 使用MySQL

#### 3.1 连接MySQL

 ```mysql
mysql -u root -p -h localhost -P 9999
 ```



#### 3.2 数据库与数据表相关命令

+ 选择数据库：USE database_name;

+ 数据库列表：SHOW DATABASES;

+ 数据表列表：SHOW TABLES;

+ 字段列表：SHOW COLUMNS FROM table_name; 或 DESCRIBE table_name;

+ 显示服务器状态：SHOW STATUS;

+ 显示创建特定数据库/数据表的MySQL语句：

  SHOW CREATE DATABASE;

  SHOW CREATE TABLE;

+ 显示授予用户的安全权限：SHOW GRANTS;

+ 显示服务器错误或警告：

  SHOW ERRORS;

  SHOW WARNINGS;

***

### 4. 检索数据

#### 4.1 SELECT语句

1. 检索单个列：

   ```
   SELECT column_name FROM table_name;
   ```

   

2. 检索多个列：

   ```
   SELECT column_one,column_tow,column_three FROM table_name;
   ```

   

3. 检索所有列：

   ```
   SELECT * FROM table_name;
   ```

   

4. 检索不同的行（去重）：

   ```mysql
SELECT DISTINCT column_name FROM table_name;
   
   //去重的是column_one和column_two字段同时相同的数据；
   SELECT DISTINCT column_one,column_two FROM table_name;
   ```
   
5. 限制结果：

   ```
SELECT column_name FROM table_name LIMIT 5;
   SELCT column_name FROM table_name LIMIT 5,5;
SELCT column_name FROM table_name LIMIT 5 OFFSET 4;
   ```

6. 使用完全限定的表名：

   ```
SELECT table_name.column_name FROM database_name.table_name;
   ```

   

> 1. SQL语句并不会区分大小写，但大多数开发人员是对SQL关键字进行大写，列和表名使用小写；
>
> 2. 处理SQL语句时，所有的空格都会被忽略；
> 3. 滥用通配符*并不是一种好的查询习惯，检索不需要的列会降低检索和应用程序的性能；
> 4. DISTINCT 关键字不能局部使用，使用时会应用于所有被指定的列，而不仅是前置列；
> 5. SELECT 检索出来的第一行是行0而不是行1，所有 LIMIT 1,1 将检索第2行而不是第1行；

#### 4.2 ORDER子句排序数据

> + 如果不对数据进行排序，数据将以它在底层表中出现的顺序显示。
> + **子句：**SQL子句由子句构成（有些子句并非必须的），一个子句通常由一个关键字和所提供的数据组成。

1. ORDER BY 子句可以取一个或多个列的名字，据此对输出进行排序。

   ```sql
   SELECT column_name from table_name ORDER BY column_name;
   ```

   

2. 按多个列排序（列名之间用逗号分开即可），排序将完全按所规定的顺序进行：

   ```sql
   SELECT column_one,column_two,column_three FROM table_name ORDER BY column_one,column_two;
   ```

   

3. 指定排序方向：

   ORDER BY 默认排序方式是按升序(ASC)，若需要降序排序，必须指定DESC关键字。

   ```sql
   SELECT column_one,column_two,column_three FROM table_name ORDER BY column_one DESC;
   
   //仅column_one以降序排序，column_two仍旧以升序排序。
   SELECT column_one,column_two,column_three FROM table_name ORDER BY column_one DESC,column_two;
   ```

   

4. 使用 ORDER BY 和 LIMIT 组合查询列中最高或最低值：

   ```sql
   SELECT column_one FROM table_name ORDER BY column_one DESC LIMIT 1;
   ```
   
   

> + 若想要多个列上进行降序排序，必须对每个列指定 DESC 关键字。
> + 在字典排序顺序中，A被视为与a相同的，这是MySQL（和大多数数据管理系统）的默认行为。
> + ORDER BY 子句应保证位于 FROM 子句之后，而 LIMIT 必须位于 ORDER BY 之后。

#### 4.3 WHERE子句过滤数据

1. 在 SELECT 语句中，数据根据 WHERE 子句指定的搜索条件进行过滤。

   ```sql
   SELECT column_one,column_two FROM table_name WHERE column_one = 2.50;
   ```

   

2. 检索单个值：

   ```sql
   SELECT column_one,column_two FROM table_name WHERE column_one = 'jack';
   ```

   

3. 不匹配检查：

   ```sql
   SELECT column_one,column_two FROM table_name WHERE column_one <> 10086;
   ```

   

4. 范围值检查：

   ```sql
   SELECT column_one,column_two FROM table_name WHERE column_one BETWEEN 5 AND 10;
   ```

   

5. 空值检查：

   SELECT 语句的特殊WHERE子句，IS NULL 用于检查具有 NULL 值的列。

   ```sql
   SELECT column_one FROM table_name WHERE column_one IS NULL;
   ```
   
   

**WHERE子句操作符**

| 操作符  |        说明        |
| :-----: | :----------------: |
|    =    |        等于        |
|   <>    |       不等于       |
|   !=    |       不等于       |
|    <    |        小于        |
|   <=    |      小于等于      |
|    >    |        大于        |
|   >=    |      大于等于      |
| BETWEEN | 在指定的俩个值之间 |

> + SQL过滤与应用过滤：在客户机上进行过滤，会极大的影响应用的性能，并且使得所创建的应用完全不具备可伸缩性。如果在客户机上过滤数据，服务器不得不通过网络发送多余的数据，这将导致网络带宽的浪费。
> + ORDER BY 子句应位于 WHERE 子句之后。
> + MySQL 在执行匹配时默认不区分大小写。
> + SQL中的单引号 '' 用来限制字符串，若需要将值与串类型的列进行比较，则需要限定引号，用来与数值列进行比较的值则不用引号。
> + NULL：无值（no value），与字段包含0，空字符串或仅仅包含空格不同。因其具有特殊的含义，在匹配过滤或者不匹配过滤时不返回该值。

#### 4.5 WHERE子句组合过滤数据

**操作符**：用于联结或改变WHERE子句中的子句的关键字。也成为逻辑关键符（logical operator）。

1. AND操作符：

   用于检索满足所有给定条件的行；

   ```sql
   SELECT column_one,column_two,column_three FROM table_name WHERE column_one = 10010 AND column_two = 10086;
   ```

   

2. OR操作符：

   用于检索匹配任一给定条件的行；

   ```sql
   SELECT column_one,column_two,column_three FROM table_name WHERE column_one = 10086 OR column_two = 10010;
   ```

   

3. 计算次序：

   SQL在处理OR操作符前会优先处理AND操作符；

   可以使用圆括号明确的分组相应的操作符；

   ```sql
   SELECT column_one,column_two FROM table_name WHERE (column_one = 10010 OR column_two = 10086) AND column_three >= 10;
   ```

   

4. IN操作符：

   用于指定条件范围，范围中的每个条件都可以进行匹配。IN取合法值得由逗号分隔的清单，全都括在圆括号中。

   IN操作符完成的是与OR相同的功能；

   ```sql
   SELECT column_one,column_two FROM table_name WHERE column_one IN (10086,10010) ORDER BY column_two;
   ```

   

5. NOT操作符：

   用于否定后跟条件的关键字；

   ```sql
   SELECT column_one,column_two FROM table_name WHERE column_one NOT IN (10010,10086) ORDER BY column_two;
   ```
   
   

> + 任何时候使用具有AND和OR操作符的WHERE子句，都应该使用圆括号明确地分组操作符；
> + 使用IN操作符的优点：
>   1. 在使用长的合法选项清单时，IN操作符的语法更清楚且直观；
>   2. IN操作符的计算次序更容易管理；
>   3. IN操作符比OR操作符执行要快；
>   4. IN操作符中可以包含其他SELECT语句，能够更动态的创建WHERE子句；
> + MySQL支持使用NOT对IN、BETWEEN和EXITSTS子句取反。

#### 4.6 使用通配符过滤数据

**通配符：**用来匹配值的一部分的特殊字符；

**搜索模式：**由字面值、通配符或俩者组合构成的搜索条件；

1. LIKE操作符：

   指示MySQL，后跟的搜索模式利用通配符匹配，而不是直接相等匹配进行比较；

2. 百分号（%）操作符：

   % 表示任何字符出现任意次数；

   ```sql
   SELECT column_one,column_two FROM table_name WHERE column_one LIKE 'ch%';
   ```

   

3. 下划线（_）通配符：

   _ 只匹配单个字符而不是多个字符，即总是匹配一个，不多不少。

   ```
   SELECT column_one,column_two FROM table_name WHERE column_one LIKE '_ china';
   ```
   
   

*关于通配符的使用：*

+ 不要过度使用通配符，因为通配符的搜索处理比一般的搜索所花的时间要长；
+ 除非绝对有必要，否则不要把通配符用在搜索模式的开始处；
+ 仔细注意通配符的位置；

> + 从技术上说，LIKE是谓词而不是操作符；
> + 尾空格可能会干扰通配符匹配，也就是尾空格可能被视为数据的一部分；
> + NULL 无法被通配符匹配；

***

### 5. MySQL正则表达式

>+ 正则表达式用于匹配文本的特殊的串（字符集合）。
>+ MySQL仅支持多数正则表达式实现的一个很小的子集。

#### 5.1 基本字符匹配

```sql
SELECT column_name FROM table_name WHERE column_name REGEXP 'pattern';
```



REGEXP 后跟的东西作为正则表达式处理；

```sql
SELECT column_name FROM table_name WHERE column_name REGEXP '.10086' ORDER BY column_name;
```



. 是正则表达式语言中的一个特殊字符，表匹配任意一个字符。

#### 5.2 OR匹配

```sql
SELECT column_name FROM table_name WHERE column_name REGEXP '10086|10010';
```



| 为正则表达式的OR操作符，表示匹配其中之一；

*匹配几个字符之一：*

```sql
SELECT column_name FROM table_name WHERE column_name REGEXP '[123] china';

SELECT column_name FROM table_name WHERE column_name REGEXP '1|2|3 china';
```



这俩行语句的区别就是，前者的解释是匹配 1 china 或 2 china 或 3 china，后者的解释是匹配 1 或 2 或 3 china。

*字符集合可以被否定：*

[^123] 用于否定除这些字符外的任何字符；

#### 5.3 匹配范围

```sql
SELECT column_name FROM table_name WHERE column_name REGEXP '[1-8] china';
```



#### 5.4 匹配特殊字符

为匹配特殊字符必须用 \\ 为前导；

```sql
SELECT column_name FROM table_name WHERE column_name REGEXP '\\.';
```



这种方式其实也就是所谓的转义，特殊字符包括 . | [] 等。

\\ 同时也可以用于引用元字符：

| 元字符 | 描述     |
| ------ | -------- |
| \\\f   | 换页     |
| \\\n   | 换行     |
| \\\r   | 回车     |
| \\\t   | 制表     |
| \\\v   | 纵向制表 |

**为什么需要使用 \\\ 转义：**

大多数正则表达式都是使用 \\ 转义特殊字符，但MySQl中使用 \\\ 的原因是MySQL需要自己解释一个，正则表达式库需要解释另一个。

#### 5.5 字符类

| 字符类     | 描述                                           |
| ---------- | ---------------------------------------------- |
| [:alnum:]  | [a-zA-Z0-9]                                    |
| [:alpha:]  | [a-zA-Z]                                       |
| [:digit:]  | [0-9]                                          |
| [:lower:]  | [a-z]                                          |
| [:upper:]  | [A-Z]                                          |
| [:xdigit:] | 十六进制数字[a-fA-F0-9]                        |
| [:blank:]  | 空格和制表[\\\t]                               |
| [:cntrl:]  | ASCII控制字符（0-31和127）                     |
| [:graph:]  | 与 [:print:] 相同，但不包含空格                |
| [:print:]  | 任意可打印的字符                               |
| [:punct:]  | 既不在 [:alnum:] 又不在 [:cntrl:] 中的任意字符 |
| [:space:]  | [\\f\\n\\r\\t\\v]                              |

#### 5.5 重复元字符

| 元字符 | 描述                         |
| ------ | ---------------------------- |
| *      | 0或多个匹配                  |
| +      | 1或多个匹配                  |
| ?      | 0或1个匹配                   |
| {n}    | 指定数目的匹配               |
| {n,}   | 不少于指定数目的匹配         |
| {n,m}  | 匹配数目的范围（m不超过255） |

```
SELECT column_name FROM table_name WHERE column_name REGEXP '[[:digit:]{4}]';

SELECT column_name FROM table_name WHERE column_name REGEXP '\\([0-9] sticks?\\)'
```



#### 5.6 定位元字符

| 元字符  | 描述     |
| ------- | -------- |
| ^       | 文本开始 |
| $       | 文本结尾 |
| [[:<:]] | 词的开始 |
| [[:>:]] | 词的结尾 |

```sql
SELECT column_name FROM table_name WHERE column_name REGEXP '^[0-9\\.]';
```



** ^ 的双重用途：**在集合（ [] ）中用于否定该集合，否则用于指定串的开始处。



> **LIKE和REGEXP的区别：**
>
> + LIKE会匹配整个列，且需要配合通配符使用，否则将不会匹配到任何数据；
> + REGEXP则会在列值内进行匹配，匹配的字符串在列中出现就会被返回；
> + REGEXP可以使用 ^ 和 $ 定位符来匹配整个列值；
>
> **大小写：**使用 BINARY 关键字可以区分大小写；
>
> ```sql
> WHERE column_name REGEXP BINARY 'Hello .10086';
> ```
>
> 

***

### 6. 计算字段

*使用场景：*

+ 当需要直接从数据库中检索出转换、计算或格式化过的数据；而不是检索出数据后再在客户机应用程序或报告程序中重新格式化。

+ 数据库服务器上完成这些操作比在客户机上完成要快的多，因为DBMS正是设计来快速有效地完成这种处理的。

#### 6.1 拼接字段（Concat）

将值联结到一起构成单个值。

```sql
SELECT Concat(column_one,'(',column_two,')') FROM table_name ORDER BY column_name;
```



**Trim函数：**

+ RTrim()：去除串右边的空格；
+ LTrim()：去除串左边的空格；
+ Trim()：去除串俩边的空格；

```sql
SELECT Concat(RTrim(column_one),'(',LTrim(column_two),')') FROM table_name ORDER BY column_name;
```

> 多数DBMS使用 + 或 || 来实现拼接，MySQL则使用 Concat() 函数来实现。

#### 6.2 别名

SQL支持别名（alias），字段或值的替换名，用 AS 关键字；

```sql
SELECT Concat(RTrim(column_one),'(',LTrim(column_two),')') AS alias_column FROM table_name ORDER BY column_name;
```



*别名的其他用途：*在实际的表列名不符合规定的字符时重新命名它，在原来的名字含混或容易误解时扩充它。

#### 6.3 算术计算

```sql
SELECT column_one,column_two,column_one*column_two AS alias_column FROM table_name WHERE column_three = 10086;
```



**MySQL算术操作符：**

| 操作符 | 描述 |
| ------ | ---- |
| +      | 加   |
| -      | 减   |
| *      | 乘   |
| /      | 除   |

***

### 7. 数据处理函数

*函数没有SQL的可移植性强：*

+ 几乎每种主要的DBMS的实现都支持其他实现不支持的函数，有时差异还很大；
+ 当使用函数时，应该保证做好代码注释；

#### 7.1 常用文本处理函数

| 函数        | 描述              |
| ----------- | ----------------- |
| Length()    | 返回串的长度      |
| Upper()     | 将串转换为大写    |
| Lower()     | 将串转换为小写    |
| Left()      | 返回串左边的字符  |
| Right()     | 返回串右边的字符  |
| LTrim()     | 去除串左边的空格  |
| RTrim()     | 去除串右边的空格  |
| Locate()    | 找出串的一个子串  |
| SubString() | 返回子串的字符    |
| Soundex()   | 返回串的SOUNDEX值 |

*SOUNDEX*：是一种将任何文本串转换为描述其语音表示的字母数字模式的算法。SOUNDEX 考虑了类似的发音字符和音节，使得能对串进行发音比较而不是字母比较。

```sql
SELECT column_one,column_two FROM table_name WHERE Soundex(column_one) = Soundex('china');
```



#### 7.2 常用日期和时间处理函数

| 函数          | 描述                         |
| ------------- | ---------------------------- |
| AddDate()     | 增加一个日期（天、周等）     |
| AddTime()     | 增加一个时间（时、分等）     |
| CurDate()     | 返回当前日期                 |
| CurTime()     | 返回当前时间                 |
| Now()         | 返回当前日期和时间           |
| Date()        | 返回日期部分                 |
| Time()        | 返回时间部分                 |
| DateDiff()    | 计算俩个日期之差             |
| Date_Add()    | 高度灵活的日期运算函数       |
| Date_Format() | 返回一个格式化的日期或时间串 |
| Day()         | 返回一个日期的天数部分       |
| DayOfWeek()   | 返回日期对应的星期几         |
| Second()      | 返回秒部分                   |
| Hour()        | 返回小时部分                 |
| Minute()      | 返回分钟部分                 |
| Month()       | 返回月份部分                 |
| Year()        | 返回年份部分                 |

*PS：*MySQL的日期格式，无论是指定日期还是插入或更新值抑或是用 WHERE 子句进行过滤，日期必须为格式 yyyy-mm-dd。

```sql
SELECT column_one,column_two FROM table_name WHERE date_column = '2020-10-09';
```



当仅需要对日期部分进行查询时可以使用 Date() 函数：

```sql
SELECT column_one,column_two FROM table_name WHERE Date(date_column) = '2020-10-09';

SELECT column_one,column_two FROM table_name WHERE Date(date_column) BETWEEN '2020-10-01' AND '2020-10-10';

SELECT column_one,column_two FROM table_name WHERE Year(date_column) = 2010 AND Month(column_date) = 9;
```

> 如果要的是日期，请尽量使用Date()

#### 7.3 常用数值处理函数

| 函数   | 描述             |
| ------ | ---------------- |
| Mod()  | 返回除操作的余数 |
| Abs()  | 取绝对值         |
| Rand() | 随机数           |
| Exp()  | 取指数值         |
| Sqrt() | 取平方根         |
| Pi()   | 圆周率           |
| Cos()  | 取余弦           |
| Sin()  | 取正弦           |
| Tan()  | 取正切           |

***

### 8. 汇总数据

#### 8.1 聚集函数

运行在行组上，计算和返回单个值的函数。

*SQL聚集函数*

| 函数    | 描述         |
| ------- | ------------ |
| AVG()   | 平均值       |
| COUNT() | 某列的行数   |
| MAX()   | 某列的最大值 |
| MIN()   | 某列的最小值 |
| SUM()   | 某列之和     |

示例：

```sql
SELECT AVG(column_one) AS avg_column FROM table_name WHERE column_two = 10086;
SELECT COUNT(*) AS num_column FROM table_name;
SELECT COUNT(column_one) AS num_column FROM table_name;
SELECT MAX(column_one) AS max_column FROM table_name;
SELECT MIN(column_one) AS min_column FROM table_name;
SELECT SUM(column_one) AS sum_column FROM table_name WHERE column_two = 10086;
SELECT SUM(column_one*column_two) AS total_column FROM table_name WHERE column_three = 10010;
SELECT AVG(DISTINCT column_one) AS avg_column FROM table_name WHERE column_two = 10086;
```



*组合聚集函数：*

```sql
SELECT COUNT(*) AS num_column,
	   MIN(column_one) AS min_column,
	   MAX(column_one) AS max_column,
	   AVG(column_one) AS avg_column,
FROM table_name;	   
```

> 1. AVG()函数只能用来确定特定数值列的平均值，而且列名必须作为函数参数给出。
>
>    AVG()函数会忽略列值为NULL的行。
>
> 2. COUNT()函数的俩种使用方式：
>
>    + 使用 COUNT(*) 对表中行的数目进行计数，不管表列中包含的是空值（NULL）还是非空值；
>    + 使用 COUNT(column_name) 对特定列中具有值的行进行计数，忽略NULL值；
>
> 3. MAX()用于文本数据时，将返回最后一行；MIN() 用于文本数据时，将返回最前面一行；
>
> 4. 对所有行执行计算，指定ALL参数或者不给参数；只包含不同的值（去重），指定DISTINCT参数；
>
> 5. 指定别名以包含某个聚集函数的结果时，不应该使用表中实际的列名。

***

### 9. 分组数据

#### 9.1 创建分组

GROUP BY子句分组数据后，聚集函数将会对每个分组而不是整个结果集进行聚集；

```sql
SELECT column_one,COUNT(*) AS num_column FROM table_name GROUP BY column_one;
```



> 1. GROUP BY 子句可以包含任意数目的列。
> 2. 若为 GROUP BY 子句中嵌套了分组，数据将在最后规定的分组上进行汇总。
> 3. GROUP BY 子句列出的每个列都必须是检索列或有效的表达式（但不能是聚集函数）。
> 4. 除聚集计算语句外，SELECT 语句中的每个列都必须在 GROUP BY 子句中给出。
> 5. 如果分组列中具有 NULL 值，则 NULL 将作为一个分组返回。
> 6. GROUP BY子句必须出现在WHERE子句之后，ORDER BY子句之前。

#### 9.2 过滤分组

MySQL允许过滤分组，规定包括哪些分组，排除哪些分组。

*HAVING与HERE的区别是：*

WHERE过滤行，HAVING过滤分组，且WHERE在分组前进行过滤，HAVINg在数据分组后进行过滤，但HAVING支持所有的WHERE操作符。

```sql
//示例1
SELECT column_one,COUNT(*) AS num_column
FROM table_name
GROUP BY column_one
HAVING COUNT(*) >= 2;

//示例2
SELECT column_one,COUNT(*) AS num_column
FROM table_name
WHERE column_two >= 10
GROUP BY column_one
HAVING COUNT(*) >= 2;
```

#### 9.3 分组和排序

| ORDER BY                                   | GROUP BY                                                   |
| ------------------------------------------ | ---------------------------------------------------------- |
| 排序产生的输出                             | 分组行。但输出可能不是分组的顺序。                         |
| 任意列都可以使用（甚至非选择的也可以使用） | 只可能使用选择列或表达式列，而且必须使用每个选择列表达式。 |
| 不一定需要                                 | 如果与聚集函数一起使用列（或表达式），则必须使用。         |

```sql
//示例1
SELECT column_one,SUM(column_one*column_two) AS sum_column
FROM table_name
GROUP BY column_one
HAVING SUM((column_one*column_two) >= 50
ORDER BY sum_column;
```

> 一般在使用GROUP BY子句时，应该给出ORDER BY子句，这是保证数据正确排序的唯一方法。尽管有时候GROUP BY后的数据可能会符合你的排序方式，但不要依赖。

#### 9.4 SELECT子句及其顺序

| 子句     | 描述                 | 是否必须使用           |
| -------- | -------------------- | ---------------------- |
| SELECT   | 要返回的列或者表达式 | 是                     |
| FROM     | 从中检索数据的表     | 仅在从表选择数据时使用 |
| WHERE    | 行级过滤             | 否                     |
| GROUP BY | 分组说明             | 仅在按组计算聚集时使用 |
| HAVING   | 组级过滤             | 否                     |
| ORDER BY | 输出排序顺序         | 否                     |
| LIMIT    | 要检索的行数         | 否                     |

***

### 10. 子查询

**子查询：**嵌套在其他查询中的查询。

#### 10.1 利用子查询进行过滤

可以将一条SELECT语句返回的结果用于另一条SELECT语句的WHERE子句；

子查询总是从内向外处理，处理外内部的查询后再处理外部查询。

```sql
//示例1
SELECT column_one
FROM table_one 
WHERE column_two IN (SELECT column_two
				    FROM table_two
				    WHERE column_three = 10086);
				    
//示例2
SELECT column_one,column_two
FROM table_one
WHERE column_one IN (SELECT column_one 
				   FROM table_two 
				   WHERE column_three IN (SELECT column_thre 
				   						FROM table_three
				   						WHERE column_four = 10086;)
```

*格式化SQL：*

含有子查询的SQL语句一般较长，加上一些条件后显得逻辑复杂，此时采用适当的缩进对SQL语句进行格式化可以大大简化对SQL理解。

**列必须匹配：**

在WHERE子句中使用子查询时，应该保证SELECT语句（子查询）具有与WHERE子句中相同的列。

#### 10.2 计算字段使用子查询

```sql
//示例1
SELECT COUNT(*) AS num_column
FROM table_name
WHERE column_one = 10086;

//示例2
SELECT column_one,
	   column_two,
	   (SELECT COUNT(*)
	   FROM table_two
	   WHERE table_two.column_three = table_one.column_three) AS num_column
FROM table_one
ORDER BY column_one;
```

**相关子查询：**涉及外部查询的子查询。任何时候只要列名可能具有多义性，就必须使用*完全限定名*的写法，避免歧义。

> 逐渐增加子查询来建立查询：使用子查询时，应该优先建立和测试最内层的查询，然后使用硬编码数据建立和测试外层查询，确认无误后再嵌入子查询。

***

### 11. 联结表

SQL最强大的功能就是能在数据检索查询的执行中联结（join）表。

***外键（foreign key）：外键为某个表中一列，它包含另一个表的主键值，定义了俩个表之间的关系。***

+ 外键的存在可以让关系数据有效的存储，并且方便管理。因此关系数据库的可伸缩性远比非关系数据库要好。

***可伸缩性（scale）：能够适应不断增加的工作量而不失败。设计良好的数据库或应用程序称之为可伸缩性好（scale well）。***

**为什么要使用联结：**

+ 联结是一种机制，用来在一条 SELECT语句中关联表，因此称之为联结。使特殊的语法，可以联结多个表返回一组输出，联结在运行时关联表中正确的行。

**维护引用完整性：**

+ 联结并不是物理实体，它在实际的数据库表中不存在。联结是由MySQL根据需要建立的，它存在于查询的执行当中。
+ 联结失败情况的发生，仅允许在关系列中插入合法的数据，这就是维护引用完整性，它是通过在表的定义中指定主键和外键来实现的。

#### 11.1 创建联结

```SQL
SELECT column_one, column_two, column_three
FROM table_one, table_two
WHERE table_one.column_four = table_two.column_four
ORDER BY column_one,column_two;
```

***完全限定列名：在引用的列可能出现二义性时，必须使用完全限定列名（用一个点分隔的表名和列名）。如果引用一个没有用表名限制的具有二义性的列名，MySQL将返回错误。***

**笛卡尔积（cartesian product）：**

+ 由没有联结条件的表关系返回的结果为笛卡尔积。检索出的行数目将是第一个表中的行数乘以第二个表中的行数。
+ 叉联结（cross join），笛卡尔积的联结类型。

***PS：应该尽量保证所有联结都有 WHERE 子句，且需要保证 WHERE 子句的正确性， 否则 MySQL 将返回比想要的数据多得多的数据。***

#### 11.2 内部联结

**等值联结（equijoin）：**基于俩个表之间的相等测试。也称为内部联结。

```sql
SELECT column_one, column_two, column_three
FROM table_one INNER JOIN table_two
ON table_one.column_four = table_two.column_four;
```

SQL 对一条 SELECT 语句中可以联结的表的数目没有限制；

```SQL
SELECT column_one, column_two, column_three
FROM table_one, table_two, table_three
WHERE table_one.column_four = table_two.column_four
  AND table_two.column_five = table_three.column_five
  AND table_three.column_six = 10086; 
```

***PS：MySQL 在处理联结表时，是非常耗费资源的，因此不要联结不必要的表。联结的表越多，性能下降的越厉害。***

复杂的子查询可以使用联结的方式：

```SQL
//子查询
SELECT column_one,column_two
FROM table_one
WHERE column_one IN (SELECT column_one 
				   FROM table_two 
				   WHERE column_three IN (SELECT column_thre 
				   						FROM table_three
				   						WHERE column_four = 10086;);

//联结
SELECT column_one, column_two
FROM table_one, table_two, table_three
WHERE table_one.column_three = table_tow.column_three
  AND table_two.column_four = table_three.column_four
  AND column_five = 10086;
```

***

### 12. 创建高级联结

#### 12.1 使用表别名

+ 缩短SQL语句；
+ 允许在单条 SELECT 语句中多次使用相同的表；

```sql
//示例1
SELECT Concat(RTrim(column_one), '(',RTrim(column_two),')') AS
column_three
FROM table_one
ORDER BY column_one;

//示例2
SELECT column_one, column_two
FROM table_one AS a, table_two AS b, table_three AS c
WHERE a.column_three  = b.column_three
  AND b.column_four = c.column_four
  AND column_five = 10086;
```

#### 12.2 使用不同类型的联结

##### 12.2.1 自联结

```sql
SELECT p1.prod_id , p1.prod_name
FROM products AS p1, products AS p2
WHERE p1.vend_id = p2.vend_id
  AND p2.prod_id = "DINTR";
```

自联结通常作为外部语句用来替代从相同表中检索数据时使用的子查询语句。

有时候处理联结远比处理子查询快得多，所以应该尽量多试几种方法，以确定哪一种的性能更好。

##### 12.2.2 自然联结

无论何时对表进行联结，应该至少有一个列出现在不止一个表中（被联结的列）。

自然联结排除多次出现，使每个列只返回一次。

```sql
SELECT c.*, o.order_num, o.order_date
	   oi.prod_id, oi.quantity, OI.item_price
FROM customer AS c, orders AS o, orderitems AS oi
WHERE c.cust_id = o.cust_id
	AND oi.order_num = o.order_num
	AND prod_id = 'FB';
```

##### 12.2.3 外部联结

联结中包含了那些在相关表中没有关联行的行。

```sql
//左联结
SELECT customers.cust_id, orders.order_num
FROM customers LEFT OUTER JOIN orders
ON customers.cust_id = orders.cust_id;

//右联结
SELECT customers.cust_id, orders.order_num
FROM customers RIGHT OUTER JOIN orders
ON orders.cust_id = customers.cust_id;
```

在使用 OUT JOIN 语法时，必须使用 RIGHT 或 LEFT 关键字指定包括其所用行的表（RIGHT 指出的是 OUTER JOIN 右边的表，而 LEFT 指出的是 OUTER JOIN 左边的表）。

**没有*=操作符：**

+ MySQL不支持简化字符 \*= 和 =\* 的使用，这俩种操作符在其他 DBMS 中很流行；

**外部联结的类型：**

+ 左外部联结和右外部联结的唯一区别是所关联的表的顺序不同；
+ 左外部联结可以通过颠倒 FROM 或 WHERE 子句中表的顺序转换为右外部联结。

##### 12.2.4 使用带聚集函数的联结

示例：

```sql
SELECT customers.cust_name,
	customers.cust_id,
	COUNT(orders.order_num) AS num_ord
FROM customers INNER JOIN orders
ON customers.cust_id = orders.cust_id
GROUP BY customers.cust_id;

SELECT customers.cust_name
	  customers.cust_id,
	  COUNT(orders.order_num) AS num_ord
FROM customers LEFT OUTER JOIN orders
ON customers.cust_id = orders.cust_id
GROUP BY customers.cust_id;
```

#### 12.3 使用联结和联结条件

联结及其使用的一些要点：

+ 注意所使用的联结类型。一般使用内部联结，但使用外部联结也是有效的；
+ 应保证使用正确的联结条件，否则将返回不正确的数据；
+ 应该总是提供联结条件，否则会得出笛卡尔积；
+ 在一个联结中可以包含多个表，甚至对于每个联结都可以采用不同的联结类型。

***

### 13. 组合查询

#### 13.1 组合查询

MySQL 允许执行多个查询（多条 SELECT 语句），并将结果作为单个查询结果返回。

这些组合查询通常称为并（union）或复合查询（compound query）。

常见使用情景：

+ 在单个查询中从不同的表返回类似结构的数据；
+ 对单个表执行多个查询，按单个查询返回数据；

#### 13.2 UNION

```sql
SELECT vend_id, prod_id, prod_price
FORM products
WHERE prod_price <= 5
UNION 
SELECT vend_id, prod_id, prod_price
FROM products 
WHERE vend_id IN (1001,1002);

//对应的多条 WHERE 子句
SELECT vend_id, prod_id, prod_price
FROM products
WHERE prod_price <= 5
OR vend_id IN (1001,1002);
```

使用 UNION 可能比使用 WHERE 子句更为复杂。但对于更复杂的过滤条件，或者从多个表（而不是单个表）中检索数据的情形，使用 UNION 可能会使处理更简单。

**UNION规则**

+ UNION 必须由俩条或俩条以上的 SELECT 语句组成，语句之间用关键字 UNION 分隔；
+ UNION 中每个查询必须包含相同的列、表达式或聚集函数；
+ 列数据类型必须兼容：类型不必完全相同，但必须是DBMS可以隐含地转换的类型。

**包含或取消重复的行**

UNION会从查询结果集中自动去除重复的行。

如果想返回所有匹配行，可使用 UNION ALL 而不是 UNION；

```sql
SELECT vend_id, prod_id, prod_price
FROM products
WHERE prod_price <= 5
UNION ALL
SELECT vend_id, prod_id, prod_price
FDROM products
WHERE vend_id IN (1001,1002);
```

**对组合结果排序：**

在用 UNION 组合查询时，只能使用一条 ORDER BY 子句，它必须出现在最后一条 SELECT 语句之后；

```sql
SELECT vend_id, prod_id, prod_price
FROM products
WHERE prod_price <= 5
UNION
SELECT vend_id, prod_id, prod_price
FDROM products
WHERE vend_id IN (1001,1002)
ORDER BY vend_id, prod_price;
```

### 14. 全文本搜索

#### 14.1 全文本搜索简介

**并非所有引擎都支持全文本搜索：**MySQL中最常用的引擎为 MyISAM 和 InnoDB，前者支持全文本搜索，后者不支持。

使用 LIKE 或正则表达式这些搜索机制确实非常有用，但存在几个重要的限制：

+ 性能。通配符和正则表达式匹配通常要求MySQL尝试匹配所有行（而且这些搜索极少使用表索引）。因此，由于被搜索行数不断增加，这些搜索可能非常耗时。
+ 明确控制。使用通配符和正则表达式匹配，很难明确控制匹配什么和不匹配什么。
+ 智能化的结果。虽然基于通配符和正则表达式的搜索提供了非常灵活的搜索，但它们都不能提供一种智能化的选择结果的方法。

**全文本搜索：**使用全文本搜索时，MySQL不需要分别查看每个行，不需要分别分析和处理每个词。MySQL创建指定列中各词的一个索引，搜索可以针对这些词进行。

#### 14.2 使用全文本搜索

为了进行全文本搜索，必须索引被搜索的列，而且要随着数据的改变不断地重新索引。

##### 14.2.1 启用全文本搜索支持

CREATE TABLE 语句接受 FULLTEXT 子句， 它给出被索引的一个逗号分隔的列表。

```sql
CREATE TABLE productnotes
(
	note_id 	int		  NOT NULL AUTO_INCREMENT,
    prod_id 	char(10)  NOT NULL,
    note_date   datetime  NOT NULL,
    note_text   text 	  NULL,
    PRIMARY KEY(note_id)
    FULLTEXT(note_text)
) ENGINE=MyISAM;
```

定义索引后，MySQL会自动维护该索引。在增加、更新或删除行时，索引随之自动更新。

**不要在导入数据时使用FULLTEXT：**

+ 更新索引需要花费时间。如果正在导入数据到一个新表，此时不应该启用 FULLTEXT 索引。应该首先导入所有数据，然后再修改表，定义FULLTEXT。

##### 14.2.2 进行全文本搜索

建立索引之后可以使用 Match() 和 Against() 执行全文本搜索，其中 Match() 指定被搜索的列，Against() 指定要使用的搜索表达式。

```sql
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('rabbit');
```

**PS：**

+ 使用完整的 Match()说明。传递给 Match() 的值必须与 FULLTEXT() 中定义的相同。如果指定多个列，则必须列出它们（而且次序正确）。
+ 搜索不区分大小写。除非使用 BINARY 方式，否则全文搜索不区分大小写。

全文本搜索返回结果依据：

+ Match() 和 Against() 用来建立一个计算列（别名rank），此列包含全文本搜索计算出的等级值。
+ 文本中靠前的行的等级值比词靠后的行的等级值高。
+ 如果指定多个搜索项，则包含多数匹配词的那些行将具有比包含较少词（或仅有一个匹配）的那些行高的等级值。

```sql
SELECT note_text.
	   Match(note_text) Against('rabbit') AS rank
FROM productnotes;
```

##### 14.2.3 使用查询扩展

使用查询扩展时，MySQL对数据和索引进行俩遍扫描来完成搜索：

+ 首先，进行一个基本的全文本搜索，找出与搜索条件匹配的所有行；
+ 其次，MySQL检查这些匹配行并选择所有有用的词；
+ 再其次，MySQL再次进行全文本搜索，这次不仅使用原来的条件，而且还使用所有有用的词。

```sql
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('anvils' WITH QUERY EXPANSION);
```

##### 14.2.4 布尔文本搜索

该搜索可以提供如下内容的细节：

+ 要匹配的词；
+ 要排斥的词（即使该行包含匹配词也不返回）；
+ 排列提示（指定某些词比其他词更重要，更重要的词等级更高）；
+ 表达式分组；
+ ...

***布尔方式不同于全文本搜索语法的地方在于，即使没有 FULLTEXT 索引，也可以使用它。但这是一种非常缓慢的操作（其性能随着数据量的增加而降低）。***

```sql
//示例
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('heavy' IN BOOLEAN MODE);

//搜索排除包含rope*的行
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('heavy -rope*' IN BOOLEAN MODE);

//搜索匹配包含词 rabbit 和 bait 的行
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('+rabbit +bait' IN BOOLEAN MODE);

//无指定操作符 搜索匹配包含 rabbit 和 bait 中至少一个词的行
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('rabbit bait' IN BOOLEAN MODE);

//匹配短语 "rabbit bait" 而不是匹配俩个词 rabbit 和 bait
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('"rabbit bait"' IN BOOLEAN MODE);

//匹配 rabbit 和 carrot， 增加前者的等级，降低后者的等级
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('>rabbit <bait' IN BOOLEAN MODE);

//搜索匹配词 safe 和 combination, 降低后者的等级
SELECT note_text
FROM productnotes
WHERE Match(note_text) Against('+safe +(<combination)' IN BOOLEAN MODE);
```

**全文本布尔操作符：**

| 布尔操作符 | 说明                                                         |
| ---------- | ------------------------------------------------------------ |
| +          | 包含，词必须存在                                             |
| -          | 排除，词必须不出现                                           |
| >          | 包含，而且增加等级值                                         |
| <          | 包含，而且减少等级值                                         |
| ()         | 把词组成子表达式（允许这些子表达式作为一个组被包含、排除、排列等） |
| ~          | 取消一个词的排序值                                           |
| *          | 词尾的通配符                                                 |
| ""         | 定义一个短语（与单个词的列表不一样，它匹配整个短语以便包含或排除这个短语） |

##### 14.2.5 全文本搜索使用说明

+ 在索引全文本数据时，短词被忽略且从索引中排除。短词定义为那些具有3个或3个以下字符的词（可更改）。
+ MySQL带有一个内建的非用词（stopword）列表，这些词在索引全文本数据时总是被忽略（可覆盖）。
+ 许多词的出现频率很高，对其进行索引无意义，因此MySQL规定一条 50% 规则， 如果一个词出现在 50% 以上的行中，则将其作为一个非用词忽略。50% 规则不用于 IN BOOLEAN MODE。
+ 如果表中行数少于3行，全文本搜索不返回结果（因为每个词或者不出现，或者至少出现在50%的行中）。
+ 忽略词中的单引号。 
+ 不具有词分隔符（包括日语和汉语）的语言不能恰当地返回全文本搜索结果。
+ 仅在MyISAM数据库引擎中支持全文本搜索。

***

### 15. 插入数据

#### 15.1 数据插入

1.各个列必须以它们在表中定义出现的次序填充：

```sql
INSERT INTO Customers
VALUES(NULL,
	'Pep E.LaPew',
	'100 Main Street',
	'Los Angeles',
	'CA',
	'90046',
	'USA',
	NULL,
	NULL);
```

2.明确指定列名：

```sql
INSERT INTO Customers(cust_name
	cust_contact,
	cust_email,
	cust_attress,
	cust_city,
	cust_state,
	cust_zip,
	cust_country)
VALUES(NULL,
	'Pep E.LaPew',
	'100 Main Street',
	'Los Angeles',
	'CA',
	'90046',
	'USA',
	NULL,
	NULL);
```

可以省略列的条件：

+ 该列定义为允许 NULL 值（无值或空值）。
+ 在表定义中给出默认值，这表示如果不给出值，将使用默认值。

**提升整体性能：**

+ INSERT 操作可能很耗时（特别是有很多索引需要更新时），而且它可能降低等待处理的 SELECT 语句的性能。
+ 如果数据检索时最重要的，则可以在 INSERT 和 INTO 之间添加关键字 LOW_PRIORITY ，指示MySQL降低 INSERT 语句的优先级。（同样适用于UPDATE 和 DELETE 语句）。

```sql
INSERT LOW_PRIORITY INTO
```

#### 15.2 插入多行

```sql
//方式1
INSERT INTO Customers(cust_name
	cust_address,
	cust_city,
	cust_state,
	cust_zip,
	cust_country)
VALUES('Pep E.LaPew',
	'100 Main Street',
	'Los Angeles',
	'CA',
	'90046',
	'USA');
INSERT INTO Customers(cust_name
	cust_address,
	cust_city,
	cust_state,
	cust_zip,
	cust_country)
VALUES('M. Martian',
	'42 Galaxy Way',
	'New York',
	'NY',
	'11213',
	'USA');

//方式2 建议使用 单挑INSERT语句处理多个插入比使用多条INSERT语句快
INSERT INTO Customers(cust_name
	cust_address,
	cust_city,
	cust_state,
	cust_zip,
	cust_country)
VALUES(
	'Pep E.LaPew',
	'100 Main Street',
	'Los Angeles',
	'CA',
	'90046',
	'USA'),
	(
	'M. Martian',
	'42 Galaxy Way',
	'New York',
	'NY',
	'11213',
	'USA');
```

#### 15.3 插入检索的数据

```sql
INSERT INTO Customers(cust_name
	cust_contact,
	cust_email,
	cust_address,
	cust_city,
	cust_state,
	cust_zip,
	cust_country)
SELECT cust_id,
	cust_contact,
	cust_email,
	cust_name,
	cust_address,
	cust_city,
	cust_state,
	cust_zip,
	cust_country
FROM custnew;
```

***

### 16. 更新和删除数据

#### 16.1 更新数据

+ 更新表中特定行；
+ 更新表中所有行；

```sql
UPDATE customers
SET cust_name = 'The Fudds',
	cust_email = 'elmer@fudd.com',
WHERE cust_id = 10005;
```

> + 在UPDATE语句中使用子查询；UPDATE语句中可以使用子查询，使得能用SELECT语句检索出的数据更新列数据。
>
> + INGBORE关键字；使用UPDATE语句更新多行时，使用 IGNORE 关键字时，即使发生错误，也继续进行更新。
>
>   ```
>   UPDATE IGNORE customers...
>   ```
>
> + 为了删除某个列的值，可以将其设置为 NULL
>
>   ```sql
>   UPDATE customers
>   SET cust_email = NULL
>   WHERE cust_id = 10005;
>   ```

#### 16.2 删除数据

+ 从表中删除特定行；
+ 从表中删除所有行；

**不要省略WHERE子句：**在使用DELETE时一定要使用WHERE子句，否则就会错误删除表中所有行（除非你想要的这样做）。

```sql
DELETE FROM customers
WHERE cust_id = 10006;
```

>+ 删除表的内容而不是表：DELETE语句从表中删除行，甚至是删除表中所有行，但是，DELETE不删除本身。
>
>+ 更快的删除：如果想从表中删除所有行，不要使用DELETE。可以使用 TRUNCATE TABLE语句，其完成相同的工作，但速度更快。
>
>  TRUNCATE 实际上是删除原来的表并重新创建一个表，而不是逐行删除表中的数据。

#### 16.3 更新和删除的指导原则

1. 除非确实打算更新和删除每一行，否则绝对不要使用不带WHERE子句的UPDATE或DELETE语句；
2. 保证每个表都有主键，尽可能像WHERE子句那样使用它（可以指定各个主键、多个值或值得范围）；
3. 在对UPDATE或DELETE语句使用WHERE子句前，应该先用SELECT进行测试，保证过滤的是正确的记录，以防编写的WHERE子句不正确；
4. 使用强制实施引用完整性的数据库，这样MySQL将不允许删除具有与其他表相关联的数据的行。

### 17. 创建和操纵表

#### 17.1 创建表

##### 17.1.1 表创建基础

使用CREATE TABLE创建表，必须给出下列信息：

+ 新表的名字，在关键字CREATE TABLE之后给出；
+ 表列的名字和定义，用逗号分隔；

```sql
CREATE TABLE customers IF NOT EXISTS
(
	cust_id 		int 		NOT NULL  AUTO_INCREMENT,
	cust_name   	char(50)	NOT NULL ,
	cust_address	char(50) 	NULL,
	cust_city 		char(50)	NULL,
	cust_state		char(50)	NULL,
	cust_zip		char(50)	NULL,
	cust_country	char(50)	NULL,
	cust_contact	char(50)	NULL,
	cust_email		char(50)	NULL,
	PRIMARY KEY (cust_id)
) ENGINE = InnoDB;
```

##### 17.1.2 使用NULL值

+ NULL值就是没有值或是缺值。

+ 允许NULL值的列也允许在插入行时不给出该列的值。

+ 不允许NULL值的列不接受该列没有值的行，换句话说，在插入或更新行时，该列必须有值。
+ NULL为默认值，若不指定 NOT NULL，则认为指定的是NULL；

```sql
CREATE TABLE orders
(
	order_num 		int 		NOT NULL AUTO_INCREMENT,
	order_date		datetime 	NOT NULL,
	cust_id			int			NOT NULL,
	PRIMARY KEY (order_num)
) ENGINE = InnoDB;

CREATE TABLE vendors
(
	vend_id 		int 		NOT NULL AUTO_INCREMENT,
	vend_name		char(50)	NOT NULL,
	vend_address	char(50)	NULL,
	vend_city		char(50)	NULL,
	vend_state		char(5)		NULL,
	vend_zip		char(50)	NULL,
	vend_country	char(50)	NULL,
	PRIMARY KEY (vend_id)
) ENGINE = InnoDB;
```

##### 17.1.3 主键再介绍

主键值必须唯一，即表中的每个行必须具有唯一的主键值。

+ 如果主键使用单个列，则它的值必须唯一。
+ 如果使用多个列，则这些列的组合值必须唯一。
+ 主键只能使用不允许 NULL 值的列。允许 NULL 值的列不能作为唯一标识。

```sql
PRIMARY KEY (vend_id)

CREATE TABLE orderitems
(
	order_num		int 			NOT NULL,
	order_item		int 			NOT NULL,
	prod_id			char(10) 		NOT NULL,
	quantity		int 			NOT NULL,
	item_price		decimal(8,2)	NOT NULL,
	PRIMARY KEY (order_num, order_item)	
) ENGINE = InnoDB;
```

##### 17.1.4 使用AUTO_INCREMENT

AUTO_INCREMENT 告诉MySQL，本列每当增加一行时自动增量。

每次执行一个 INSERT 操作时，MySQL 自动对该列增量（从而才有这个关键字 AUTO_INCREMEN），给该列赋予下一个可用的值。

这样给每个行分配一个唯一的 cust_id，从而可以用作主键值。



+ 每个表只允许一个 AUTO_INCREMENT 列，而且它必须被索引。
+ 覆盖 AUTO_INCREMENT ：可以简单的在 INSERT 语句中指定一个值，只要它是唯一的即可，该值将被用来替代自动生成的值。后续的增量将开始使用该手工插入的值。
+ 获取最后一个 AUTO_INCREMENT 值：SELECT last_insert_id()。

##### 17.1.5 指定默认值

默认值用 CREATE TABLE 语句的列定义中的 DEFAULT 关键字指定：

```sql
CREATE TABLE orderitems
(
	order_num		int 			NOT NULL,
	order_item		int 			NOT NULL,
	prod_id			char(10) 		NOT NULL,
	quantity		int 			NOT NULL DEFAULT 1,
	item_price		decimal(8,2)	NOT NULL,
	PRIMARY KEY (order_num, order_item)
) ENGINE = InnoDB;
```

**PS：**

+ 不允许函数，与大多数 DBMS 不一样，MySQL不允许使用函数作为默认值，它只支持常量。
+ 尽量使用默认值而不是 NULL 值，特别是对于计算或数据分组的列更是如此。

##### 17.1.6 引擎类型

+ InnoDB：可靠的事务处理引擎，但不支持全文本引擎；
+ MEMORY：在功能上等同于 MyISAM，但由于数据存储在内存（而不是磁盘）中，速度很快（特别适合于临时表）；
+ MyISAM：性能极高的引擎，支持全文搜索，但不支持事务处理；

***外键不能跨引擎：***混用引擎类型有一个大缺陷，即外键不能跨引擎，即使用一个引擎的表不能引用具有使用不同引擎的表的外键。

#### 17.2 更新表

ALTER TABLE 语句用于更改表结构，使用该语句必须给出下面信息：

+ 在 ALTER TABLE 之后给出要更改的表名（该表必须存在，否则将出错）；
+ 所做更改的列表；

```sql
-- 添加列
ALTER TABLE vendors
ADD vend_phone CHAR(20);

-- 删除列
ALTER TABLE vendors
DROP COLUMN vend_phone;

-- 定义外键
ALTER TABLE orderitems
ADD CONSTRAINT fk_orderitems_orders FOREIGN KEY (order_num) 
REFERENCES orders (order_num);

ALTER TABLE orderitems
ADD CONSTRAINT fk_orderitems_products FOREIGN KEY (prod_id) 
REFERENCES products (prod_id);

ALTER TABLE orderitems
ADD CONSTRAINT fk_orderitems_customers FOREIGN KEY (cust_id) 
REFERENCES customers (cust_id);

ALTER TABLE orderitems
ADD CONSTRAINT fk_orderitems_vendors FOREIGN KEY (vend_id) 
REFERENCES vendors (vend_id);
```

复杂的表结构更改一般需要手动删除过程：

1. 用新的列布局创建一个新表；
2. 使用 INSERT SELECT 语句从旧表复制数据到新表。如果有必要，可使用转换函数和计算字段；
3. 检验包含所需数据的新表；
4. 重命名旧表（如果确定，可以删除它）；
5. 用旧表原来的名字重命名新表；
6. 根据需要，重新创建触发器、存储过程。索引和外键。

#### 17.3 删除表

```sql
DROP TABLE customers2;
```

#### 17.4 重命名表

```sql
RENAME TABLE customers2 TO customers;

RENAME TABLE backup_customers TO customers,
			 backup_vendors TO vendors,
			 backup_products TO products;
```

***

### 18.使用视图

#### 18.1 视图

> + 视图是虚拟的表，与包含数据的表不一样，视图只包含使用时动态检索数据的查询。
> + 视图可以重用SQL语句。
> + 简化复杂的SQL操作。
> + 使用表的组成部分，而不是整个表。
> + 保护数据。可以给用户授予表的特定部分的访问权限，而不是整个表的访问权限。
> + 更改数据格式和表示。视图可返回与底层表的表示和格式不同的数据。

**PS：**

1. 视图仅仅是用来查看存储在别处的数据的一种设施。视图本身不包含数据，因此它们返回的数据时从其他表中检索出来的。在添加或更改这些表中的数据时，视图将返回改变过的数据。

2. 因为视图不包含数据，所以每次使用视图时，都必须处理查询执行时所需的任一个检索。如果使用多个联结和过滤创建了复杂的视图或者嵌套了视图，性能可能会发生大幅下降。



**视图的规则和限制：**

+ 与表一样，视图必须唯一命名。
+ 对于可以创建的视图数目没有限制。
+ 为了创建视图，必须具有足够的访问权限。
+ 视图可以嵌套，即可以利用从其他视图中检索的数据的查询来构造一个视图。
+ ORDER BY 可以用在视图中，但如果从该视图检索数据 SELECT 中也含有 ORDER BY ，那么该视图中的 ORDER BY 将被覆盖。
+ 视图不能索引，也不能有关联的触发器或默认值。
+ 视图可以和表以期使用。

#### 18.2 使用视图

+ 视图用 CREATE VIEW 语句来创建。
+ 使用 SHOW CREATE VIEW viewname; 来查看创建视图的语句。
+ 用 DROP 删除视图，其语法为 DROP VIEW viewname;。
+ 更新视图时，可以先用 DROP 再用 CREATE ，也可以直接用 CREATE OR REPLACE VIEW 。

##### 18.2.1 利用视图简化复杂的联结

视图最常见的应用之一就是隐藏复杂的SQL。

```sql
//创建视图
CREATE VIEW productcustomers AS
SELECT cust_name,cust_contact,prod_id
FROM customers,orders,orderitems
WHERE customers.cust_id = orders.cust_id
	AND orderitems.order_num = orders.order_num;
	
//使用视图
SELECT cust_name,cust_contact
FROM productcustomers
WHERE prod_id = 'TNT2';
```

##### 18.2.2 用视图重新格式化检索出的数据

```sql
CREATE VIEW vendorlocation AS
SELECT Concat(Rtrim(vend_name),'(',RTrim(vend_country),')')
		AS vend_title
FROM vendors
ORDER BY vend_name;
```

##### 18.2.3 使用视图过滤不想要的数据

```sql
CREATE VIEW customeremaillist AS
SELECT cust_id,cust_name,cust_email
FROM customers
WHERE cust_email IS NOT NULL;
```

如果从视图检索数据时使用了一条 WHERE 子句，再传递给视图一条 WHERE 子句时，则俩组子句将自动组合。

##### 18.2.4 使用视图和计算字段

```sql
CREATE VIEW orderitemsexpanded AS
SELECT order_num,prod_id,quantity,item_price,quantity*item_price AS expanded_price
FROM orderitems;
```

##### 18.2.5 更新视图

视图是可更新的(INSERT、UPDATE、DELETE)，更新一个视图将更新其基表。如果对视图增加或删除行，实际上是对其基表增加或删除行。

并非所有视图都可以更新，如果视图中定义有一下操作，则不能进行视图的更新：

+ 分组(使用 GROUP BY 和 HAVING)；
+ 联结；
+ 子查询；
+ 并；
+ 聚集函数(MIN()、Count()、Sum()等)；
+ DISTINCT；
+ 到处(计算)列；

**一般，应该将视图用于检索数据而不用于更新。**

***

### 19.使用存储过程

#### 19.1 存储过程

存储过程简单来说，就是为以后的使用而保存的一条或多条MySQL语句的集合。可将其视为批文件，虽然它们的作用不仅限于批处理。

**使用存储过程的理由：(简单、安全、高性能)**

+ 通过把处理封装在容易使用的单元中，简化复杂的操作。
+ 由于不要求反复建立一系列处理步骤，这保证了数据的完整性。
+ 简化了对变动的管理。(通过存储过程限制对基础数据的访问减少了数据讹误的机会。)
+ 提高性能。使用存储过程比使用单独的SQL语句要快。
+ 存在一些只能用在单个请求中的MySQL元素和特性，存储过程可以使用它们来编写更强更灵活的代码。

**存储过程的缺陷：**

+ 一般来说，存储过程的编写比基本SQL语句复杂，编写存储过程需要更高的技能，更丰富的经验。
+ 创建存储过程需要安全访问权限。

MySQL将编写存储过程的安全和访问与执行存储过程的安全和访问区分开来。所以你可能不能编写存储过程，却依然可以使用。

##### 19.2 使用存储过程

存储过程的执行远比其定义更经常遇到。

###### 19.2.1 执行存储过程

MySQL执行存储过程的语句为CALL。

CALL接受存储过程的名字以及需要传递给它的任意参数。

```sql
CALL productpricing(@pricelow,
					@pricehigh,
					@priceaverage);
```

##### 19.2.2 创建存储过程

```sql
-- 创建
CREATE PROCEDURE productpricing()
BEGIN
	SELECT Avg(prod_price) AS priceaverage
	FROM products;
END;

-- 调用
CALL productoricing();
```

存储过程实际上是一种函数，所以存储过程名后需要有"()"符号(即使不传递参数也需要)。

**MySQL命令行客户机分隔符：**

默认的MySQL语句分隔符为 ";"。

MySQL命令行使用程序也使用";"作为语句分隔符。

如果命令行实用程序要解释存储过程自身内的";"字符，则它们不会成为存储过程的成分，这会使存储过程中得SQL出现句法错误。

*解决办法是临时更改命令行实用程序的语句分隔符。*

```sql
DELIMITER //

CREATE PROCEDURE productpricing()
BEGIN 
	SELECT Avg(prod_price) AS priceaverage
	FROM products;
END //

DELIMITER ;
```

##### 19.2.3 删除存储过程

```sql
DROP PROCEDURE productpricing;
DROP PROCEDURE IF EXISTS productpricing;
```

##### 19.2.4 使用参数

**变量(variable)：**内存中提个特定的位置，用来临时存储数据。

```sql
CREATE PROCEDURE productpricing(
	OUT pl DECIMAL(8,2),
	OUT ph DECIMAL(8,2),
	OUT pa DECIMAL(8,2)
)
BEGIN 
	SELECT Min(prod_price)
	INTO pl
	FROM products;
	SELECT Max(prod_price)
	INTO ph
	FROM products;
	SELECT Avg(prod_price)
	INTO pa
	FROM products;
END;

-- 调用
CALL productpricing(@pricelow,
					@pricehigh,
					@priceaverage);
```

关键词OUT指出相应的参数用来从存储过程传出一个值(返回给调用者)。MySQL支持IN(传递给存储过程)、OUT(从存储过程传出)和INOUT(对存储过程传入和传出)类型的参数。

**变量名：**所有的MySQL变量必须以@开始。

```sql
SELECT @priceaverage;
SELECT @priceaverage, @pricelow, @pricehigh;

CREATE PROCEDURE ordertotal(
	IN onnumber INT,
	OUT ototal DECIMAL(8,2)
)
BEGIN 
	SELECT Sum(item_price*quantity)
	FROM orderitems
	WHERE order_num = onnumber
	INTO ototal;
END;

-- 调用
CALL ordertotal(20005, @total);
SELECT @total;
```

##### 19.2.5 检查存储过程

```sql
SHOW CREATE PROCEDURE ordertotal;
SHOW PROCEDURE STATUS;
```

**限制过程状态结果：**

SHOW PROCEDURE STATUS 列出了所有存储过程。为限制其输出，可使用LIKE指定一个过滤模式，例如：SHOW PROCEDURE STATUS LIKE 'ordertotal';

***

### 20.使用游标

#### 20.1 游标

游标(cursor)是一个存储在MySQL服务器上的数据库查询，她不是一条SELECT语句，而是被该语句检索出来的结果集。在存储了游标之后，应用程序可以根据需要滚动或浏览其中的数据。

**只能用于存储过程：**MySQL游标只能用于存储过程(和函数)。

#### 20.2 使用游标

+ 在能够使用游标之前，必须声明(定义)它。这个过程实际上没有检索数据，它只是定义要使用的SELECT语句。
+ 一旦声明后，必须打开游标以供使用。这个过程就会将前面定义的SELECT语句的数据检索出来。
+ 对于填有数据的游标，根据需要取出(检索)各行。
+ 在结束游标使用时，必须关闭游标。

#### 20.3 创建游标

DECLARE命名游标，并定义相应的SELECT语句，根据需要带WHERE和其他子句。

```sql
CREATE PROCEDURE processorders()
BEGIN 
	DECLARE ordernumbers CURSOR
	FOR
	SELECT order_num FROM orders;
END;
```

DECLARE语句用来定义和命名游标。存储过程处理完成后，游标就消失(游标局限于存储过程)。

#### 20.4 打开和关闭游标

```sql
-- 打开游标
OPEN ordernumbers;

-- 关闭游标
CLOSE ordernumbers;

CREATE PROCEDURE processorders()
BEGIN 
	--声明游标
	DECLARE ordernumbers CURSOR
	FOR
	SELECT order_num FROM orders;
	-- 打开游标
	OPEN ordernumbers;
	-- 关闭游标
	CLOSE ordernumbers;
END;
```

+ 处理OPEN语句时执行查询，存储检索出来的数据以供浏览和滚动。
+ CLOSE释放游标使用的所有内部内存和资源，因此在每个游标不在需要时都应该关闭。
+ 游标关闭后，如果没有重新打开，则不能使用它。但是使用声明过的游标不需要再次申明，用OPEN语句打开它就可以了。

#### 20.5 使用游标数据

游标被打开后，可以使用FETCH语句分别访问它的每一行。

FETCH指定检索什么数据(所需的列)，检索的数据存储在什么地方。

FETCH可以向前移动游标中的内部指针，使下一条FETCH语句检索下一行(不重复读取同一行)。

```sql
CREATE PROCEDURE processorders()
BEGIN 
	-- 声明变量
	DECLARE o INT;
	-- 声明游标
	DECLARE ordernumbers CURSOR
	FOR
	SELECT order_num FROM orders;
	-- 打开游标
	OPEN ordernumbers;
	-- 获取游标数据
	FETCH ordernumbers INTO o;
	-- 关闭游标
END;

-- 循环检索数据
CREATE PROCEDURE processorders()
BEGIN
	-- 声明变量
	DECLARE done BOOLEAN DEFAULT 0;
	DECLARE o INT;
	-- 声明游标
	DECLARE ordernumbers CURSOR
	FOR
	SELECT order_num FROM orders;
	-- 声明指针
	DECLARE CONTINUE HANDLER FOR SQLSTATE `02000` SET done=1;
	-- 打开游标
	OPEN ordernumbers;
	-- 循环结果集
	REPEAT
		-- 获取游标数据
		FETCH ordernumbers INTO o;
	-- 结束循环
	UNTIL done END REPEAT;
	--关闭游标
	CLOSE ordernumbers;
END;

CREATE PROCEDURE processorders()
BEGIN 
	-- 声明变量
	DECLARE done BOOLEAN DEFAULT 0;
	DECLARE o INT;
	DECLARE t DECIMAL(8,2);
	-- 声明游标
	DECLARE ordernumbers CURSOR
	FOR
	SELECT order_num FROM orders;
	-- 声明句柄
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done=1;
	-- 创建表存储结果
	CREATE TABLE IF NOT EXISTS ordertotals
		(order_num INT, total DECIMAL(8,2));
	-- 打开游标
	OPEN ordernumbers;
	-- 循环结果集
	REPEAT
		-- 获取游标数据
		FETCH ordernumbers INTO o;
		-- 获取总数
		CALL ordertotal(o, 1, t);
		-- 插入数据
		INSERT INTO ordertotals(order_num, total)
		VALUES(o, t);
	-- 结束循环
	UNTIL done END REPEAT;
	-- 关闭游标
	CLOSE ordernumbers;
END;
```

**DECLARE语句的次序：**DECLARE语句的发布存在特定的次序。用DECLARE语句定义的局部变量必须在定义任意游标或句柄之前定义，而句柄必须在游标之后定义。不遵守此顺序将产生错误消息。

*句柄（Handle）是一个是用来标识对象或者项目的标识符，可以用来描述窗体、文件等，值得注意的是句柄不能是常量 。*

**重复或循环：**MySQL支持循环语句，它可用来重复执行代码，直到使用LEAVE语句手动退出为止。通常REPEAT语句的语法使它更适合于对游标进行循环。

***

### 21.使用触发器

触发器是MySQL响应一下任意语句而自动执行的一条MySQL语句(或位于BEGIN和END语句之间的一组语句)；

+ DELETE；
+ INSERT；
+ UPDATE；

其他MySQL语句不支持触发器；

#### 21.1 创建触发器

+ 唯一的触发器名；
+ 触发器关联的表；
+ 触发器应该响应的活动(DELETE、INSERT、UPDATE)；
+ 触发器何时执行(处理之前或之后)；

**保持每个数据库的触发器名唯一：**

MySQL5中触发器名必须在每个表中唯一，但不是每个数据库中唯一。即可在同一个数据库的俩张表中具有相同名字的触发器，这在其他每个数据库触发器名必须唯一的DBMS中是不允许的。考虑以后MySQL的命名会更为严格，建议在数据库范围内使用唯一的触发器名。

```sql
CREATE TRIGGER newproduct AFTER INSERT ON products
FOR EACH ROW SELECT 'Product added';
```

*只有表才支持触发器，视图不支持(临时表也不支持)。*

每个表最多支持6个触发器(每条INSERT、UPDATE、DELETE的之前和之后)。单一触发器不能与多个事件或多个表关联，即如果需要定义一个对INSERT和UPDATE操作执行的触发器，则应该定义俩个触发器。

**触发器失败：**

如果BEFORE触发器失败，则MySQL将不执行请求的操作。此外，如果BEFORE触发器或语句本身失败，MySQL将不执行AFTER触发器(如果有的话)。

#### 21.2 删除触发器

```sql
DROP TRIGGER newproduct;
```

#### 21.3 使用触发器

##### 21.3.1 INSERT触发器

INSERT触发器在INSERT语句执行之前或之后执行。

+ 在INSERT触发器代码内，可引用一个名为NEW的虚拟表，访问被插入的行；
+ 在BEFORE INSERT触发器中，NEW中的值也可以被更新(允许更改被插入的值)；
+ 对于AUTO_INCREMENT列，NEW在INSERT执行之前包含0，在INSERT执行之后包含新的自动生成值；

```sql
CREATE TRIGGER neworder AFTER INSERT ON orders
FOR EACH ROW SELECT NEW.order_num;
```

通常，将BEFORE用于数据验证和净化(目的是保证插入表中的数据确实是需要的数据)。

##### 21.3.2 DELETE触发器

DELETE触发器在DELETE语句执行之前或之后执行。

+ 在DELETE触发器代码内，可以引用一个名为OLD的虚拟表，访问被删除的行；
+ OLD中的值全都是只读的，不能更新。

```sql
CREATE TRIGGER deleteorder BEFORE DELETE ON orders
FOR EACH ROW
BEGIN
	ISNERT INTO archive_orders(order_num, order_date, cust_id)
	VALUES(OLD.order_num, OLD.order_date, OLD.cust_id);
END;
```

使用BEFORE DELETE触发器的优点(相对于AFTER DELETE触发器来说)为，如果由于某种原因，订单不能存档，DELETE本身将被放弃。

使用BEGIN END的好处是触发器能容纳多条SQL语句(在BEGIN END之间)。

##### 21.3.3 UPDATE触发器

UPDATE触发器在UPDATE语句执行之前或之后执行。

+ 在UPDATE触发器代码中，你可以引用一个名为OLD的虚拟表访问以前(UPDATE语句前)的值，引用一个名为NEW的虚拟表访问新更新的值；
+ 在BEFORE UPDATE触发器中，NEW中的值可能也被更新(允许更改将要用于UPDATE语句中的值)。
+ OLD中的值全都是只读的，不能更新。

```sql
CREATE TRIGGER updatevendor BEFORE UPDATE ON vendors
FOR EACH ROW SET NEW.vend_state = Upper(NEW.vend_state);
```

#### 21.4 注意

+ 与其他DBMS相比，MySQL5中支持的触发器相当初级。未来的MySQL版本中有一些改进和增强触发器支持的计划。
+ 创建触发器可能需要特殊的安全访问权限，但是触发器的执行时自动的。如果INSERT、UPDATE或DELETE语句能够执行，则相关的触发器也能执行。
+ 应该用触发器来保证数据的一致性(大小写、格式等)。在触发器中执行这种类型的处理的优点是它总是进行这种处理，而且是透明地进行，与客户机应用无关。
+ 触发器的一种非常有意义的使用时创建审计跟踪。使用触发器，把更改(如果需要，甚至还有之前和之后的状态)记录到另一个表非常容易；
+ MySQL触发器中不支持CALL语句。这表示不能从触发器内调用存储过程。所需的存储过程代码需要复制到触发器内。

***

### 22.管理事务处理

#### 22.1 事务处理

**并非所有引擎都支持事务处理：**MySQL支持几种基本的数据库引擎，并非所有引擎都支持明确的事务处理管理。MyISAM和InnoDB是俩种最常使用的引擎。前者不支持明确的事务处理管理，而后者支持。如果你的应用中需要事务处理功能，则一定要使用正确引擎类型。

*事务处理(transaction processing)：可以用来维护数据库的完整性，它保证成批的MySQL操作要么完全执行，要么完全不执行。*

事务处理是一种机制，用来管理必须成批执行的MySQL操作，以保证数据库包含不完整的操作结果。利用事务处理，可以保证一组操作不会中途停止，它们或者作为整体执行，或者完全不执行(除非明确指示)。如果没有错误发生，整组语句提交给(写到)数据库表。如果发生错误，则进行回退(撤销)以恢复数据库到某个已知且安全的状态。

+ 事务(transaction)指一组SQL语句；
+ 回退(rollback)只撤销指定SQL语句的过程；
+ 提交(commit)指将未存储的SQL语句结果写入数据库表；
+ 保留点(savepoint)指事务处理中设置的临时占位符(placeholder)，你可以对它发布回退(与回退整个事务处理不同)。

#### 22.2 控制事务处理

管理事务处理的关键在于将SQL语句组分解为逻辑块，并明确规定数据何时应该回退，何时不应该回退。

```sql
START TRANSACTION
```

##### 22.2.1 使用ROLLBACK

```sql
SELECT * FROM ordertotals;
START TRANSACTION;
DELETE FROM ordertotals;
SELECT * FROM ordertotals;
ROLLBACK;
SELECT * FROM ordertotals;
```

显然，ROLLBACK只能在一个事务处理内使用(在执行一条START TRANSACTION命令之后)。

##### 22.2.2 使用COMMIT

一般的MySQL语句都是直接针对数据库表执行和编写的。这就是所谓的隐含提交(implicit commit)，即提交(写或保存)操作是自动进行的。

```sql
START TRANSACTION;
DELETE FROM orderitems WHERE order_num = 20010;
DELETE FROM orders WHERE order_num = 20010;
COMMIT;
```

**隐含事务关闭：**

当COMMIT或ROLLBACK语句执行后，事务会自动关闭(将来的更改会隐含提交)。

##### 22.2.3 使用保留点

为了支持回退部分事务处理，必须能在事务处理块中合适的位置放置占位符。这样，如果需要回退，可以回退到某个占位符。

这些占位符称为保留点，为了创建占位符，可使用SAVEPOINT；

```
SAVEPOINT deletel;
ROLLBACK TO deletel;
```

**释放保留点：**保留点在事务处理完成(执行一条ROLLBACK或COMMIT)后自动释放。自MySQL5以来，也可以用RELEASE SAVEPOINT明确地释放保留点。

##### 22.2.4 更改默认的提交行为

为指示MySQL不自动提交更改，需要使用以下语句：

```sql
SET autocommit=0;
```

**标志为连接专用：**autocommit标志是针对每个联结而不是服务器。

***

### 23.全球化和本地化

#### 23.1 字符集和校对顺序

数据库表被用来存储和检索数据。不同的语言和字符集需要以不同的方式存储和检索。因此，MySQL需要适应不同的字符集(不同的字母和字符)，适应不同的排序和检索数据的方法。

+ 字符集为字母和符号的集合；
+ 编码为某个字符集成员的内部集合；
+ 校对为规定字符如何比较的指令；

*使用何种字符集和校对的决定在服务器、数据库和表级进行*。

#### 23.2 使用字符集和校对顺序

```sql
-- 查看所支持的字符集完整列表
SHOW CHARACTER SET;
-- 查看所支持校对的完整列表
SHOW COLLATION;
-- 确定所用的字符集和校对
SHOW VARIABLES LIKE 'character%';
SHOW VARIABLES LIKE 'collation%';
-- 给表指定字符集和校对
CREATE TABLE mytable
(
	column1 INT,
	column2 VARCHAR(10)
)DEFAULT CHARACTER SET hebrew
 COLLATE hebrew_general_ci;
 -- 对单个列设置字符集
CREATE TABLE mytable
(
	column1 INT,
	column2 VARCHAR(10),
	column3 VARCHAR(10) CHARACTER SET latinl COLLATE latinl_general_ci
)DEFAULT CHARACTER SET hebrew
 COLLATE hebrew_general_ci;
  -- 使用与创建表时不同的校对顺序排序特定的SELECT语句
 SELECT * FROM customers
 ORDER BY lastname, firstname, COLLATE latinl_general_cs;
```

+ 如果指定CHARACTER SET 和 COLLATE俩者，则使用这些值；
+ 如果只指定CHARACTER SET，则使用此字符集及其默认的校对；
+ 如果既不指定CHARACTER SET，也不指定COLLATE，则使用数据库默认。

***

### 24. 安全管理

#### 24.1 访问控制

+ 多数用户只需要对表进行读和写，但少数用户甚至需要能创建和删除表；
+ 某些用户需要读表，但可能不需要更新表；
+ 允许某些用户添加数据，但不允许删除数据；
+ 管理员需要拥有处理用户账号的权限，但多数用户不需要；
+ 允许用户通过存储过程访问数据，但不允许直接访问数据；
+ 更具用户登录的地点限制对某些功能的访问。

**不要使用root，仅在绝对需要时使用root。不应该在日常的MySQL操作中使用root。**

#### 24.2 管理用户

MySQL用户账号和信息存储能在名为mysql的MySQL数据库中。

```sql
use mysql;
SELECT user FROM user;
```

mysql数据库有一个名为user的表，包含了所有用户账号。

##### 24.2.1 创建账号

```sql
-- 创建用户
 CREATE USER ben IDENTIFIED BY 'p@$$wOrd';
-- 重命名账号
RENAME USER ben TO bforta;
```

**指定散列口令：**

INDENTIFIED BY指定的口令为纯文本，MySQL将在保存到user表之前对其进行加密。为了作为散列值指定口令，使用IDENTIFIED BY PASSWORD。

##### 24.2.2 删除用户账号

```sql
DROP USER bgorta;
```

##### 24.2.3 设置访问权限

```sql
-- 查看用户权限
SHOW GRANTS FOR  bforta;
-- 授予SELECT访问权限
GRANT SELECT ON crashcourse.* TO bforta;
-- 撤销权限
REVOKE SELECT ON crashcourse.* FROM bforta;
-- 同时授权多个
GRANT SELECT, INSERT ON crashcourse.* TO bforta;
```

GRANT和REVOKE可在几个层次上控制访问权限：

+ 整个服务器，使用GRANT ALL 和 REVOKE ALL；
+ 整个数据库，使用ON database.*；
+ 特定的表，使用ON database.table；
+ 特定的列；
+ 特定的存储过程。

##### 24.2.4 更改口令

```sql
-- 更改用户口令
SET PASSWORD FOR bforta = Password('n3w p@$$wOrd');
-- 
SET PASSWORD = Password('n3w p@$$wOrd');
```

### 25.数据库维护

#### 25.1 备份数据

MySQL数据也必须经常备份。

MySQL是基于磁盘的文件，普通备份系统和例程就能备份MySQL的数据。

但由于这些文件总是处于打开和使用状态，普通的文件副本备份不一定有效。

**解决方案：**

+ 使用命令行实用程序mysqldump转储所有数据库内容到某个外部文件。在进行常规备份前，这个实用程序应该正常运行，以便能正确备份转储文件；
+ 可用命令行实用程序mysqlhotcopy从一个数据库复制所有数据(并非所有数据库引擎都支持这个实用程序)；
+ 可以使用MySQL的BACKUP TABLE或SELECT INTO OUTFILE转储所有数据到某个外部文件。这俩条语句都接受将要创建的系统文件名，此系统文件必须不存在，否则会出错。数据可以用RESTORE TABLE来复原。

*首先刷新未写数据：*

为了保证所有数据被写入到磁盘(包括索引数据)，可能需要在进行备份前使用FLUSH TABLE语句。

#### 25.2 进行数据库维护

MySQL提供一系列的语句，用来保证数据库正确和正常运行。

+ ANALYZE TABLE，用来检查表键是否正确。

+ CHECK TABLE用来针对许多问题对表进行检查。

  CHECK TABLE支持一系列的用于MyISAM表的方式。CHANGED检查自最后一次检查以来改动过的表，EXTENDED执行最彻底的检查，FAST只检查未正常关闭的表，MEDIUM检查所有被删除的链接并进行键检查，QUICK只进行快速扫描。

+ 如果MyISAM表访问产生不正确和不一致的结果，可能需要用REPAIR TABLE来修复相应的表。

+ 如果从一个表删除大量数据，应该使用OPTIMEZE TABLE来收回所用的空间，从而优化表的性能。

#### 25.3 诊断启动问题

MySQL服务器自身通过在命令行上执行mysqld启动。

几个重要的mysqld命令行选项：

+ --help 显示帮助——一个选项列表；
+ --safe-mode 装载减去某些最佳配置的服务器；
+ --verbose 显示全文本消息(为获得更详细的帮助信息与 --help 联合使用)；
+ --version 显示版本信息然后退出；

#### 25.4 查看日志文件

MySQL维护管理员依赖的一系列日志文件。

**主要的日志文件：**

+ 错误日志。包含启动和关闭问题以及任意关键错误的细节。该日志通常名为hostname.err，位于data目录。该日志名可用 --log-error命令行选项修改；
+ 查询日志。记录所有的MySQL活动，在诊断问题时非常有用。该日志文件可能会很快变得非常大，因此不应该长期使用它。该日志通常名为hostname.log，位于data目录中。此名字可以用--log命令行选项更改。
+ 二进制日志。记录更新过数据(或者更新过数据)的所有语句。该日志通常名为hostname-bin，位于data目录内。此名字可以用--log-bin命令行选项更改。该日志文件在MySQL5中添加，以前的MySQL版本中使用的是更新日志。
+ 缓慢查询日志。该日志记录执行缓慢的任何查询。该日志在确定数据库何处需要优化很有用。通常名为hostname-slow.log，位于data目录中。此名字可以用--log-slow-queries命令行选项更改。

*在使用日志时，可用FLUSH LOGS 语句来刷新和重新开始所有日志文件。*

***

### 26.改善性能

#### 26.1 改善性能

**使用MySQL的相关建议**

1. 一般来说，关键的生产DBMS应该运行在自己的专用服务器上；

2. MySQL是一系列的默认设置预先配置的，从这些设置开始通常是很好的。但过一段时间后需要调整内存分配、缓冲区大小等。

3. MySQL是一个多用户多线程的DBMS，即它经常同时执行多个任务。

   如果这些任务中的某一个执行缓慢，则所有请求都会执行缓慢。如果遇到了显著的性能不良，可使用SHOW PROCESSLIST显示所有活动进程(以及它们的线程ID和执行时间)。还可以用KILL命令终结某个特定的进程(使用这个命令需要作为管理员登录)。

4. 总是有不止一种方法编写同一条SELECT语句。应该试验联结、并、子查询等，找出最佳方法。

5. 使用EXPLAIN语句让MySQL解释它将如何执行一条SELECT语句。

6. 一般来说，存储过程执行得比一条一条地执行其中的各条MySQL语句快。

7. 应该总是使用正确的数据类型。

8. 绝不要检索比需求还要多的数据。(不要使用SELECT *)。

9. 有的操作(包括INSERT)支持一个可选的DELAYED关键字，如果使用它，将把控制立即返回给调用程序，并且一旦有可能就实际执行该操作。

10. 在导入数据时，应该关闭自动提交。

11. 必须索引数据库表以改善数据检索的性能。

12. 对于复杂的OR条件，通过使用多条SELECT语句和连接它们的UNION语句，会有极大的性能提升。

13. 索引改善数据检索的能力，但损害数据插入、删除和更新的性能。(索引可根据需要添加和删除)

14. LIKE很慢。一般来说，最好使用FULLTEXT而不是LIKE。

15. 数据库是不断变化的实体。由于表的使用和内容的更改，理想的优化和配置也会改变。

16. 最重要的规则是，每条规则都会在某些条件下被打破。







