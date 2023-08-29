SET GLOBAL event_scheduler=ON;

DROP EVENT IF EXISTS `distribute_monthly_tokens`;
DELIMITER $$
CREATE EVENT `distribute_monthly_tokens`
ON SCHEDULE EVERY 1 MONTH STARTS '2023-08-31 23:59:00'
ON COMPLETION PRESERVE
DO BEGIN
    declare new_tokens int;
    declare users int;
    declare new_username varchar(45);
    declare new_t_tokens int;
    declare i smallint; 

    select tokens into new_tokens from tokens where id=1;
    select count(*) into users from user;
    
    set i = 0;
    if(users > 5) then
        set new_tokens = round(0.8 * new_tokens / (users / 3));
        set users = round(users / 3);
        while (i < users) do
            select username, t_tokens into new_username,new_t_tokens from user order by m_score desc limit i,1;
            set new_t_tokens = new_t_tokens + new_tokens;
            update ignore user set t_tokens = new_t_tokens, m_tokens = new_tokens where username like new_username;
            set i = i + 1;
        end while;  
    else
        set new_tokens = round(0.8 * new_tokens / 3);
        while (i < 3) do
            select username, t_tokens into new_username,new_t_tokens from user order by m_score desc limit i,1;
            set new_t_tokens = new_t_tokens + new_tokens;
            update ignore user set t_tokens = new_t_tokens, m_tokens = new_tokens where username like new_username;
            set i = i + 1;
        end while;  
    end if;
END$$
DELIMITER ;













