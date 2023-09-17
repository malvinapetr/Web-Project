SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `update_offers_exp_check`;
DELIMITER $$
CREATE EVENT `update_offers_exp_check`
ON SCHEDULE EVERY 1 DAY STARTS '2023-09-21 00:00:00'
ON COMPLETION PRESERVE
DO BEGIN
    declare num_offers INT;
    declare i INT;
    declare prod_id INT;
    declare offer_id INT;
    declare curr_date DATE;
    declare expiration_date DATE;
    declare offer_price DECIMAL(5,2);
    declare lw_low DECIMAL(5,2);
    declare yd_low DECIMAL(5,2);

    select count(*) into num_offers from offers;
    select curdate() into curr_date;

    set i=0;
    while (i < num_offers) do
        select id, exp_date,price,p_id into offer_id, expiration_date,offer_price, prod_id from offers limit i,1;
        select last_week_low, yesterday_low into lw_low,yd_low FROM lows where p_id = prod_id;
    
        if(expiration_date like curr_date and offer_price < (lw_low - 0.2*lw_low)) or (expiration_date like curr_date and offer_price < (yd_low - 0.2*yd_low)) then
            set expiration_date = curdate() + interval 7 day; 
            update offers set exp_date = expiration_date where id = offer_id;
        end if;
        set i=i+1;
    end while;       

END$$
DELIMITER ;