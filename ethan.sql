/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.47-MariaDB : Database - ethan
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ethan` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `ethan`;

/*Table structure for table `balance_channel` */

DROP TABLE IF EXISTS `balance_channel`;

CREATE TABLE `balance_channel` (
  `channel_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) NOT NULL COMMENT '渠道名称',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `balance_channel` */

LOCK TABLES `balance_channel` WRITE;

insert  into `balance_channel`(`channel_id`,`name`,`created_at`,`updated_at`) values (1,'飞客茶馆','2017-04-14 09:46:37',NULL);
insert  into `balance_channel`(`channel_id`,`name`,`created_at`,`updated_at`) values (2,'飞客旅行','2017-04-14 09:46:47',NULL);
insert  into `balance_channel`(`channel_id`,`name`,`created_at`,`updated_at`) values (3,'飞客返利','2017-04-14 09:46:54',NULL);

UNLOCK TABLES;

/*Table structure for table `balance_income` */

DROP TABLE IF EXISTS `balance_income`;

CREATE TABLE `balance_income` (
  `income_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `operator_type` tinyint(1) unsigned NOT NULL COMMENT '操作类型',
  `channel_id` int(1) unsigned NOT NULL COMMENT '渠道id',
  `operator_id` int(3) unsigned NOT NULL COMMENT '具体的操作行为',
  `change_balance` float(10,2) NOT NULL COMMENT '变动金额',
  `before_balance` float(10,2) NOT NULL COMMENT '变动前金额',
  `after_balance` float(10,2) NOT NULL COMMENT '变动后金额',
  `left_balance` float(10,2) NOT NULL COMMENT '剩余可支出余额',
  `active_member` varchar(20) NOT NULL COMMENT '操作人',
  `type` int(10) DEFAULT NULL COMMENT '1.订单，2帖子',
  `relation_id` int(10) DEFAULT NULL COMMENT '具体内容的id',
  `created_at` datetime NOT NULL COMMENT '操作时间，入库时间',
  `updated_at` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`income_id`),
  KEY `m_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额变动明细表（收入）';

/*Data for the table `balance_income` */

LOCK TABLES `balance_income` WRITE;

UNLOCK TABLES;

/*Table structure for table `balance_income_spend` */

DROP TABLE IF EXISTS `balance_income_spend`;

CREATE TABLE `balance_income_spend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `income_id` int(10) unsigned NOT NULL COMMENT '收入id',
  `spend_id` int(10) unsigned NOT NULL COMMENT '支出id',
  `change_balance` float(10,2) NOT NULL COMMENT '支出金额',
  `rollback_balance` float(10,2) NOT NULL COMMENT '剩余可回滚金额，一般是与支持金额相等',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_id` (`income_id`),
  KEY `s_id` (`spend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收入支出关系表';

/*Data for the table `balance_income_spend` */

LOCK TABLES `balance_income_spend` WRITE;

UNLOCK TABLES;

/*Table structure for table `balance_log` */

DROP TABLE IF EXISTS `balance_log`;

CREATE TABLE `balance_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `operator_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作类型，1增加，2消耗，3回滚，4回收，1和3是增加收入，2和4是消耗收入',
  `operator_id` int(10) NOT NULL DEFAULT '0' COMMENT '具体操作行为',
  `change_balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '变化金额',
  `channel_id` int(10) NOT NULL DEFAULT '0' COMMENT '渠道id',
  `member_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `active_member` varchar(20) NOT NULL DEFAULT '' COMMENT '操作人',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '根据具体内容分类',
  `relation_id` tinyint(10) NOT NULL DEFAULT '0' COMMENT '关联具体内容的id',
  `before_balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动前用户金额',
  `after_balance` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动后用户金额',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `log_operator_type` (`operator_type`),
  KEY `log_operator_id` (`operator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额变动log表';

/*Data for the table `balance_log` */

LOCK TABLES `balance_log` WRITE;

UNLOCK TABLES;

/*Table structure for table `balance_member` */

DROP TABLE IF EXISTS `balance_member`;

CREATE TABLE `balance_member` (
  `member_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `nickname` varchar(20) DEFAULT NULL COMMENT '用户昵称',
  `sex` enum('男','女','保密') DEFAULT '男' COMMENT '性别',
  `realname` varchar(20) DEFAULT NULL COMMENT '真实姓名',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `phone` int(11) DEFAULT NULL COMMENT '手机号',
  `balance` float(10,2) DEFAULT '0.00' COMMENT '剩下的余额=incomebalance-spendbalance+rollbackbalance-recoverybalance',
  `incomebalance` float(10,2) DEFAULT '0.00' COMMENT '总收入余额',
  `spendbalance` float(10,2) DEFAULT '0.00' COMMENT '总支出余额',
  `rollbackbalance` float(10,2) DEFAULT '0.00' COMMENT '总回滚余额',
  `recoverybalance` float(10,2) DEFAULT '0.00' COMMENT '总回收余额',
  `created_at` datetime DEFAULT NULL COMMENT '产生时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表\r\n';

/*Data for the table `balance_member` */

LOCK TABLES `balance_member` WRITE;

insert  into `balance_member`(`member_id`,`nickname`,`sex`,`realname`,`birthday`,`email`,`phone`,`balance`,`incomebalance`,`spendbalance`,`rollbackbalance`,`recoverybalance`,`created_at`,`updated_at`) values (1,'测试','男','测试','2017-04-28','1@qq.com',1234556,0.00,0.00,0.00,0.00,0.00,'2017-04-28 10:16:29','2017-04-28 10:16:31');

UNLOCK TABLES;

/*Table structure for table `balance_operation_code` */

DROP TABLE IF EXISTS `balance_operation_code`;

CREATE TABLE `balance_operation_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `operation_id` int(3) NOT NULL COMMENT '操作code',
  `name` varchar(30) NOT NULL COMMENT '操作名称',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='操作code明细';

/*Data for the table `balance_operation_code` */

LOCK TABLES `balance_operation_code` WRITE;

insert  into `balance_operation_code`(`id`,`operation_id`,`name`,`created_at`,`updated_at`) values (1,101,'返现至余额','2017-04-28 10:18:17','2017-04-28 10:18:18');
insert  into `balance_operation_code`(`id`,`operation_id`,`name`,`created_at`,`updated_at`) values (2,201,'提现','2017-04-28 10:18:34','2017-04-28 10:18:36');
insert  into `balance_operation_code`(`id`,`operation_id`,`name`,`created_at`,`updated_at`) values (3,202,'购买飞米商城商品','2017-04-28 10:19:14','2017-04-28 10:19:16');
insert  into `balance_operation_code`(`id`,`operation_id`,`name`,`created_at`,`updated_at`) values (4,203,'购买飞客权益卡','2017-04-28 10:19:25','2017-04-28 10:19:27');
insert  into `balance_operation_code`(`id`,`operation_id`,`name`,`created_at`,`updated_at`) values (5,301,'退款至余额','2017-04-28 10:19:48','2017-04-28 10:19:49');
insert  into `balance_operation_code`(`id`,`operation_id`,`name`,`created_at`,`updated_at`) values (6,401,'系统回收','2017-04-28 10:19:59','2017-04-28 10:20:01');

UNLOCK TABLES;

/*Table structure for table `balance_operation_type` */

DROP TABLE IF EXISTS `balance_operation_type`;

CREATE TABLE `balance_operation_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL COMMENT 'typeid指定',
  `name` varchar(20) NOT NULL COMMENT '名称',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='总操作类型表';

/*Data for the table `balance_operation_type` */

LOCK TABLES `balance_operation_type` WRITE;

insert  into `balance_operation_type`(`id`,`type_id`,`name`,`created_at`,`updated_at`) values (1,1,'收入','2017-04-13 17:58:05','0000-00-00 00:00:00');
insert  into `balance_operation_type`(`id`,`type_id`,`name`,`created_at`,`updated_at`) values (2,2,'支出','2017-04-13 17:58:16','0000-00-00 00:00:00');
insert  into `balance_operation_type`(`id`,`type_id`,`name`,`created_at`,`updated_at`) values (3,3,'回滚','2017-04-13 17:58:47','0000-00-00 00:00:00');
insert  into `balance_operation_type`(`id`,`type_id`,`name`,`created_at`,`updated_at`) values (4,4,'回收','2017-04-13 17:59:19','0000-00-00 00:00:00');

UNLOCK TABLES;

/*Table structure for table `balance_recovery` */

DROP TABLE IF EXISTS `balance_recovery`;

CREATE TABLE `balance_recovery` (
  `recovery_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `operator_type` tinyint(1) unsigned NOT NULL COMMENT '操作类型',
  `channel_id` int(1) unsigned NOT NULL COMMENT '渠道id',
  `operator_id` int(3) unsigned NOT NULL COMMENT '具体的操作行为',
  `change_balance` float(10,2) NOT NULL COMMENT '变动金额',
  `before_balance` float(10,2) NOT NULL COMMENT '变动前金额',
  `after_balance` float(10,2) NOT NULL COMMENT '变动后金额',
  `active_member` varchar(20) NOT NULL COMMENT '操作人',
  `type` int(10) DEFAULT NULL COMMENT '1.订单，2帖子',
  `relation_id` int(10) DEFAULT NULL COMMENT '具体内容的id',
  `created_at` datetime NOT NULL COMMENT '操作时间，入库时间',
  `updated_at` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`recovery_id`),
  KEY `m_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额变动明细表（回收）';

/*Data for the table `balance_recovery` */

LOCK TABLES `balance_recovery` WRITE;

UNLOCK TABLES;

/*Table structure for table `balance_recovery_income` */

DROP TABLE IF EXISTS `balance_recovery_income`;

CREATE TABLE `balance_recovery_income` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `recovery_id` int(10) unsigned NOT NULL COMMENT '回收id',
  `income_id` int(10) unsigned NOT NULL COMMENT '收入id',
  `change_balance` float(10,2) NOT NULL COMMENT '回收金额',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额回收关系表';

/*Data for the table `balance_recovery_income` */

LOCK TABLES `balance_recovery_income` WRITE;

UNLOCK TABLES;

/*Table structure for table `balance_rollback` */

DROP TABLE IF EXISTS `balance_rollback`;

CREATE TABLE `balance_rollback` (
  `rollback_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `operator_type` tinyint(1) unsigned NOT NULL COMMENT '操作类型',
  `channel_id` int(1) unsigned NOT NULL COMMENT '渠道id',
  `operator_id` int(3) unsigned NOT NULL COMMENT '具体的操作行为',
  `change_balance` float(10,2) NOT NULL COMMENT '变动金额',
  `before_balance` float(10,2) NOT NULL COMMENT '变动前金额',
  `after_balance` float(10,2) NOT NULL COMMENT '变动后金额',
  `active_member` varchar(20) NOT NULL COMMENT '操作人',
  `type` int(10) DEFAULT NULL COMMENT '1.订单，2帖子',
  `relation_id` int(10) DEFAULT NULL COMMENT '具体内容的id',
  `created_at` datetime NOT NULL COMMENT '操作时间，入库时间',
  `updated_at` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`rollback_id`),
  KEY `m_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额变动明细表（回滚）';

/*Data for the table `balance_rollback` */

LOCK TABLES `balance_rollback` WRITE;

UNLOCK TABLES;

/*Table structure for table `balance_rollback_income` */

DROP TABLE IF EXISTS `balance_rollback_income`;

CREATE TABLE `balance_rollback_income` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `rollback_id` int(10) unsigned NOT NULL COMMENT '回滚id',
  `spend_id` int(10) unsigned NOT NULL COMMENT '支出id',
  `income_id` int(10) unsigned NOT NULL COMMENT '收入id',
  `change_balance` float(10,2) NOT NULL COMMENT '回滚金额',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额回滚收入关系表';

/*Data for the table `balance_rollback_income` */

LOCK TABLES `balance_rollback_income` WRITE;

UNLOCK TABLES;

/*Table structure for table `balance_spend` */

DROP TABLE IF EXISTS `balance_spend`;

CREATE TABLE `balance_spend` (
  `spend_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `operator_type` tinyint(1) NOT NULL COMMENT '操作类型',
  `channel_id` int(1) NOT NULL COMMENT '渠道id',
  `operator_id` int(3) NOT NULL COMMENT '具体的操作行为',
  `change_balance` float(10,2) NOT NULL COMMENT '变动金额',
  `before_balance` float(10,2) NOT NULL COMMENT '变动前金额',
  `after_balance` float(10,2) NOT NULL COMMENT '变动后金额',
  `active_member` varchar(20) NOT NULL COMMENT '操作人',
  `type` int(10) DEFAULT NULL COMMENT '1.订单，2帖子',
  `relation_id` int(10) DEFAULT NULL COMMENT '具体内容的id',
  `created_at` datetime NOT NULL COMMENT '操作时间，入库时间',
  `updated_at` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`spend_id`),
  KEY `m_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='余额变动明细表（支出）';

/*Data for the table `balance_spend` */

LOCK TABLES `balance_spend` WRITE;

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
