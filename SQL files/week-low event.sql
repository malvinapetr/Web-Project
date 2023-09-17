SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `get_weekly_low`;
DELIMITER $$
CREATE EVENT `get_weekly_low`
ON SCHEDULE EVERY 1 WEEK STARTS '2023-09-24 00:00:00'
ON COMPLETION PRESERVE
DO BEGIN
    declare tmp_week_low DECIMAL(5,2);
    declare tmp_week_avg DECIMAL(5,2);
    declare sun DECIMAL(5,2);
    declare mon DECIMAL(5,2);
    declare tue DECIMAL(5,2);
    declare wed DECIMAL(5,2);
    declare thu DECIMAL(5,2);
    declare fri DECIMAL(5,2);
    declare sat DECIMAL(5,2);
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
        select sunday, monday, tuesday, wednesday, thursday, friday, saturday into sun, mon,tue, wed,thu, fri, sat 
        from last_week_lows where p_id = prod_id;

        update lows set last_week_low = tmp_week_low where p_id = prod_id;
        update lows set temp_last_week_low = 50 where p_id = prod_id;

        set tmp_week_avg = (sun + mon + tue + wed + thu + fri + sat) / 7;
        insert into `total_week_averages` (`p_id`, `week_avg`, `starting_date`,`ending_date`) VALUES
        (prod_id, tmp_week_avg,starting_date, end_date);
        
        set i=i+1;
    end while;       
END$$
DELIMITER ;