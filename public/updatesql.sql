/******************************2017年4月17日13:32:01*********************************/
ALTER TABLE `ethan`.`balance_income` CHANGE `left_value` `left_balance` FLOAT(10,2) NOT NULL COMMENT '剩余可支出余额'; 


ALTER TABLE `ethan`.`balance_income` CHANGE `cteated_at` `created_at` DATETIME NOT NULL COMMENT '操作时间，入库时间'; 




