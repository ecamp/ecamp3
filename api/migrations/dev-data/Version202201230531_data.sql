INSERT INTO public.content_node (id, slot, "position", instancename, createtime, updatetime, rootid, parentid, contenttypeid, strategy) VALUES
	('cffc0df3cd43', '1', 1, 'dolores', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '63ad36c59b9e', '63ad36c59b9e', '3ef17bd1df72', 'materialnode'),
	('3b00067bdf25', '1', 1, 'materialNode1', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '645000130f8c', '3ef17bd1df72', 'materialnode'),
	('b4c9ff773de8', '1', 1, 'aut', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '63ad36c59b9e', '63ad36c59b9e', '1a0f84e322c8', 'multiselect'),
	('1af3d7b79795', '1', 0, 'multiSelect2', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '813b9a3c5023', '1a0f84e322c8', 'multiselect'),
	('46eaa682e2f5', '1', 0, 'multiSelect1', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '645000130f8c', '1a0f84e322c8', 'multiselect'),
	('07198a60db89', '1', 1, 'velit', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '63ad36c59b9e', '63ad36c59b9e', '4f0c657fecef', 'singletext'),
	('c0a2b448acd0', '1', 1, 'singleText2', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '813b9a3c5023', '4f0c657fecef', 'singletext'),
	('0296c6e13097', '1', 2, 'singleText1', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '645000130f8c', '4f0c657fecef', 'singletext'),
	('c14c722f6573', '1', 3, 'safetyConcept1', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '645000130f8c', '44dcc7493c65', 'singletext'),
	('fdd159859a9f', '1', 1, 'et', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '63ad36c59b9e', '63ad36c59b9e', 'cfccaecd4bad', 'storyboard'),
	('b3d19f327138', '2', 0, 'storyboard1', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '645000130f8c', 'cfccaecd4bad', 'storyboard'),
	('c9b689bac714', '2', 0, 'Storyboard2', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '813b9a3c5023', 'cfccaecd4bad', 'storyboard'),
	('869c7bbcf042', '', 0, 'columnLayout2', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '869c7bbcf042', NULL, 'f17470519474', 'columnlayout'),
	('7956dabc05b1', '2', 0, 'columnLayout2Child', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '869c7bbcf042', '869c7bbcf042', 'f17470519474', 'columnlayout'),
	('cb499c5495f0', '', 0, 'quis', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'cb499c5495f0', NULL, 'f17470519474', 'columnlayout'),
	('75a94bbedf29', '', 0, 'totam', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '75a94bbedf29', NULL, 'f17470519474', 'columnlayout'),
	('6c8c74499260', '', 0, 'laborum', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '6c8c74499260', NULL, 'f17470519474', 'columnlayout'),
	('63ad36c59b9e', '', 0, 'libero', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '63ad36c59b9e', NULL, 'f17470519474', 'columnlayout'),
	('0b8894aba174', '', 0, 'fuga', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '0b8894aba174', NULL, 'f17470519474', 'columnlayout'),
	('c9ebda9090f5', '', 0, 'exercitationem', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'c9ebda9090f5', NULL, 'f17470519474', 'columnlayout'),
	('fb21b1c912fd', '', 0, 'est', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'fb21b1c912fd', NULL, 'f17470519474', 'columnlayout'),
	('645000130f8c', '', 0, 'columnLayout1', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', NULL, 'f17470519474', 'columnlayout'),
	('813b9a3c5023', '2', 1, 'columnLayoutChild1', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', '645000130f8c', 'f17470519474', 'columnlayout'),
	('81d3e8514f06', '', 0, 'nihil', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '81d3e8514f06', NULL, '3ef17bd1df72', 'materialnode');



INSERT INTO public.abstract_content_node_owner (id, createtime, updatetime, rootcontentnodeid, entitytype) VALUES
	('ce9edfa3a000', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '869c7bbcf042', 'category'),
	('546f8f6116b3', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '75a94bbedf29', 'category'),
	('60b7df7ce82a', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '6c8c74499260', 'category'),
	('cf2586caa87c', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '0b8894aba174', 'category'),
	('dbc3fbcc684c', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'fb21b1c912fd', 'category'),
	('32bc5076f8b3', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '81d3e8514f06', 'activity'),
	('7be390586b33', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'cb499c5495f0', 'activity'),
	('c13b91f0e465', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '63ad36c59b9e', 'activity'),
	('d3cfa12d19f2', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'c9ebda9090f5', 'activity'),
	('8f1126e31ddb', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '645000130f8c', 'activity');



INSERT INTO public.profile (id, email, username, firstname, surname, nickname, language, roles, createtime, updatetime) VALUES
	('5e387cad273d', 'test@example.com', 'test-user', 'Robert', 'Baden-Powell', 'Bi-Pi', 'en', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10'),
	('f9f1a2f9af25', 'reichel.zetta@hotmail.com', 'member-user', 'Zora', 'Steuber', 'omnis', 'en', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10'),
	('0870635edda6', 'lane65@yahoo.com', 'guest-user', 'Tremaine', 'Kohler', 'nulla', 'en', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10'),
	('e5433660140b', 'joan.doyle@lynch.net', 'unrelated-user', 'Wanda', 'Koelpin', 'sit', 'en', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10'),
	('22dce794d4e2', 'ivy.mann@hotmail.com', 'inactive-user', 'Pat', 'Fadel', 'velit', 'en', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10'),
	('4cda72af2704', 'rdooley@gmail.com', 'user6invited', 'Karlie', 'Terry', 'et', 'en', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10'),
	('711ad2e96f9f', 'admin@example.com', 'admin', 'Admi', 'Nistrator', 'Administrator', 'de', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10'),
	('d36197370d44', 'evonrueden@hotmail.com', 'fresh-user', 'Clifford', 'Beier', 'sed', 'en', '["ROLE_USER"]', '2022-01-23 16:19:10', '2022-01-23 16:19:10');



INSERT INTO public."user" (id, state, activationkeyhash, password, createtime, updatetime, profileid) VALUES
	('9145944210a7', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '5e387cad273d'),
	('bae69a1c9fcc', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'f9f1a2f9af25'),
	('48f00685a292', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '0870635edda6'),
	('e7b00084dabf', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e5433660140b'),
	('130684395770', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '22dce794d4e2'),
	('e89f74da0089', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '4cda72af2704'),
	('3b41dca5c568', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '711ad2e96f9f'),
	('be69ef467190', 'activated', NULL, '$argon2id$v=19$m=65536,t=4,p=1$/RC8YWMDDXR19wB4or6bBA$Kq5haK2SACQgo4CB7eDUibsD3QXCE32w25ZwhKg1SGw', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'd36197370d44');



INSERT INTO public.camp (id, campprototypeid, isprototype, name, title, motto, addressname, addressstreet, addresszipcode, addresscity, createtime, updatetime, creatorid, ownerid) VALUES
	('05ce4b9836e9', NULL, false, 'Camp1', 'rem', 'Voluptas fuga totam reiciendis.', 'qui', '15513 Schuster Mountain Apt. 742
Lake Kavonside, HI 50824', '79668', 'Beattychester', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'bae69a1c9fcc', '9145944210a7'),
	('75a8d09937a1', NULL, false, 'Camp2', 'non', 'Velit reiciendis aperiam et fuga.', 'doloribus', '572 Ernser Turnpike Apt. 547
Stephonburgh, CO 11949-0473', '52479', 'North Theoberg', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e7b00084dabf', '3b41dca5c568'),
	('6430aecc5422', NULL, false, 'CampUnrelated', 'vero', 'Officia id corporis incidunt saepe provident esse hic eligendi.', 'quos', '57654 Ondricka Trace Suite 792
Port Asiaton, MN 44798-0182', '10529', 'New Kaylinfort', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '3b41dca5c568', '3b41dca5c568'),
	('e5027d852487', NULL, true, 'CampPrototype', 'est', 'Rerum ut et enim ex eveniet facere sunt.', 'quia', '4498 Pete Cape
South Halleton, MD 75869', '10089-1808', 'New Zelmaberg', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '3b41dca5c568', '3b41dca5c568');



INSERT INTO public.category (id, categoryprototypeid, short, name, color, numberingstyle, campid) VALUES
	('ce9edfa3a000', NULL, 'LA', 'Lageraktivität', '#FF9800', '1', '05ce4b9836e9'),
	('546f8f6116b3', NULL, 'LP', 'Lagerprogramm', '#99CCFF', '1', '05ce4b9836e9'),
	('60b7df7ce82a', NULL, 'LS', 'Lagersport', '#4CAF50', '1', '75a8d09937a1'),
	('cf2586caa87c', NULL, 'LS', 'Lagersport', '#4CAF50', '1', '6430aecc5422'),
	('dbc3fbcc684c', NULL, 'LS', 'Lagersport', '#4CAF50', '1', 'e5027d852487');



INSERT INTO public.activity (id, title, location, campid, categoryid) VALUES
	('32bc5076f8b3', 'voluptates', 'Optio quos qui illo error.', '75a8d09937a1', '60b7df7ce82a'),
	('7be390586b33', 'Activity 2', 'Ducimus aperiam nesciunt est quia.', '05ce4b9836e9', '546f8f6116b3'),
	('c13b91f0e465', 'nisi', 'Quidem ut sunt et quidem est accusamus aut nemo.', '6430aecc5422', 'cf2586caa87c'),
	('d3cfa12d19f2', 'iure', 'Cum culpa rem aut rerum.', 'e5027d852487', 'dbc3fbcc684c'),
	('8f1126e31ddb', 'Activity 1', 'Quia ipsum voluptatibus est accusantium eveniet.', '05ce4b9836e9', 'ce9edfa3a000');



INSERT INTO public.camp_collaboration (id, inviteemail, invitekey, status, role, collaborationacceptedby, createtime, updatetime, userid, campid) VALUES
	('f08468783b7a', NULL, NULL, 'established', 'manager', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '9145944210a7', '05ce4b9836e9'),
	('b2ef8a3b319f', NULL, NULL, 'established', 'member', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'bae69a1c9fcc', '05ce4b9836e9'),
	('490f4b599034', NULL, NULL, 'established', 'guest', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '48f00685a292', '05ce4b9836e9'),
	('83f4171a5c12', 'e.mail@test.com', 'myInviteKey', 'invited', 'member', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', NULL, '05ce4b9836e9'),
	('c43d5c10b01d', NULL, NULL, 'inactive', 'manager', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '130684395770', '05ce4b9836e9'),
	('dde24aaaa193', NULL, NULL, 'established', 'manager', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '9145944210a7', '75a8d09937a1'),
	('13e399474698', NULL, NULL, 'established', 'member', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'bae69a1c9fcc', '75a8d09937a1'),
	('25f863efcd74', NULL, NULL, 'established', 'guest', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '48f00685a292', '75a8d09937a1'),
	('237abf0bd057', 'e.mail2@test.com', 'myInviteKey2', 'invited', 'member', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', NULL, '6430aecc5422'),
	('0e524d43e799', NULL, NULL, 'established', 'manager', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e7b00084dabf', '6430aecc5422'),
	('14d23ae4014e', 'rdooley@gmail.com', 'myInviteKeyCamp2', 'invited', 'member', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e89f74da0089', '75a8d09937a1'),
	('2c313fa367b3', NULL, NULL, 'established', 'manager', NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '3b41dca5c568', 'e5027d852487');



INSERT INTO public.activity_responsible (id, createtime, updatetime, activityid, campcollaborationid) VALUES
	('b4b496570cd5', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '8f1126e31ddb', 'f08468783b7a'),
	('02527639ad4e', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '7be390586b33', 'b2ef8a3b319f'),
	('c2e84a852a64', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'c13b91f0e465', '0e524d43e799'),
	('4df913eee4de', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'd3cfa12d19f2', '2c313fa367b3');



INSERT INTO public.category_contenttype (category_id, contenttype_id) VALUES
	('ce9edfa3a000', '44dcc7493c65');



INSERT INTO public.content_node_columnlayout (id, columns) VALUES
	('869c7bbcf042', '[{"slot":"1","width":12}]'),
	('7956dabc05b1', '[{"slot":"1","width":12}]'),
	('cb499c5495f0', '[{"slot":"1","width":12}]'),
	('75a94bbedf29', '[{"slot":"1","width":12}]'),
	('6c8c74499260', '[{"slot":"1","width":12}]'),
	('63ad36c59b9e', '[{"slot":"1","width":12}]'),
	('0b8894aba174', '[{"slot":"1","width":12}]'),
	('c9ebda9090f5', '[{"slot":"1","width":12}]'),
	('fb21b1c912fd', '[{"slot":"1","width":12}]'),
	('645000130f8c', '[{"slot":"1","width":6},{"slot":"2","width":6}]'),
	('813b9a3c5023', '[{"slot":"1","width":7},{"slot":"2","width":5}]');



INSERT INTO public.content_node_materialnode (id) VALUES
	('81d3e8514f06'),
	('cffc0df3cd43'),
	('3b00067bdf25');



INSERT INTO public.content_node_multiselect (id) VALUES
	('b4c9ff773de8'),
	('1af3d7b79795'),
	('46eaa682e2f5');



INSERT INTO public.content_node_multiselect_option (id, translatekey, checked, createtime, updatetime, "position", multiselectid) VALUES
	('e2ec000afa3b', 'atque', true, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 0, '46eaa682e2f5'),
	('5f20d4143f66', 'possimus', true, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 0, '1af3d7b79795'),
	('e89811f0ef28', 'aut', true, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 0, 'b4c9ff773de8');



INSERT INTO public.content_node_singletext (id, text) VALUES
	('07198a60db89', 'qui'),
	('c0a2b448acd0', 'suscipit'),
	('0296c6e13097', 'a'),
	('c14c722f6573', 'Laudantium quibusdam enim nostrum soluta qui ipsam.');



INSERT INTO public.content_node_storyboard (id) VALUES
	('fdd159859a9f'),
	('b3d19f327138'),
	('c9b689bac714');



INSERT INTO public.content_node_storyboard_section (id, column1, column2, column3, createtime, updatetime, "position", storyboardid) VALUES
	('457d6751f57d', 'assumenda', 'minima', 'sunt', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 0, 'b3d19f327138'),
	('de17d0c13b3a', 'qui', 'similique', 'ut', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 0, 'c9b689bac714'),
	('d3d4ba4ca59b', 'culpa', 'natus', 'consequatur', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 0, 'fdd159859a9f');



INSERT INTO public.period (id, description, start, "end", createtime, updatetime, campid) VALUES
	('e8c03e4285cb', 'Hauptlager', '2023-05-01', '2023-05-03', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '05ce4b9836e9'),
	('303205cf227c', 'Vorabend', '2023-04-15', '2023-04-15', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '05ce4b9836e9'),
	('ac5028de9a55', 'Vorweekend', '2023-11-10', '2023-11-10', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '75a8d09937a1'),
	('ff755e56fe79', 'Hauptlager', '2024-02-20', '2024-02-20', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '6430aecc5422'),
	('a12a9154b43b', 'Hauptlager', '2021-01-01', '2021-01-01', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e5027d852487');



INSERT INTO public.day (id, dayoffset, createtime, updatetime, periodid) VALUES
	('ad21560b27b3', 0, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e8c03e4285cb'),
	('65d73042d34e', 1, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e8c03e4285cb'),
	('f036e9302f65', 2, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e8c03e4285cb'),
	('81c68b92b294', 0, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '303205cf227c'),
	('99ffa4cc913b', 0, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'ac5028de9a55'),
	('9a23375e49ba', 0, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'ff755e56fe79'),
	('c163dc9c0862', 0, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'a12a9154b43b');



INSERT INTO public.day_responsible (id, createtime, updatetime, dayid, campcollaborationid) VALUES
	('5c321135d5e9', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'ad21560b27b3', 'f08468783b7a'),
	('b0364210ca51', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '65d73042d34e', 'f08468783b7a'),
	('78d5d2ec7036', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '9a23375e49ba', '0e524d43e799'),
	('f3a25527bb83', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'c163dc9c0862', '2c313fa367b3');



INSERT INTO public.material_list (id, materiallistprototypeid, name, createtime, updatetime, campid) VALUES
	('628a609902c2', NULL, 'Baumarkt', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '05ce4b9836e9'),
	('bea5c374e5f5', NULL, 'Packliste', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '05ce4b9836e9'),
	('7ed532335465', NULL, 'Baumarkt', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '75a8d09937a1'),
	('9869949f344f', NULL, 'Einkaufsliste', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '6430aecc5422'),
	('d26923cc560d', NULL, 'Einkaufsliste', '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e5027d852487');



INSERT INTO public.material_item (id, article, quantity, unit, createtime, updatetime, materiallistid, periodid, materialnodeid) VALUES
	('fc92a177b0b9', 'Mehl', 1, 'kg', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '628a609902c2', NULL, '3b00067bdf25'),
	('77b5956a8212', 'Blachen', 3, 'Bünde', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '628a609902c2', 'e8c03e4285cb', NULL),
	('ab2a1e58fc15', 'Zucker', 1, 'kg', '2022-01-23 16:19:10', '2022-01-23 16:19:10', '7ed532335465', 'ac5028de9a55', NULL),
	('419b8e7addb9', 'Lagerapotheke', 1, NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', '9869949f344f', 'ff755e56fe79', NULL),
	('e91ec866d090', 'Lagerapotheke', 1, NULL, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'd26923cc560d', 'a12a9154b43b', NULL);



INSERT INTO public.schedule_entry (id, periodoffset, length, "left", width, createtime, updatetime, periodid, activityid) VALUES
	('42342c948b76', 540, 60, 0, 1, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'ac5028de9a55', '32bc5076f8b3'),
	('49334ec99fc4', 600, 60, 0, 1, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'ff755e56fe79', 'c13b91f0e465'),
	('2dbeb6095bd8', 660, 60, 0, 1, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'a12a9154b43b', 'd3cfa12d19f2'),
	('54c8d6a938f2', 720, 60, 0, 1, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'a12a9154b43b', 'd3cfa12d19f2'),
	('542bed6a6b03', 480, 60, 0, 1, '2022-01-23 16:19:10', '2022-01-23 16:19:10', 'e8c03e4285cb', '8f1126e31ddb');



