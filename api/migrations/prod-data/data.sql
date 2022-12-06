



INSERT INTO public.profile (id, email, firstname, surname, nickname, language, roles, createtime, updatetime, googleid, pbsmidataid, cevidbid, untrustedemail, untrustedemailkeyhash) VALUES
	('f052d5a73a27', 'support@ecamp3.ch', 'Support', 'Support', 'Support', 'de', '["ROLE_USER", "ROLE_ADMIN"]', '2022-10-08 20:11:57', '2022-10-08 20:38:22', NULL, NULL, NULL, NULL, NULL);



INSERT INTO public."user" (id, state, activationkeyhash, password, createtime, updatetime, profileid, passwordresetkeyhash) VALUES
	('8adf80011c2b', 'activated', NULL, '$2y$13$totwtuXrORYCHS83/wI5yeLq9hOE0Jtm3R9l7TGFml/6sqYNOe0nq', '2022-10-08 20:11:57', '2022-10-08 21:12:54', 'f052d5a73a27', NULL);



INSERT INTO public.camp (id, campprototypeid, isprototype, name, title, motto, addressname, addressstreet, addresszipcode, addresscity, createtime, updatetime, creatorid, ownerid) VALUES
	('75b3572a338e', NULL, true, 'J+S Lager', 'Jugend und Sport', '', NULL, NULL, NULL, NULL, '2022-10-08 20:13:50', '2022-10-08 20:13:50', '8adf80011c2b', '8adf80011c2b'),
	('497f974e7d5d', NULL, true, 'Camp J+S', 'Jeunesse+Sport', '', NULL, NULL, NULL, NULL, '2022-10-08 20:41:53', '2022-10-08 20:41:53', '8adf80011c2b', '8adf80011c2b'),
	('f92fe1cd1ae9', NULL, true, 'Basic', 'Basic', 'Basic', NULL, NULL, NULL, NULL, '2022-10-08 20:49:03', '2022-10-08 20:49:03', '8adf80011c2b', '8adf80011c2b');



INSERT INTO public.content_node (id, slot, "position", instancename, createtime, updatetime, rootid, parentid, contenttypeid, strategy, data) VALUES
	('5f578b4a7fae', NULL, 0, NULL, '2022-10-08 20:15:22', '2022-10-08 20:15:22', '5f578b4a7fae', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('195c74ab5a69', NULL, 0, NULL, '2022-10-08 20:16:14', '2022-10-08 20:16:14', '195c74ab5a69', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('41bcdd2d75d2', NULL, 0, NULL, '2022-10-08 20:16:51', '2022-10-08 20:16:51', '41bcdd2d75d2', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('7b41e3aff0e5', NULL, 0, NULL, '2022-10-08 20:17:17', '2022-10-08 20:17:17', '7b41e3aff0e5', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('6403c48b0a2e', NULL, 0, NULL, '2022-10-08 20:43:44', '2022-10-08 20:43:44', '6403c48b0a2e', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('c29aa1fbad8f', NULL, 0, NULL, '2022-10-08 20:44:40', '2022-10-08 20:44:40', 'c29aa1fbad8f', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('33d8cac7c219', NULL, 0, NULL, '2022-10-08 20:45:16', '2022-10-08 20:45:16', '33d8cac7c219', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('a5d09f5460c3', NULL, 0, NULL, '2022-10-08 20:45:55', '2022-10-08 20:45:55', 'a5d09f5460c3', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('714413ea63dc', NULL, 0, NULL, '2022-10-08 20:49:57', '2022-10-08 20:49:57', '714413ea63dc', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('12d742a2041b', NULL, 0, NULL, '2022-10-08 20:50:30', '2022-10-08 20:50:30', '12d742a2041b', NULL, 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 12}]}'),
	('26c1a70471ed', '1', 0, NULL, '2022-10-08 20:51:56', '2022-10-08 20:51:56', '7b41e3aff0e5', '7b41e3aff0e5', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('f71814486ee6', '1', 1, NULL, '2022-10-08 20:51:59', '2022-10-08 20:51:59', '7b41e3aff0e5', '7b41e3aff0e5', '3ef17bd1df72', 'materialnode', NULL),
	('c4b6a125d19c', '1', 0, NULL, '2022-10-08 20:52:13', '2022-10-08 20:52:17', '41bcdd2d75d2', '41bcdd2d75d2', 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 8}, {"slot": "2", "width": 4}]}'),
	('6050f8d01c5a', '1', 0, NULL, '2022-10-08 20:52:24', '2022-10-08 20:52:24', '41bcdd2d75d2', 'c4b6a125d19c', 'cfccaecd4bad', 'storyboard', '{"sections": {"9816258f-de1f-405c-9475-fc669f443485": {"column1": "", "column2": "", "column3": "", "position": 0}}}'),
	('97dffe215773', '1', 1, NULL, '2022-10-08 20:52:29', '2022-10-08 20:52:29', '41bcdd2d75d2', 'c4b6a125d19c', '3ef17bd1df72', 'materialnode', NULL),
	('e49621c83f1a', '2', 0, NULL, '2022-10-08 20:52:46', '2022-10-08 20:52:46', '41bcdd2d75d2', 'c4b6a125d19c', '318e064ea0c9', 'singletext', '{"text": ""}'),
	('7f69c34d30d5', '2', 1, NULL, '2022-10-08 20:52:49', '2022-10-08 20:52:49', '41bcdd2d75d2', 'c4b6a125d19c', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('097d8186c97a', '1', 0, NULL, '2022-10-08 20:53:03', '2022-10-08 20:53:08', '195c74ab5a69', '195c74ab5a69', 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 8}, {"slot": "2", "width": 4}]}'),
	('44e70e042bd0', '1', 0, NULL, '2022-10-08 20:53:13', '2022-10-08 20:53:13', '195c74ab5a69', '097d8186c97a', 'cfccaecd4bad', 'storyboard', '{"sections": {"abd17cbf-f874-4888-8859-0f0a4f93bafa": {"column1": "", "column2": "", "column3": "", "position": 0}}}'),
	('73f38f452b2a', '1', 1, NULL, '2022-10-08 20:53:16', '2022-10-08 20:53:16', '195c74ab5a69', '097d8186c97a', '3ef17bd1df72', 'materialnode', NULL),
	('8ffd086f910a', '1', 1, NULL, '2022-10-08 20:55:13', '2022-10-08 20:55:13', '6403c48b0a2e', '6403c48b0a2e', '3ef17bd1df72', 'materialnode', NULL),
	('42bd640cc0e8', '2', 0, NULL, '2022-10-08 20:53:34', '2022-10-08 20:53:53', '195c74ab5a69', '097d8186c97a', '318e064ea0c9', 'singletext', '{"text": ""}'),
	('f1f12096fca6', '2', 2, NULL, '2022-10-08 20:53:20', '2022-10-08 20:53:20', '195c74ab5a69', '097d8186c97a', '1a0f84e322c8', 'multiselect', '{"options": {"security": {"checked": false}, "outdoorTechnique": {"checked": false}, "pioneeringTechnique": {"checked": false}, "natureAndEnvironment": {"checked": false}, "campsiteAndSurroundings": {"checked": false}, "preventionAndIntegration": {"checked": false}}}'),
	('a3c948021c7e', '2', 1, NULL, '2022-10-08 20:53:50', '2022-10-08 20:53:54', '195c74ab5a69', '097d8186c97a', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('3f53f44dd400', '1', 0, NULL, '2022-10-08 20:54:03', '2022-10-08 20:54:07', '5f578b4a7fae', '5f578b4a7fae', 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 8}, {"slot": "2", "width": 4}]}'),
	('537ff0b1bc85', '1', 0, NULL, '2022-10-08 20:54:14', '2022-10-08 20:54:14', '5f578b4a7fae', '3f53f44dd400', 'cfccaecd4bad', 'storyboard', '{"sections": {"1fc47265-b725-4676-8b21-e8c06b5493da": {"column1": "", "column2": "", "column3": "", "position": 0}}}'),
	('038230547b9b', '1', 1, NULL, '2022-10-08 20:54:17', '2022-10-08 20:54:17', '5f578b4a7fae', '3f53f44dd400', '3ef17bd1df72', 'materialnode', NULL),
	('f9adb6d2af7f', '2', 0, NULL, '2022-10-08 20:54:23', '2022-10-08 20:54:23', '5f578b4a7fae', '3f53f44dd400', '318e064ea0c9', 'singletext', '{"text": ""}'),
	('04bd98b65b99', '2', 1, NULL, '2022-10-08 20:54:26', '2022-10-08 20:54:26', '5f578b4a7fae', '3f53f44dd400', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('8db20e389dd5', '2', 2, NULL, '2022-10-08 20:54:28', '2022-10-08 20:54:28', '5f578b4a7fae', '3f53f44dd400', '44dcc7493c65', 'singletext', '{"text": ""}'),
	('d3029d81a65b', '1', 0, NULL, '2022-10-08 20:55:11', '2022-10-08 20:55:11', '6403c48b0a2e', '6403c48b0a2e', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('11f8cbe334fd', '1', 0, NULL, '2022-10-08 20:55:35', '2022-10-08 20:55:40', 'a5d09f5460c3', 'a5d09f5460c3', 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 8}, {"slot": "2", "width": 4}]}'),
	('b05722ecf7a9', '1', 0, NULL, '2022-10-08 20:55:45', '2022-10-08 20:55:45', 'a5d09f5460c3', '11f8cbe334fd', 'cfccaecd4bad', 'storyboard', '{"sections": {"3588eaa1-b0a2-4f74-a07b-b8c413052680": {"column1": "", "column2": "", "column3": "", "position": 0}}}'),
	('92e65cf79542', '1', 1, NULL, '2022-10-08 20:55:48', '2022-10-08 20:55:48', 'a5d09f5460c3', '11f8cbe334fd', '3ef17bd1df72', 'materialnode', NULL),
	('23c1ebcebebc', '2', 0, NULL, '2022-10-08 20:55:51', '2022-10-08 20:55:51', 'a5d09f5460c3', '11f8cbe334fd', '318e064ea0c9', 'singletext', '{"text": ""}'),
	('52b485757f3e', '2', 1, NULL, '2022-10-08 20:55:55', '2022-10-08 20:55:55', 'a5d09f5460c3', '11f8cbe334fd', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('ba9af13ca7de', '1', 0, NULL, '2022-10-08 20:56:03', '2022-10-08 20:56:07', '33d8cac7c219', '33d8cac7c219', 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 8}, {"slot": "2", "width": 4}]}'),
	('58d624bd2be5', '1', 0, NULL, '2022-10-08 20:56:11', '2022-10-08 20:56:11', '33d8cac7c219', 'ba9af13ca7de', 'cfccaecd4bad', 'storyboard', '{"sections": {"9a32f52c-cc2f-42fd-911d-01d6d8d3200a": {"column1": "", "column2": "", "column3": "", "position": 0}}}'),
	('e98963ac0f5c', '1', 1, NULL, '2022-10-08 20:56:16', '2022-10-08 20:56:16', '33d8cac7c219', 'ba9af13ca7de', '3ef17bd1df72', 'materialnode', NULL),
	('48e5f4a29d1a', '2', 0, NULL, '2022-10-08 20:56:20', '2022-10-08 20:56:20', '33d8cac7c219', 'ba9af13ca7de', '318e064ea0c9', 'singletext', '{"text": ""}'),
	('094304d6e653', '2', 1, NULL, '2022-10-08 20:56:22', '2022-10-08 20:56:22', '33d8cac7c219', 'ba9af13ca7de', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('fc9c93c5ca52', '2', 2, NULL, '2022-10-08 20:56:25', '2022-10-08 20:56:25', '33d8cac7c219', 'ba9af13ca7de', '1a0f84e322c8', 'multiselect', '{"options": {"security": {"checked": false}, "outdoorTechnique": {"checked": false}, "pioneeringTechnique": {"checked": false}, "natureAndEnvironment": {"checked": false}, "campsiteAndSurroundings": {"checked": false}, "preventionAndIntegration": {"checked": false}}}'),
	('ff1c66fdcbd8', '1', 0, NULL, '2022-10-08 20:56:30', '2022-10-08 20:56:34', 'c29aa1fbad8f', 'c29aa1fbad8f', 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 8}, {"slot": "2", "width": 4}]}'),
	('3954b2e1735b', '1', 0, NULL, '2022-10-08 20:56:38', '2022-10-08 20:56:38', 'c29aa1fbad8f', 'ff1c66fdcbd8', 'cfccaecd4bad', 'storyboard', '{"sections": {"c03a17c2-6860-4938-80ea-ce4bada1f5ee": {"column1": "", "column2": "", "column3": "", "position": 0}}}'),
	('c87bf71d32b0', '1', 1, NULL, '2022-10-08 20:56:41', '2022-10-08 20:56:41', 'c29aa1fbad8f', 'ff1c66fdcbd8', '3ef17bd1df72', 'materialnode', NULL),
	('834f7834700f', '2', 0, NULL, '2022-10-08 20:56:44', '2022-10-08 20:56:44', 'c29aa1fbad8f', 'ff1c66fdcbd8', '318e064ea0c9', 'singletext', '{"text": ""}'),
	('f28bbb91a515', '2', 1, NULL, '2022-10-08 20:56:47', '2022-10-08 20:56:47', 'c29aa1fbad8f', 'ff1c66fdcbd8', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('eb16854efd88', '2', 2, NULL, '2022-10-08 20:56:51', '2022-10-08 20:56:51', 'c29aa1fbad8f', 'ff1c66fdcbd8', '44dcc7493c65', 'singletext', '{"text": ""}'),
	('68109ac3018f', '1', 0, NULL, '2022-10-08 20:57:19', '2022-10-08 20:57:22', '12d742a2041b', '12d742a2041b', 'f17470519474', 'columnlayout', '{"columns": [{"slot": "1", "width": 8}, {"slot": "2", "width": 4}]}'),
	('15ec9dc82bf1', '1', 0, NULL, '2022-10-08 20:57:25', '2022-10-08 20:57:25', '12d742a2041b', '68109ac3018f', 'cfccaecd4bad', 'storyboard', '{"sections": {"15c7ef17-a372-4086-980c-8d55357c7307": {"column1": "", "column2": "", "column3": "", "position": 0}}}'),
	('da24d1e5f128', '1', 1, NULL, '2022-10-08 20:57:27', '2022-10-08 20:57:27', '12d742a2041b', '68109ac3018f', '3ef17bd1df72', 'materialnode', NULL),
	('3d8fedce90a4', '2', 0, NULL, '2022-10-08 20:57:31', '2022-10-08 20:57:31', '12d742a2041b', '68109ac3018f', '318e064ea0c9', 'singletext', '{"text": ""}'),
	('48c9ff0d436b', '2', 1, NULL, '2022-10-08 20:57:34', '2022-10-08 20:57:34', '12d742a2041b', '68109ac3018f', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('a1e289ed40b9', '1', 0, NULL, '2022-10-08 20:57:47', '2022-10-08 20:57:47', '714413ea63dc', '714413ea63dc', '4f0c657fecef', 'singletext', '{"text": ""}'),
	('3a4d46d9fa60', '1', 1, NULL, '2022-10-08 20:57:50', '2022-10-08 20:57:50', '714413ea63dc', '714413ea63dc', '3ef17bd1df72', 'materialnode', NULL);



INSERT INTO public.category (id, categoryprototypeid, short, name, color, numberingstyle, campid, rootcontentnodeid, createtime, updatetime) VALUES
	('9adf1b7753eb', NULL, 'LS', 'Lagersport', '#BE2D00', '1', '75b3572a338e', '5f578b4a7fae', '2022-10-08 20:15:22', '2022-10-08 20:15:22'),
	('9a8a15bf5b9d', NULL, 'LP', 'Lagerprogramm', '#2090FF', '1', '75b3572a338e', '41bcdd2d75d2', '2022-10-08 20:16:51', '2022-10-08 20:16:51'),
	('a0af9dd993d9', NULL, 'ES', 'Essen', '#A3A3A3', '1', '75b3572a338e', '7b41e3aff0e5', '2022-10-08 20:17:17', '2022-10-08 20:30:49'),
	('3236c849e99d', NULL, 'R', 'Repas', '#A3A3A3', '1', '497f974e7d5d', '6403c48b0a2e', '2022-10-08 20:43:44', '2022-10-08 20:43:44'),
	('37a96aafb9f1', NULL, 'SC', 'Sport de camp', '#BE2D00', '1', '497f974e7d5d', 'c29aa1fbad8f', '2022-10-08 20:44:40', '2022-10-08 20:44:40'),
	('b85ba544838b', NULL, 'AC', 'Activités de camp', '#10A010', '1', '497f974e7d5d', '33d8cac7c219', '2022-10-08 20:45:16', '2022-10-08 20:45:16'),
	('b41d284199c2', NULL, 'LA', 'Lageraktivität', '#10A010', '1', '75b3572a338e', '195c74ab5a69', '2022-10-08 20:16:14', '2022-10-08 20:45:18'),
	('6e1cc2c9d44d', NULL, 'PC', 'Programme de camp', '#2090FF', '1', '497f974e7d5d', 'a5d09f5460c3', '2022-10-08 20:45:55', '2022-10-08 20:45:55'),
	('38a5dbcf9d5a', NULL, 'ES', 'Essen', '#A3A3A3', '1', 'f92fe1cd1ae9', '714413ea63dc', '2022-10-08 20:49:57', '2022-10-08 20:49:57'),
	('e308f0eb1099', NULL, 'PR', 'Programm', '#2090FF', '1', 'f92fe1cd1ae9', '12d742a2041b', '2022-10-08 20:50:30', '2022-10-08 20:50:30');






INSERT INTO public.camp_collaboration (id, inviteemail, invitekeyhash, status, role, collaborationacceptedby, createtime, updatetime, userid, campid) VALUES
	('71c07d3be9f3', NULL, NULL, 'established', 'manager', NULL, '2022-10-08 20:13:50', '2022-10-08 20:13:50', '8adf80011c2b', '75b3572a338e'),
	('77b7a7e51080', NULL, NULL, 'established', 'manager', NULL, '2022-10-08 20:41:53', '2022-10-08 20:41:53', '8adf80011c2b', '497f974e7d5d'),
	('1c7985ab4c7a', NULL, NULL, 'established', 'manager', NULL, '2022-10-08 20:49:03', '2022-10-08 20:49:03', '8adf80011c2b', 'f92fe1cd1ae9');






INSERT INTO public.category_contenttype (category_id, contenttype_id) VALUES
	('9adf1b7753eb', '44dcc7493c65'),
	('9adf1b7753eb', '318e064ea0c9'),
	('9adf1b7753eb', '4f0c657fecef'),
	('9adf1b7753eb', 'f17470519474'),
	('9adf1b7753eb', 'cfccaecd4bad'),
	('9adf1b7753eb', '3ef17bd1df72'),
	('b41d284199c2', '318e064ea0c9'),
	('b41d284199c2', '4f0c657fecef'),
	('b41d284199c2', 'f17470519474'),
	('b41d284199c2', 'cfccaecd4bad'),
	('b41d284199c2', '3ef17bd1df72'),
	('b41d284199c2', '1a0f84e322c8'),
	('9a8a15bf5b9d', '318e064ea0c9'),
	('9a8a15bf5b9d', '4f0c657fecef'),
	('9a8a15bf5b9d', 'f17470519474'),
	('9a8a15bf5b9d', 'cfccaecd4bad'),
	('9a8a15bf5b9d', '3ef17bd1df72'),
	('a0af9dd993d9', '4f0c657fecef'),
	('a0af9dd993d9', '3ef17bd1df72'),
	('3236c849e99d', '3ef17bd1df72'),
	('3236c849e99d', '4f0c657fecef'),
	('37a96aafb9f1', '44dcc7493c65'),
	('37a96aafb9f1', '318e064ea0c9'),
	('37a96aafb9f1', '4f0c657fecef'),
	('37a96aafb9f1', 'f17470519474'),
	('37a96aafb9f1', 'cfccaecd4bad'),
	('37a96aafb9f1', '3ef17bd1df72'),
	('b85ba544838b', '318e064ea0c9'),
	('b85ba544838b', '4f0c657fecef'),
	('b85ba544838b', 'f17470519474'),
	('b85ba544838b', 'cfccaecd4bad'),
	('b85ba544838b', '3ef17bd1df72'),
	('b85ba544838b', '1a0f84e322c8'),
	('6e1cc2c9d44d', '318e064ea0c9'),
	('6e1cc2c9d44d', '4f0c657fecef'),
	('6e1cc2c9d44d', 'f17470519474'),
	('6e1cc2c9d44d', 'cfccaecd4bad'),
	('6e1cc2c9d44d', '3ef17bd1df72'),
	('e308f0eb1099', '318e064ea0c9'),
	('e308f0eb1099', '4f0c657fecef'),
	('e308f0eb1099', 'f17470519474'),
	('e308f0eb1099', 'cfccaecd4bad'),
	('e308f0eb1099', '3ef17bd1df72');



INSERT INTO public.period (id, description, start, "end", createtime, updatetime, campid) VALUES
	('302bd2df1442', 'Hauptlager', '2023-01-10', '2023-01-14', '2022-10-08 20:13:50', '2022-10-08 20:40:00', '75b3572a338e'),
	('151cb15935b7', 'Camp principal', '2023-01-10', '2023-01-14', '2022-10-08 20:41:53', '2022-10-08 20:41:53', '497f974e7d5d'),
	('d2ca29989215', 'Hauptlager', '2023-01-10', '2023-01-14', '2022-10-08 20:49:03', '2022-10-08 20:49:03', 'f92fe1cd1ae9');



INSERT INTO public.day (id, dayoffset, createtime, updatetime, periodid) VALUES
	('2256ac84b529', 0, '2022-10-08 20:13:50', '2022-10-08 20:13:50', '302bd2df1442'),
	('3f6cba2ee106', 1, '2022-10-08 20:13:50', '2022-10-08 20:13:50', '302bd2df1442'),
	('216e719622ca', 2, '2022-10-08 20:13:50', '2022-10-08 20:13:50', '302bd2df1442'),
	('cef86517f2ef', 3, '2022-10-08 20:40:00', '2022-10-08 20:40:00', '302bd2df1442'),
	('743e00281ccc', 4, '2022-10-08 20:40:00', '2022-10-08 20:40:00', '302bd2df1442'),
	('dad24da6f891', 0, '2022-10-08 20:41:53', '2022-10-08 20:41:53', '151cb15935b7'),
	('672b6ee5c996', 1, '2022-10-08 20:41:53', '2022-10-08 20:41:53', '151cb15935b7'),
	('bc7e94bc3b39', 2, '2022-10-08 20:41:53', '2022-10-08 20:41:53', '151cb15935b7'),
	('c1abd3c08b9e', 3, '2022-10-08 20:41:53', '2022-10-08 20:41:53', '151cb15935b7'),
	('e101c3a86d17', 4, '2022-10-08 20:41:53', '2022-10-08 20:41:53', '151cb15935b7'),
	('05ee069dcf59', 0, '2022-10-08 20:49:03', '2022-10-08 20:49:03', 'd2ca29989215'),
	('b8ce6647f552', 1, '2022-10-08 20:49:03', '2022-10-08 20:49:03', 'd2ca29989215'),
	('e5a713e7ee8e', 2, '2022-10-08 20:49:03', '2022-10-08 20:49:03', 'd2ca29989215'),
	('8013278f5618', 3, '2022-10-08 20:49:03', '2022-10-08 20:49:03', 'd2ca29989215'),
	('8a207f0b2afa', 4, '2022-10-08 20:49:03', '2022-10-08 20:49:03', 'd2ca29989215');






INSERT INTO public.material_list (id, materiallistprototypeid, name, createtime, updatetime, campid, campcollaborationid) VALUES
	('a1f4fbc719ec', NULL, 'Lebensmittel', '2022-10-08 20:30:58', '2022-10-08 20:30:58', '75b3572a338e', NULL),
	('a1e7ba6c21f2', NULL, 'J+S Material', '2022-10-08 20:31:10', '2022-10-08 20:31:10', '75b3572a338e', NULL),
	('c58d5153bc00', NULL, 'Baumarkt', '2022-10-08 20:31:14', '2022-10-08 20:31:14', '75b3572a338e', NULL),
	('9da9b0d1fc59', NULL, 'Produits alimentaires', '2022-10-08 20:46:57', '2022-10-08 20:46:57', '497f974e7d5d', NULL),
	('33243d77a3d3', NULL, 'Matériel de J+S', '2022-10-08 20:47:27', '2022-10-08 20:47:27', '497f974e7d5d', NULL),
	('0f15a8d26976', NULL, 'Marché de la construction', '2022-10-08 20:47:40', '2022-10-08 20:47:40', '497f974e7d5d', NULL),
	('547e41c893d5', NULL, 'Detailhandel', '2022-10-08 20:51:04', '2022-10-08 20:51:04', 'f92fe1cd1ae9', NULL),
	('46ac7f708d13', NULL, 'Baumarkt', '2022-10-08 20:51:09', '2022-10-08 20:51:09', 'f92fe1cd1ae9', NULL);












