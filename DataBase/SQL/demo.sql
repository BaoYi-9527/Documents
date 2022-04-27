-- CASE表达式的写法

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

-- 剩余的 WHEN 子句被忽略的写法
CASE
    WHEN col_1 IN ('a', 'b') THEN 'The first'
    WHEN col_2 IN ('a') THEN 'The second'   -- 该条件被忽略 不会执行
ELSE 'others' END

-- 将县编号转换成地区编号
SELECT CASE pred_name
    WHEN '德岛' THEN `四国`
    WHEN '香川' THEN `四国`
    WHEN '爱媛' THEN `四国`
    WHEN '高知' THEN `四国`
    WHEN '福冈' THEN `九州`
    WHEN '佐贺' THEN `九州`
    WHEN '长崎' THEN `九州`
ELSE '其他' END AS district, SUM(population)
FORM `PobTbl`
