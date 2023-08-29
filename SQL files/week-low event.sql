SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `get_weekly_low`;
DELIMITER $$
CREATE EVENT `get_weekly_low`
ON SCHEDULE EVERY 1 WEEK STARTS '2023-08-29 00:00:00'
ON COMPLETION PRESERVE
DO BEGIN
    declare tmp_week_low DECIMAL(5,2);
    declare products INT;
    declare i INT;
    declare prod_id INT;

    select count(*) into products from lows;

    set i=0;
    while (i < products) do
        select p_id into prod_id from lows limit i,1;

        select temp_last_week_low into tmp_week_low FROM lows where p_id = prod_id;
        update lows set last_week_low = tmp_week_low where p_id = prod_id;
        update lows set temp_last_week_low = 5000 where p_id = prod_id;

        set i=i+1;
    end while;       
END$$
DELIMITER ;