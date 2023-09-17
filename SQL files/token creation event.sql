SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `create_monthly_tokens`;
DELIMITER $$
CREATE EVENT `create_monthly_tokens`
ON SCHEDULE EVERY 1 MONTH STARTS '2023-10-01 00:00:00'
ON COMPLETION PRESERVE
DO BEGIN
    declare count int;
    select count(*) into count from user;
    set count = count * 100;
    update tokens set tokens = count where id = 1;
END$$
DELIMITER ;





