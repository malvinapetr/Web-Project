INSERT INTO `user` (`username`, `password`, `type`, `t_score`, `m_score`, `t_tokens`, `m_tokens`,`signup_date`) VALUES
('MrAdmin', '$$Adm1n$$', 'admin', 400,32,600,103,'2021-11-30'),
('MrsUser', '##Us3r##', 'user', 200,5,100,25,'2022-02-02'),
('Random1', '$$Asm1n$$', 'user', 300,6,200,10,'2021-11-30'),
('Random2', '$$Adn1n$$', 'user', 350,5,230,3,'2021-11-30'),
('Random3', '$$Arm1n$$', 'user', 200,7,100,90,'2021-11-30'),
('Random4', '$$Odm1n$$', 'user', 100,8,400,34,'2021-11-30'),
('Random5', '$$Bdm1n$$', 'user', 150,54,302,103,'2021-11-30'),
('Random6', '$$Adm4n$$', 'user', 40,16,40,10,'2021-11-30'),
('Random7', '$$Adm1n#$', 'user', 320,2,450,35,'2021-11-30'),
('Random8', '$$Ad2an$$', 'user', 130,3,32,23,'2021-11-30'),
('Random9', '$$Adm1n2$', 'user', 100,1,45,16,'2021-11-30'),
('Random10', '$$A3m1n$$', 'user', 0,0,0,0,'2021-11-30');


INSERT INTO `products` (`id`, `name`, `category`, `subcategory`) VALUES
(1, 'spaghetti','food','pasta');


INSERT INTO `pois` (`id`, `name`, `address`,`latitude`,`longitude`) VALUES
(1, 'test',34.05, -22.3456789);


INSERT INTO `offers` (`id`, `username`, `p_id`,`lcount`,`dcount`,`price`,`ful_criteria`,`sub_date`,`poi_id`,`stock`,`exp_date`) VALUES
(1, 'MrsUser',7,2,0,2.12,'yes','2021-11-08','980515550','ναι','2021-11-15'),
(2, 'MrsUser',8,2,0,2.12,'no','2021-09-15','980515550','ναι','2021-09-22'),
(3, 'MrsUser',52,2,0,2.12,'no','2021-10-01','980515550','όχι','2021-10-08'),
(4, 'MrsUser',189,2,0,2.12,'yes','2022-11-10','364381224','ναι','2022-11-17'),
(5, 'MrsUser',52,2,0,2.12,'yes','2022-11-10','364381224','ναι','2023-08-29'),
(6, 'MrsUser',52,2,0,0.8,'yes','2022-11-10','364381224','ναι','2023-08-29');


INSERT INTO `images` (`id`, `url`, `p_id`) VALUES
(1,'fix-330ml.jpg',7),
(2,default,8),
(3,default,52);

INSERT INTO `tokens` (`id`, `tokens`) VALUES
(1,default);

INSERT INTO `lows` (`p_id`, `yesterday_low`,`last_week_low`, `temp_last_week_low`) VALUES
(52,1.1,1.2, 1.3);

