SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `get_daily_low`;
DELIMITER $$
CREATE EVENT `get_daily_low`
ON SCHEDULE EVERY 1 DAY STARTS '2023-08-29 00:00:30'
ON COMPLETION PRESERVE
DO BEGIN
    declare prod_id INT;
    declare products INT;
    declare i INT;
    declare total_pois INT;
    declare pois_with_offers INT;
    declare pois_without_offers INT;
    declare nooffer_price DECIMAL(5,2);
    declare offer_price DECIMAL(5,2);
    declare tmp_week_low DECIMAL(5,2);
    declare avg_price DECIMAL(5,2);
    declare yesterday_date DATE;
    declare prod_name VARCHAR(45);

    select count(*) into products from lows;
    SELECT DATE_SUB(CURDATE(), INTERVAL 1 DAY) into yesterday_date;

    set i=0;
    while (i < products) do
        select p_id into prod_id from lows limit i,1;
        select count(*) into total_pois from pois;

        SELECT count(distinct pois.id) into pois_with_offers FROM pois inner join offers 
        on pois.id = offers.poi_id and offers.p_id = prod_id and offers.sub_date <= CURDATE() and offers.exp_date >= CURDATE();
        select name into prod_name from products where id = prod_id;

        SELECT price into nooffer_price FROM prices where name like prod_name and date like yesterday_date;
        SELECT sum(price) into offer_price from offers where p_id = prod_id and offers.sub_date <= CURDATE() and offers.exp_date >= CURDATE();

        SELECT temp_last_week_low into tmp_week_low FROM lows where p_id = prod_id;

        set pois_without_offers = total_pois - pois_with_offers;
        set nooffer_price = nooffer_price*pois_without_offers;
        set avg_price = round((nooffer_price + offer_price) / total_pois);

        UPDATE lows set yesterday_low = avg_price where lows.p_id = prod_id;

        if (tmp_week_low > avg_price) then
            update lows set temp_last_week_low = avg_price where lows.p_id = prod_id;
        end if;

        set i=i+1;
    end while;    

  
END$$
DELIMITER ;