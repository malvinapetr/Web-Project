SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `reset_monthly_score`;
DELIMITER $$
CREATE EVENT `reset_monthly_score`
ON SCHEDULE EVERY 1 MONTH STARTS '2023-08-31 23:59:00'
ON COMPLETION PRESERVE
DO BEGIN
    update user set m_score = 0;
END$$
DELIMITER ;