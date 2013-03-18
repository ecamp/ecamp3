
INSERT INTO `media` 
(`name`) 
VALUES
('mobile'),
('print'),
('web')
;



INSERT INTO `camp_types` 
(`id`,`created_at`,`updated_at`,`name`,`type`)
VALUES 
('71558608', NOW( ), NOW( ), 'J+S Kindersport', 'kindersport'),
('00764960', NOW( ), NOW( ), 'J+S Jugendsport', 'jugendsport'),
('59526376', NOW( ), NOW( ), 'J+S Ausbildung', 'ausbildung')
;



INSERT INTO `event_types` 
(`id` ,`created_at` ,`updated_at` ,`name` ,`defaultColor` ,`defaultNumberingStyle` ,`campType_id`)
VALUES 
('98686904', NOW( ), NOW( ), 'Lagersport',  '00ff00',  '1',  '71558608'),
('76473604', NOW( ), NOW( ), 'Lageraktivität',  'ff0000',  'a',  '71558608'),
('63982270', NOW( ), NOW( ), 'Sonstige',  '0000ff',  'I',  '71558608'),

('87445036', NOW( ), NOW( ), 'Lagersport',  '00ff00',  '1',  '00764960'),
('92449710', NOW( ), NOW( ), 'Lageraktivität',  'ff0000',  'a',  '00764960'),
('78976194', NOW( ), NOW( ), 'Sonstige',  '0000ff',  'I',  '00764960'),

('66211292', NOW( ), NOW( ), 'Sonstige',  '0000ff',  'I',  '59526376'),
('00445058', NOW( ), NOW( ), 'Ausbildung',  'ff0000',  '1',  '59526376')
;



INSERT INTO `event_prototypes` 
(`id`, `created_at`, `updated_at`, `name`, `active`) 
VALUES
('70978687', NOW( ), NOW( ), 'Sportblock', 1),
('41185680', NOW( ), NOW( ), 'Wanderung', 1),
('32939798', NOW( ), NOW( ), 'Aktivität', 1),
('11133262', NOW( ), NOW( ), 'Ausbildung', 1)
;



INSERT INTO  `allowed_event_prototypes` 
(`eventtype_id`, `eventprototype_id`)
VALUES 
('98686904',  '70978687'),
('98686904',  '41185680'),
('87445036',  '70978687'),
('87445036',  '41185680'),
('00445058',  '11133262'),
('76473604',  '32939798'),
('92449710',  '32939798')
;
