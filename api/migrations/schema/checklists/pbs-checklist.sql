-- Prototype Checklists are not referenced from any Camp.
-- Prototype Checklists are copied.
-- Therefore, Prototype Checklists can be deleted and re-inserted

DELETE FROM public.checklist c where c.id = '000100000000';
INSERT INTO public.checklist (createtime, updatetime, id, campid, name, isprototype) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100000000', null, 'PBS Basiskurs Wolfsstufe', true)
;
INSERT INTO public.checklist_item (createtime, updatetime, id, checklistid, parentid, position, text) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100010000', '000100000000', NULL, 1, 'Der Kurs vermittelt den TN die Pfadigrundlagen.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100010001', '000100000000', '000100010000', 1, 'Entwicklungsstand und Bedürfnisse der Kinder der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100010002', '000100000000', '000100010000', 2, 'Wolfsstufensymbolik'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100010003', '000100000000', '000100010000', 3, 'Persönliche Auseinandersetzung mit Gesetz und Versprechen der Roverstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100010004', '000100000000', '000100010000', 4, 'Bezug der Pfadigrundlagen zum Pfadialltag'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100010005', '000100000000', '000100010000', 5, 'Stufenmodell und Abgrenzung zw. Biber-, Wolfs- und Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100010006', '000100000000', '000100010000', 6, 'Ausgestaltung der sieben Pfadimethoden und fünf Pfadibeziehungen auf der Wolfsstufe'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020000', '000100000000', NULL, 2, 'Der Kurs bildet die TN aus, ein Programm für die Wolfsstufe zu planen, durchzuführen und auszuwerten.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020001', '000100000000', '000100020000', 1, 'Einkleidung von Aktivitäten und Quartalsprogrammen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020002', '000100000000', '000100020000', 2, 'Methoden zur Planung, Durchführung und Auswertung von Programmen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020003', '000100000000', '000100020000', 3, 'Quartalsprogramm planen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020004', '000100000000', '000100020000', 4, 'Abenteuer als Alternative zum Quartalsprogramm und als Form der Mitbestimmung auf der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020005', '000100000000', '000100020000', 5, 'wesentliche Punkte beim Organisieren von Weekends'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020006', '000100000000', '000100020000', 6, 'Planen, Durchführen und Auswerten von J+S Aktivitäten für die Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020007', '000100000000', '000100020000', 7, 'Planen, Durchführen und Auswerten von Wanderungen für die Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100020008', '000100000000', '000100020000', 8, 'Inklusive Gestaltung des Programms, damit sich alle Wölfe wohlfühlen und ihre Persönlichkeiten individuell entwickeln können'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030000', '000100000000', NULL, 3, 'Der Kurs bildet die TN zu verantwortungsbewussten Mitgliedern eines Leitungsteams aus.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030001', '000100000000', '000100030000', 1, 'Funktion sowie Rechte und Pflichten als Mitglied eines Leitungsteams der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030002', '000100000000', '000100030000', 2, 'Leitwölfe betreuen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030003', '000100000000', '000100030000', 3, 'Umgang mit Wölfen mit herausforderndem Verhalten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030004', '000100000000', '000100030000', 4, 'Sicherheitskonzepte für sicherheitsrelevante Aktivitäten planen und umsetzen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030005', '000100000000', '000100030000', 5, 'Angebote und Anlaufstellen des Kantonalverbands/ der Region sowie Krisenkonzept'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030006', '000100000000', '000100030000', 6, 'eigene Leiterpersönlichkeit und Rolle im Team'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030007', '000100000000', '000100030000', 7, 'Regeln für konstruktive Gespräche im Leitungsteam'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030008', '000100000000', '000100030000', 8, 'Möglichkeiten der Aus- und Weiterbildung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100030009', '000100000000', '000100030000', 9, 'Sexuelle Ausbeutung und Grenzverletzungen, mögliche heikle Situationen in Aktivitäten und vorbeugende Massnahmen'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100040000', '000100000000', NULL, 4, 'Der Kurs befähigt die TN, Aktivitäten wolfsstufengerecht zu gestalten.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100040001', '000100000000', '000100040000', 1, 'Pfadimethode „Persönlicher Fortschritt fördern": Inhalte der Etappen und Spezialitäten in Aktivitäten der Wolfsstufe einbauen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100040002', '000100000000', '000100040000', 2, 'Arbeiten mit Gesetz und Versprechen auf der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100040003', '000100000000', '000100040000', 3, 'Gestaltung von Lagerfeuern auf der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000100040004', '000100000000', '000100040000', 4, 'Vertiefen der Kenntnisse und stufengerechtes Vermitteln der Wolfsstufentechnik')
;

DELETE FROM public.checklist c where c.id = '000200000000';
INSERT INTO public.checklist (createtime, updatetime, id, campid, name, isprototype) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200000000', null, 'PBS Basiskurs Pfadistufe', true)
;
INSERT INTO public.checklist_item (createtime, updatetime, id, checklistid, parentid, position, text) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200010000', '000200000000', null, 1, 'Der Kurs vermittelt den TN die Pfadigrundlagen.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200010001', '000200000000', '000200010000', 1, 'Entwicklungsstand und Bedürfnisse der Kinder und Jugendlichen der Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200010002', '000200000000', '000200010000', 2, 'Persönliche Auseinandersetzung mit Gesetz und Versprechen der Roverstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200010003', '000200000000', '000200010000', 3, 'Bezug der Pfadigrundlagen zum Pfadialltag'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200010004', '000200000000', '000200010000', 4, 'Stufenmodell und Abgrenzung zw. Wolfs-, Pfadi- und Piostufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200010005', '000200000000', '000200010000', 5, 'Ausgestaltung der sieben Pfadimethoden und fünf Pfadibeziehungen auf der Pfadistufe'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020000', '000200000000', null, 2, 'Der Kurs bildet die TN aus, ein Programm für die Pfadistufe zu planen, durchzuführen und auszuwerten.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020001', '000200000000', '000200020000', 1, 'Einkleidung von Aktivitäten und Quartalsprogrammen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020002', '000200000000', '000200020000', 2, 'Methoden zur Planung, Durchführung und Auswertung von Programmen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020003', '000200000000', '000200020000', 3, 'Quartalsprogramm planen (inkl. Integration von Fähnliaktivitäten in geeigneter Weise)'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020004', '000200000000', '000200020000', 4, 'Projekt als Alternative zum Quartalsprogramm und als Form der Mitbestimmung auf der Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020005', '000200000000', '000200020000', 5, 'wesentliche Punkte beim Organisieren von Weekends'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020006', '000200000000', '000200020000', 6, 'Planen, Durchführen und Auswerten von J+S Aktivitäten für die Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020007', '000200000000', '000200020000', 7, 'Planen, Durchführen und Auswerten von Wanderungen für die Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200020008', '000200000000', '000200020000', 8, 'Inklusive Gestaltung des Programms, damit sich alle Pfadis wohlfühlen und ihre Persönlichkeiten individuell entwickeln können'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030000', '000200000000', null, 3, 'Der Kurs bildet die TN zu verantwortungsbewussten Mitgliedern eines Leitungsteams und zu Betreuenden von Leitpfadis aus.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030001', '000200000000', '000200030000', 1, 'Funktion sowie Rechte und Pflichten als Mitglied eines Leitungsteams der Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030002', '000200000000', '000200030000', 2, 'Rolle der Leitpfadis und ihre Betreuung, insbesondere bei Fähnliaktivitäten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030003', '000200000000', '000200030000', 3, 'Umgang mit Pfadis mit herausforderndem Verhalten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030004', '000200000000', '000200030000', 4, 'Sicherheitskonzepte für sicherheitsrelevante Aktivitäten (inkl. herausfordernde Fähnliaktivitäten) planen und umsetzen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030005', '000200000000', '000200030000', 5, 'Angebote und Anlaufstellen des Kantonalverbands/der Region inkl. Krisenkonzept'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030006', '000200000000', '000200030000', 6, 'eigene Leiterpersönlichkeit und Rolle im Team'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030007', '000200000000', '000200030000', 7, 'Regeln für konstruktive Gespräche im Leitungsteam'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030008', '000200000000', '000200030000', 8, 'Möglichkeiten der Aus- und Weiterbildung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200030009', '000200000000', '000200030000', 9, 'Sexuelle Ausbeutung und Grenzverletzungenund, mögliche heikle Situationen in Aktivitäten und verbeugende Massnahmen'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200040000', '000200000000', null, 4, 'Der Kurs befähigt die TN, Aktivitäten pfadistufengerecht zu gestalten.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200040001', '000200000000', '000200040000', 1, 'Pfadimethode „Persönlicher Fortschritt fördern": Inhalte der Etappen und Spezialitäten in Aktivitäten der Pfadistufen einbauen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200040002', '000200000000', '000200040000', 2, 'Arbeiten mit Gesetz und Versprechen auf der Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200040003', '000200000000', '000200040000', 3, 'Gestaltung von Lagerfeuern auf der Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000200040004', '000200000000', '000200040000', 4, 'Vertiefen der Kenntnisse und stufengerechtes Vermitteln der Pfadistufentechnik'),
;

DELETE FROM public.checklist c where c.id = '000300000000';
INSERT INTO public.checklist (createtime, updatetime, id, campid, name, isprototype) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300000000', null, 'PBS Aufbaukurs Wolfsstufe', true)
;
INSERT INTO public.checklist_item (createtime, updatetime, id, checklistid, parentid, position, text) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300010000', '000300000000', null, 1, 'Der Kurs ermöglicht den TN die Auseinandersetzung mit den Pfadigrundlagen.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300010001', '000300000000', '000300010000', 1, 'Vertiefung über die Bedürfnisse der Kinder der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300010002', '000300000000', '000300010000', 2, 'Wolfsstufensymbolik, ihr Bezug zu den Pfadigrundlagen und ihre Einbindung im Programm'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300010003', '000300000000', '000300010000', 3, 'Pfadigrundlagen als Hilfsmittel zum Sicherstellen der Ausgewogenheit des Programms'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300010004', '000300000000', '000300010000', 4, 'Einbindung der sieben Methoden und fünf Beziehungen in das Programm. als Mittel zur Erreichung der Ziele der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300010005', '000300000000', '000300010000', 5, 'Stille Momente/ Förderung der Beziehung zum Spirituellen auf der Wolfsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300010006', '000300000000', '000300010000', 6, 'Pfadimethode "Persönlichen Fortschritt fördern": Längerfristige Einbindung ins Programm'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020000', '000300000000', null, 2, 'Der Kurs bildet die TN zu verantwortlichen Einheitsleitenden oder Stufenleitenden aus.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020001', '000300000000', '000300020000', 1, 'Funktion sowie Rechte und Pflichten als Einheitsleitende oder Stufenleitende'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020002', '000300000000', '000300020000', 2, 'Organisation der Einheit/ Stufe und längerfristige Planung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020003', '000300000000', '000300020000', 3, 'Pflege von Elternkontakten und Öffenzlichkeitsarbeit'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020004', '000300000000', '000300020000', 4, 'Leiterpersönlichkeit: Auftreten und Vertreten von Anliegen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020005', '000300000000', '000300020000', 5, 'Rolle im kantonalen Krisenkonzept'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020006', '000300000000', '000300020000', 6, 'Umgang mit Kindern mit herausforderndem Verhalten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020007', '000300000000', '000300020000', 7, 'Gesundheitsförderung: psychisches, physisches und soziales Wohlbefinden positiv beeinflussen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020008', '000300000000', '000300020000', 8, 'Suchtthematik: Prävention im Programm der Wolfsstufe und Regeln im Leitungsteam'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020009', '000300000000', '000300020000', 9, 'Sexuelle Ausbeutung und Grenzverletzungen: Verantwortung als Stufenleitende und vorbeugende Massnahmen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300020010', '000300000000', '000300020000', 10, 'Gewalt: verschiedene Formen von Gewalt (u.a. psychische, physische und strukturelle) und Möglichkeiten, diesen vorzubeugen'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030000', '000300000000', null, 3, 'Der Kurs bildet die TN zu verantwortlichen Lagerleitenden aus.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030001', '000300000000', '000300030000', 1, 'Funktion, Rechte und Pflichten als Lagerleiter*in'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030002', '000300000000', '000300030000', 2, 'Funktionen und Aufgaben von Coach und AL, insbesondere bei der Lagerbetreuung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030003', '000300000000', '000300030000', 3, 'Einsetzung des Lagerreglements gezielt für die Planung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030004', '000300000000', '000300030000', 4, 'Ablauf der Lagerplanung und -administration'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030005', '000300000000', '000300030000', 5, 'Gestaltung des Lagerprogramms'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030006', '000300000000', '000300030000', 6, 'Gesundheit im Lager, Umgang mit Krankheiten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030007', '000300000000', '000300030000', 7, 'Sicherheitskonzepte für Lager'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030008', '000300000000', '000300030000', 8, 'sicherheitsrelevante Aktivitäten planen und analysieren, Durchführungsentscheid, Anpassungen während Aktivitäten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030009', '000300000000', '000300030000', 9, 'Anspruchsvolle J+S-Aktivitäten für die Wolfstufe planen, durchführen und auswerten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300030010', '000300000000', '000300030000', 10, 'Wanderungen auf der Wolfsstufe kritisch beurteilen'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300040000', '000300000000', null, 4, 'Der Kurs befähigt die TN, junge Leitende anzuleiten und zu betreuen.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300040001', '000300000000', '000300040000', 1, 'Teamleiter*in sein und Zusammenarbeit im Team'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300040002', '000300000000', '000300040000', 2, 'inklusive Atmosphäre schaffen in einem Leitungsteam'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300040003', '000300000000', '000300040000', 3, 'Sitzungen vorbereiten und leiten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300040004', '000300000000', '000300040000', 4, 'relevante und konstruktive Rückmeldung zu einem Programmteil für die Wolfstufe geben'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000300040005', '000300000000', '000300040000', 5, 'Betreuung und Förderung einzelner Teammitglieder')
;

DELETE FROM public.checklist c where c.id = '000400000000';
INSERT INTO public.checklist (createtime, updatetime, id, campid, name, isprototype) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400000000', null, 'PBS Aufbaukurs Pfadistufe', true)
;
INSERT INTO public.checklist_item (createtime, updatetime, id, checklistid, parentid, position, text) VALUES
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400010000', '000400000000', null, 1, 'Der Kurs ermöglicht den TN die Auseinandersetzung mit den Pfadigrundlagen.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400010001', '000400000000', '000400010000', 1, 'Vertiefung über die Bedürfnisse der Kinder und Judenlichen der Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400010002', '000400000000', '000400010000', 2, 'Pfadigrundlagen als Hilfsmittel zum Sicherstellen der Ausgewogenheit des Programms'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400010003', '000400000000', '000400010000', 3, '…Einbindung der sieben Methoden und fünf Pfadfinderbeziehungen in das Programm. als Mittel zur Erreichung der Ziele der Wölflingsstufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400010004', '000400000000', '000400010000', 4, 'Stille Momente/ Förderung der Beziehung zum Spirituellen auf der Pfadistufe'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400010005', '000400000000', '000400010000', 5, 'Pfadimethode „Persönlichen Fortschritt fördern": Längerfristige Einbindung ins Programm'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020000', '000400000000', null, 2, 'Der Kurs bildet die TN zu verantwortlichen Einheitsleitenden oder Stufenleitenden aus.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020001', '000400000000', '000400020000', 1, 'Funktion sowie Rechte und Pflichten als Einheitsleitende oder Stufenleitende'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020002', '000400000000', '000400020000', 2, 'Organisation der Einheit/ Stufe, insbesondere Betreuung der Leitpfadis, und längerfristige Planung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020003', '000400000000', '000400020000', 3, 'Pflege von Elternkontakten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020004', '000400000000', '000400020000', 4, 'Leiterpersönlichkeit: Auftreten und Vertreten von Anliegen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020005', '000400000000', '000400020000', 5, 'Rolle im kantonalen Krisenkonzept'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020006', '000400000000', '000400020000', 6, 'Gesundheitsförderung: psychisches, physisches und soziales Wohlbefinden positiv beeinflussen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020007', '000400000000', '000400020000', 7, 'Umgang mit Kinder mit herausforderndem Verhalten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020008', '000400000000', '000400020000', 8, 'Suchtthematik: Prävention im Programm der Pfadistufe und Regeln im Leitungsteam'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020009', '000400000000', '000400020000', 9, 'Sexuelle Ausbeutung und Grenzverletzungen: Verantwortung als Stufenleitende und vorbeugende Massnahmen'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400020010', '000400000000', '000400020000', 10, 'Gewalt: verschiedene Formen von Gewalt (u.a. psychische, physische und strukturelle) und Möglichkeiten, diesen vorzubeugen'),

    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030000', '000400000000', null, 3, 'Der Kurs bildet die TN zu verantwortlichen Lagerleitenden aus.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030001', '000400000000', '000400030000', 1, 'Funktion, Rechte und Pflichten als Lagerleiter*in'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030002', '000400000000', '000400030000', 2, 'Funktionen und Aufgaben von Coach und AL, insbesondere bei der Lagerbetreuung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030003', '000400000000', '000400030000', 3, 'Einsetzung des Lagerreglements gezielt für die Planung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030004', '000400000000', '000400030000', 4, 'Ablauf der Lagerplanung und -administration'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030005', '000400000000', '000400030000', 5, 'Fähnliaktivitäten im Lagerprogramm und deren Betreuung'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030006', '000400000000', '000400030000', 6, 'Gestaltung des Lagerprogramms'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030007', '000400000000', '000400030000', 7, 'Gesundheit im Lager, Umgang mit Krankheiten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030008', '000400000000', '000400030000', 8, 'Sicherheitskonzepte für Lager'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030009', '000400000000', '000400030000', 9, 'sicherheitsrelevante Aktivitäten, Durchführungsentscheid, Anpassungen während Aktivitäten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030010', '000400000000', '000400030000', 10, 'Spiele abändern und grössere sportliche Aktivitäten organisieren'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400030011', '000400000000', '000400030000', 11, 'Wanderungen auf der Pfadistufe'),
    
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400040000', '000400000000', null, 4, 'Der Kurs befähigt die TN, junge Leitende anzuleiten und zu betreuen.'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400040001', '000400000000', '000400040000', 1, 'Teamleiter*in sein und Zusammenarbeit im Team'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400040002', '000400000000', '000400040000', 2, 'inklusive Atmosphäre schaffen in einem Leitungsteam'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400040003', '000400000000', '000400040000', 3, 'relevante und konstruktive Rückmeldung zu einem Programmteilgeben'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400040004', '000400000000', '000400040000', 4, 'Sitzungen vorbereiten und leiten'),
    ('2024-09-28 10:00:00', '2024-09-28 10:00:00', '000400040005', '000400000000', '000400040000', 5, 'Betreuung und Förderung einzelner Teammitglieder'),
;



