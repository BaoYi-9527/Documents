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

-- 建立 poptbl2 表
CREATE TABLE `poptbl2` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `perf_name` VARCHAR ( 50 ) NOT NULL COMMENT '县名',
    `sex` TINYINT(4) NOT NULL COMMENT '性别',
    `population` INT ( 10 ) NOT NULL COMMENT '人口',
    PRIMARY KEY (`id`)
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '人口表';

-- 填充数据
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

-- 简化上述写法
SELECT perf_name,
    -- 男性人口
    SUM(CASE WHEN `sex` = 1 THEN population ELSE 0 END) AS cnt_m,
    -- 女性人口
    SUM(CASE WHEN `sex` = 2 THEN population ELSE 0 END) AS cnt_f
FROM `poptbl2`
GROUP BY perf_name;

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

-- 创建数据表
CREATE TABLE `salaries`(
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL COMMENT '姓名',
    `salary` DECIMAL(10, 2) NOT NULL COMMENT '薪水',
    PRIMARY KEY (`id`)
) ENGINE=INNODB CHARACTER SET=utf8 COLLATE=utf8_general_ci COMMENT = '员工工资信息表';
-- 填充数据
INSERT INTO `salaries` (`name`, `salary`) VALUES ('相田', 30000);
INSERT INTO `salaries` (`name`, `salary`) VALUES ('神琦', 27000);
INSERT INTO `salaries` (`name`, `salary`) VALUES ('木村', 22000);
INSERT INTO `salaries` (`name`, `salary`) VALUES ('齐藤', 29000);

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


-- 数据源
CREATE TABLE `course_master`(
    `course_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `course_name` VARCHAR(50) NOT NULL COMMENT '课程',
    PRIMARY KEY (`course_id`)
) ENGINE=INNODB CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `open_course`(
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `month` int(10) NOT NULL COMMENT '月份',
    `course_id` int(10) NOT NULL COMMENT '课程ID',
    PRIMARY KEY (`id`)
) ENGINE=INNODB CHARACTER SET=utf8 COLLATE=utf8_general_ci;

-- 填充数据
INSERT INTO `course_master` (`course_name`) VALUES ('会计入门');
INSERT INTO `course_master` (`course_name`) VALUES ('财务知识');
INSERT INTO `course_master` (`course_name`) VALUES ('薄记考试');
INSERT INTO `course_master` (`course_name`) VALUES ('税务师');

INSERT INTO `open_course` (`month`, `course_id`) VALUES (200706, 1);
INSERT INTO `open_course` (`month`, `course_id`) VALUES (200706, 3);
INSERT INTO `open_course` (`month`, `course_id`) VALUES (200706, 4);
INSERT INTO `open_course` (`month`, `course_id`) VALUES (200707, 4);
INSERT INTO `open_course` (`month`, `course_id`) VALUES (200708, 2);
INSERT INTO `open_course` (`month`, `course_id`) VALUES (200708, 4);

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


-- 数据源
CREATE TABLE `student_club`(
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `std_id` int(10) NOT NULL COMMENT '学号',
    `club_id` int(10) NOT NULL COMMENT '社团ID',
    `club_name` VARCHAR(50) NOT NULL COMMENT '社团名称',
    `main_club_flag` VARCHAR(50) NOT NULL COMMENT '主社团标志',
    PRIMARY KEY (`id`)
) ENGINE=INNODB CHARACTER SET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (100, 1, '棒球', 'Y');
INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (100, 2, '管弦乐', 'N');
INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (200, 2, '管弦乐', 'N');
INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (200, 3, '羽毛球', 'Y');
INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (200, 4, '足球', 'N');
INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (300, 4, '足球', 'N');
INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (400, 5, '游泳', 'N');
INSERT INTO `student_club` (`std_id`, `club_id`, `club_name`, `main_club_flag`) VALUES (500, 6, '围棋', 'N');






