CREATE TABLE `user_info`
(
    `id`          int(11) NOT NULL AUTO_INCREMENT,
    `nick`        char(10) CHARACTER SET latin1  DEFAULT NULL,
    `password`    char(32) CHARACTER SET latin1 NOT NULL,
    `register_tm` datetime                      NOT NULL COMMENT '注册时间',
    `login_tm`    datetime                       DEFAULT NULL COMMENT '最后一次登录时间',
    `head`        char(100) CHARACTER SET latin1 DEFAULT NULL COMMENT '头像',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

