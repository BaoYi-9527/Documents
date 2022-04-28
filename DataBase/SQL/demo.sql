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


-- 数据源
-- 建立 poptbl 表
CREATE TABLE `poptbl` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`perf_name` VARCHAR ( 50 ) NOT NULL COMMENT '县名',
	`population` INT ( 10 ) NOT NULL COMMENT '人口',
	PRIMARY KEY (`id`)
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '人口表';

-- 插入数据
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('德岛',100);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('香川',200);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('爱媛',150);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('高知',200);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('福冈',300);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('佐贺',100);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('长崎',200);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('东京',400);
INSERT INTO `Poptbl` (`perf_name`,`population`) VALUES ('群马',50);


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


-- 建立 poptbl2 表
CREATE TABLE `poptbl2` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `perf_name` VARCHAR ( 50 ) NOT NULL COMMENT '县名',
    `sex` TINYINT(4) NOT NULL COMMENT '性别',
    `population` INT ( 10 ) NOT NULL COMMENT '人口',
    PRIMARY KEY (`id`)
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '人口表';

INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('德岛', 1, 60);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('德岛', 2, 40);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('香川', 1, 100);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('香川', 2, 100);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('爱媛', 1, 50);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('爱媛', 2, 100);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('高知', 1, 100);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('高知', 1, 100);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('福冈', 1, 100);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('福冈', 2, 200);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('佐贺', 1, 20);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('佐贺', 2, 80);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('长崎', 1, 125);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('长崎', 2, 200);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('东京', 1, 250);
INSERT INTO `poptbl2` (`perf_name`,`sex`,`population`) VALUES ('东京', 2, 150);

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

SELECT perf_name,
       -- 男性人口
       SUM(CASE WHEN `sex` = 1 THEN population ELSE 0 END) AS cnt_m,
       -- 女性人口
       SUM(CASE WHEN `sex` = 2 THEN population ELSE 0 END) AS cnt_f
FROM `poptbl2`
GROUP BY perf_name;