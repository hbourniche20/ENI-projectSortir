-- Init Inconnu
INSERT INTO ville(id, nom, code_postal) values(0, 'Inconnu', 0);

-- User
INSERT INTO user(id, ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (0, 0, 'email@inconnu.inc', 'Inconnu', 'Inconnu','Inconnu','0000000000','[]','');

INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (1, 'pseu@gmail.com', 'Pseu', 'Valentin','Moi','0698653278','[]','password');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (1, 'dop@gmail.com', 'Dop', 'Hugo','Toi','0678451298','[]','password');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (2, 'ryu@free.fr', 'Ryu', 'Ryan','Lui','0696857432','[]','password');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (2, 'mario@gmail.com', 'Mario', 'Antoine','Eux','0636251498','[]','password');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (3, 'peach@gmail.com', 'Peach', 'Océane','Elle','0651545758','[]','password');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (4, 'doomguy@gmx.fr', 'DoomGuy', 'Audrey','Autre','0653565952','[]','password');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (5, 'rincevent@gmail.com', 'RinceVent', 'Aurore','Nom','0645953512','[]','password');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (6, 'sonic@gmail.com', 'Sonic', 'Claire','Jesaispas','0687324516','[]','password');