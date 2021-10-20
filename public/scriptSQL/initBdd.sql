-- Quelques données pour la db

-- Init villes
insert into ville(nom, code_postal) values('Nantes', 44000);
insert into ville(nom, code_postal) values('Rennes', 35000);
insert into ville(nom, code_postal) values('Vitré', 35500);
insert into ville(nom, code_postal) values('Nort-sur-Erdre', 44390);
insert into ville(nom, code_postal) values('Saint-Nazaire', 44600);
insert into ville(nom, code_postal) values('Vitry-le-François', 51300);

-- Init Sites
insert into site(ville_id, nom, rue) values(1, 'Café des plantes', '24 Bd Stalingrad');
insert into site(ville_id, nom, rue) values(1, 'Crêperie du Vieux Quimper', '10 Rue de la Baclerie');
insert into site(ville_id, nom, rue) values(1, 'FNAC', 'Pl. du Commerce');
insert into site(ville_id, nom, rue) values(2, 'Institut Mirabelle', '24 Rue Poullain Duparc');
insert into site(ville_id, nom, rue) values(2, 'Restaurant BèJe', '2 Bd de la Tour d''Auvergne');
insert into site(ville_id, nom, rue) values(2, 'Hôtel de Police', '22 Bd de la Tour d''Auvergne');
insert into site(ville_id, nom, rue) values(3, 'La Taverne Vitré Table de Caractère', '9 Pl. du Général de Gaulle');
insert into site(ville_id, nom, rue) values(3, 'Etrier Vitréen', '2 All. des Cavaliers');
insert into site(ville_id, nom, rue) values(4, 'Chez vapo', '19 Rue du Général Leclerc');
insert into site(ville_id, nom, rue) values(4, 'Le Bretagne Restaurant LORIN', '41 Rue Aristide Briand');
insert into site(ville_id, nom, rue) values(6, 'Le Kiosque à Pizzas', '37 Fbg Léon Bourgeois');
insert into site(ville_id, nom, rue) values(6, 'McDonalds', 'Rn Avenue Du Génral De Gaulle');

-- Init User password pour tous les utilisateurs est "password"
INSERT INTO user(id, ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (0, 1, 'email@inconnu.inc', 'Inconnu', 'Inconnu','Inconnu','0000000000','[]','');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (1, 'pseu@gmail.com', 'Pseu', 'Valentin','Moi','0698653278','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (1, 'dop@gmail.com', 'Dop', 'Hugo','Toi','0678451298','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (2, 'ryu@free.fr', 'Ryu', 'Ryan','Lui','0696857432','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (2, 'mario@gmail.com', 'Mario', 'Antoine','Eux','0636251498','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (3, 'peach@gmail.com', 'Peach', 'Océane','Elle','0651545758','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (4, 'doomguy@gmx.fr', 'DoomGuy', 'Audrey','Autre','0653565952','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (5, 'rincevent@gmail.com', 'RinceVent', 'Aurore','Nom','0645953512','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');
INSERT INTO user(ville_id, email, pseudo, prenom, nom, tel, roles, password) VALUES (6, 'sonic@gmail.com', 'Sonic', 'Claire','Jesaispas','0687324516','[]','$2y$13$fPxr.WdTTNoVzV9nIPlOquD/tXNcdbJsfVsgKIWYCKXjIkFR.6bm6');

-- Init Sorties
insert into sortie(ville_organisatrice_id, ville_accueil_id, organisateur_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) values (1, 1, 1, 2, 'Dejeuner à la crêperie', current_timestamp, current_date, 6, 120, 'Déjeuné à la Crêperie du Vieux Qumpier. C''est Didier qui régale' );
insert into sortie(ville_organisatrice_id, ville_accueil_id, organisateur_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) values (3, 1, 5, 1, 'Café du matin', current_timestamp, current_date, 12, 60, 'Café au parc, détente et baignade');
insert into sortie(ville_organisatrice_id, ville_accueil_id, organisateur_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) values (2, 3, 4, 1, 'Détour par la Taverne', current_timestamp, current_date, 8, 80, 'Petite bière à la taverne de la gare');
insert into sortie(ville_organisatrice_id, ville_accueil_id, organisateur_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) values (1, 2, 2, 1, 'Formation de défense', current_timestamp, current_date, 24, 360, 'Entrainement de self defense auprès de la police de Rennes');
insert into sortie(ville_organisatrice_id, ville_accueil_id, organisateur_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) values (5, 6, 7, 12, 'Anniversaire de Bertrand', current_timestamp, current_date, 12, 150, 'Anniversaire de Bertrand au MacDonald de Vitry-le-François, seul MacDonald qui a accepté recevoir Bertrand pour ses 52 ans');
insert into sortie(ville_organisatrice_id, ville_accueil_id, organisateur_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) values (5, 1, 7, 3, 'Un peu de culture', current_timestamp, current_date, 8, 60, 'Petit tour à la FNAC rayon livre, ON EST PAS LA POUR ACHETER DES JEUX-VIDEOS');
insert into sortie(ville_organisatrice_id, ville_accueil_id, organisateur_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) values (4, 1, 6, 3, 'Pause video-games', current_timestamp, current_date, 15, 90, 'Petite pause aux jeux-vidéals');
