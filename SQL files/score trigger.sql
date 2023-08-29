drop trigger if exists score_calc;
delimiter $
create trigger score_calc
before update on offers

for each row
begin

declare cur_t_score int;
declare cur_m_score int;
declare count int;

select distinct t_score, m_score into cur_t_score, cur_m_score 
from user inner join offers 
where user.username like new.username; 

select lcount into count from offers where id = new.id;
if (count > new.lcount) then
   set cur_m_score = cur_m_score - 5;
   if (cur_m_score < 0) then
      set cur_m_score = 0;
   else 
      set cur_t_score = cur_t_score - 5;   
   end if;
elseif (count < new.lcount) then
   set cur_m_score = cur_m_score + 5;
   set cur_t_score = cur_t_score + 5;
end if;

select dcount into count from offers where id = new.id;
if (count < new.dcount) then
   set cur_m_score = cur_m_score - 1;
   if (cur_m_score < 0) then
      set cur_m_score = 0;
   else 
      set cur_t_score = cur_t_score - 1;
   end if;
elseif count > new.dcount then
   set cur_m_score = cur_m_score + 1;
   set cur_t_score = cur_t_score + 1;
end if;


update user set t_score = cur_t_score, m_score = cur_m_score where username like new.username;

end$
delimiter ;

