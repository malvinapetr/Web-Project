drop trigger if exists new_price_insert;
delimiter $
create trigger new_price_insert
after insert on prices

for each row
begin

declare new_pid int;
declare init_price DECIMAL(5,2);

select id into new_pid from products where products.name like new.name;
set init_price = new.price;

insert into `lows` (`p_id`, `yesterday_low`,`last_week_low`, `temp_last_week_low`) VALUES
    (new_pid, init_price,0,init_price);

insert into `last_week_lows` (`p_id`, `sunday`,`monday`, `tuesday`,`wednesday`,`thursday`,`friday`,`saturday`) VALUES
    (new_pid, 0,0,0,0,0,0,0);

end$
delimiter ;
