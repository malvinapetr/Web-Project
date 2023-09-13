SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `get_weekly_low`;
DELIMITER $$
CREATE EVENT `get_weekly_low`
ON SCHEDULE EVERY 1 WEEK STARTS '2023-09-17 00:00:01'
ON COMPLETION PRESERVE
DO BEGIN
    declare tmp_week_low DECIMAL(5,2);
    declare products INT;
    declare i INT;
    declare prod_id INT;
    declare starting_date DATE;
    declare end_date DATE;

    select count(*) into products from lows;
    set starting_date = CURDATE() - interval 7 day;
    set end_date = CURDATE() - interval 1 day;

    set i=0;
    while (i < products) do
        select p_id into prod_id from lows limit i,1;

        select temp_last_week_low into tmp_week_low FROM lows where p_id = prod_id;
        update lows set last_week_low = tmp_week_low where p_id = prod_id;
        update lows set temp_last_week_low = 50 where p_id = prod_id;

        insert into `total_week_lows` (`p_id`, `week_low`, `starting_date`,`ending_date`) VALUES
        (prod_id, tmp_week_low,starting_date, end_date);
        
        set i=i+1;
    end while;       
END$$
DELIMITER ;