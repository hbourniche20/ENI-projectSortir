
-- Init villes
select * from ville;
insert into ville(nom, code_postal) values('Nantes', 44000);
insert into ville(nom, code_postal) values('Rennes', 35000);
insert into ville(nom, code_postal) values('Vitré', 35500);
insert into ville(nom, code_postal) values('Nort-sur-Erdre', 44390);
insert into ville(nom, code_postal) values('Saint-Nazaire', 44600);
insert into ville(nom, code_postal) values('Vitry-le-François', 51300);

insert into site(ville_id, nom, rue) values(6, 'Le Kiosque à Pizzas', '37 Fbg Léon Bourgeois');
