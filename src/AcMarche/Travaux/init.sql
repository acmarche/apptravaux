
INSERT INTO `batiment` (`id`, `slugname`, `intitule`, `created`, `updated`) VALUES
(3,  'ecoles', 'Ecoles', '2014-12-17 09:44:31', '2014-12-17 09:44:31'),
(4,  'administratif', 'Administratif', '2014-12-17 09:44:38', '2014-12-17 09:44:38'),
(5,  'creches', 'Crêches', '2014-12-17 09:44:46', '2014-12-17 09:44:46'),
(6,  'sports', 'Sports', '2014-12-17 09:44:53', '2014-12-17 09:44:53'),
(7,  'eglises', 'Eglises', '2014-12-17 09:45:00', '2014-12-17 09:45:00'),
(8,  'divers', 'Divers', '2014-12-17 09:45:07', '2014-12-17 09:45:07'),
(9,  'ecole-de-on', 'Ecole de On', '2014-12-17 15:57:32', '2014-12-17 15:57:32'),
(10, 'ecole-de-hargimont', 'Ecole de Hargimont', '2014-12-17 15:57:48', '2014-12-17 15:57:48'),
(11,  'ecole-de-waha', 'Ecole de Waha', '2014-12-17 15:57:58', '2014-12-17 15:57:58'),
(12,  'ecole-de-hollogne', 'Ecole de Hollogne', '2014-12-17 15:58:12', '2014-12-17 15:58:12'),
(13,  'ecole-de-aye', 'Ecole de Aye', '2014-12-17 15:58:24', '2014-12-17 15:58:24'),
(14,  'ecole-de-humain', 'Ecole de Humain', '2014-12-17 15:58:33', '2014-12-17 15:58:33'),
(15,  'creche-des-zoulous', 'Crèche des Zoulous', '2014-12-17 15:58:44', '2014-12-17 15:58:44'),
(16,  'creche-de-marloie', 'Crèche de Marloie', '2014-12-17 15:58:53', '2014-12-17 15:58:53'),
(17,  'creche-de-aye', 'Crèche de Aye', '2014-12-17 15:59:03', '2014-12-17 15:59:03'),
(18,  'halte-accueil', 'Halte accueil', '2014-12-17 15:59:10', '2014-12-17 15:59:10'),
(19,  'complexe-saint-francois', 'Complexe Saint-François', '2014-12-17 15:59:22', '2014-12-17 15:59:22'),
(20,  'co-accueil-de-on', 'Co-accueil de On', '2014-12-17 15:59:33', '2014-12-17 15:59:33');

INSERT INTO `categorie` (`id`, `slugname`, `intitule`, `created`, `updated`) VALUES
(3, 'intervention', 'Intervention', '2014-12-17 09:44:05', '2014-12-17 09:44:05'),
(4, 'prevention', 'Prévention', '2014-12-17 09:44:14', '2014-12-17 09:44:14');

INSERT INTO `domaine` (`id`, `slugname`, `intitule`, `created`, `updated`) VALUES
(4,  'batiment', 'Bâtiment', '2014-12-17 09:42:08', '2014-12-17 09:42:08'),
(5,  'voirie', 'Voirie', '2014-12-17 09:42:17', '2014-12-17 09:42:17'),
(6,  'cadre_de_vie', 'Cadre de vie', '2014-12-17 09:42:26', '2014-12-17 09:42:26'),
(7,  'parc_et_jardin', 'Parc et jardin', '2014-12-17 09:42:36', '2014-12-17 09:42:36'),
(8,  'maintenance', 'Maintenance', '2014-12-17 09:42:43', '2014-12-17 09:42:43'),
(9,  'festivites', 'Festivités', '2014-12-17 09:42:51', '2014-12-17 09:42:51'),
(10, 'cimetieres', 'Cimetières', '2014-12-17 09:42:58', '2014-12-17 09:42:58'),
(11,  'foret', 'Forêt', '2014-12-17 09:43:06', '2014-12-17 09:43:06'),
(12,  'divers', 'Divers', '2014-12-17 09:43:15', '2014-12-17 09:43:15');

INSERT INTO `service` (`id`, `slugname`, `intitule`, `created`, `updated`) VALUES
(1,  'enseignement', 'Enseignement', '2014-12-17 16:13:59', '2014-12-17 16:13:59'),
(2,  'espace-parents-enfants', 'Espace Parents Enfants', '2014-12-17 16:14:46', '2014-12-17 16:14:46'),
(3,  'creches', 'Crèches', '2014-12-17 16:14:56', '2014-12-17 16:14:56'),
(4,  'coccinelles', 'Coccinelles', '2014-12-17 16:15:11', '2014-12-17 16:15:11'),
(5,  'ecole-des-devoirs', 'Ecole des devoirs', '2014-12-17 16:15:25', '2014-12-17 16:15:25');

INSERT INTO `quartier` (`id`, `slugname`, `nom`) VALUES
(1, 'la-campagnette', 'La Campagnette'),
(2, 'la-fourche', 'La Fourche'),
(3, 'famennoise', 'Famennoise'),
(4, 'cresse-de-lorichamps', 'Cresse de Lorichamps'),
(5, 'wex', 'Wex'),
(7, 'lotissement-tp', 'Lotissement TP'),
(8, 'cite-militaire', 'Cité militaire');

INSERT INTO `village` (`id`, `slugname`, `nom`, `couleur`, `icone`, `created`, `updated`) VALUES
(1, 'aye', 'Aye', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(12, 'champlon', 'Champlon', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(13, 'grimbiemont', 'Grimbiémont', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(14, 'hargimont', 'Hargimont', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(15, 'hollogne', 'Hollogne', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(16, 'humain', 'Humain', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(17, 'lignieres', 'Lignières', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(18, 'marche', 'Marche', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(19, 'marloie', 'Marloie', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(20, 'on', 'On', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(21, 'roy', 'Roy', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(22, 'verdenne', 'Verdenne', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(23, 'waha', 'Waha', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00');

INSERT INTO `etat` (`id`, `intitule`, `couleur`, `icone`, `created`, `updated`) VALUES
(1, 'Nouveau', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(2, 'En cours', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(3, 'En attente', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(4, 'Clôturé', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00');

INSERT INTO `priorite` (`id`, `intitule`, `couleur`, `icone`, `created`, `updated`) VALUES
(1, 'Normal', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(2, 'Basse', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00'),
(3, 'Haute', NULL, NULL, '2017-06-14 10:00:00', '2017-06-14 10:00:00');