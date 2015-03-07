<?php
/*
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Items";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Cartes";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Bots";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Monstres";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Industries";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Zones";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Quêtes";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Buffs";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Compétances";
$wgExtraNamespaces[$wgExtraNamespacesIndex++] = "Type de monstres";
* [[Liste des bots]]
* [[Liste des buffs]]
* [[Liste des crafting]]
* [[Liste des industries]]
* [[Liste des items]]
* [[Liste des cartes]]
* [[Liste des monstres]]
* [[Type des monstres]]
* [[Liste des plantes]]
* [[Liste des quêtes]]
* [[Liste des compétances]]
* [[Débuts]]
*/

$temp_lang='fr';

if(!isset($datapackexplorergeneratorinclude))
    die('abort into translation '.$temp_lang."\n");

$translation_list[$temp_lang]['Bots list']='Liste des bots';
$translation_list[$temp_lang]['Map']='Carte';
$translation_list[$temp_lang]['Quest start']='Départ de quête';
$translation_list[$temp_lang]['Text']='Texte';
$translation_list[$temp_lang]['Shop']='Boutique';
$translation_list[$temp_lang]['Fight']='Combat';
$translation_list[$temp_lang]['Heal']='Soin';
$translation_list[$temp_lang]['Learn']='Apprentissage';
$translation_list[$temp_lang]['Warehouse']='Entrepôt';
$translation_list[$temp_lang]['Market']='Marché';
$translation_list[$temp_lang]['Clan']='Clan';
$translation_list[$temp_lang]['Sell']='Vente';
$translation_list[$temp_lang]['Zone capture']='Capture de zone';
$translation_list[$temp_lang]['Industry']='Industrie';
$translation_list[$temp_lang]['Unknown type']='Type inconnu';
$translation_list[$temp_lang]['Industry [id]']='Industrie [id]';
$translation_list[$temp_lang]['Buffs list']='Liste des buffs';
$translation_list[$temp_lang]['Crafting list']='Liste des crafting';
$translation_list[$temp_lang]['Industries list']='Liste des industries';
$translation_list[$temp_lang]['Items list']='Liste des items';
$translation_list[$temp_lang]['Maps list']='Liste des cartes';
$translation_list[$temp_lang]['Monsters list']='Liste des monstres';
$translation_list[$temp_lang]['Plants list']='Liste des plantes';
$translation_list[$temp_lang]['Quests list']='Liste des quêtes';
$translation_list[$temp_lang]['Skills list']='Liste des compétances';
$translation_list[$temp_lang]['Monsters types']='Type des monstres';
$translation_list[$temp_lang]['Starters']='Débuts';
$translation_list[$temp_lang]['Level [level]']='Niveau [level]';
$translation_list[$temp_lang]['Capture bonus: ']='Bonus de capture: ';
$translation_list[$temp_lang]['This buff is only valid for this fight']='Ce buff est seulement valide pour ce combat';
$translation_list[$temp_lang]['This buff is always valid']='Ce buff est toujours valide';
$translation_list[$temp_lang]['This buff is valid during [turns] turns']='Ce buff est valide pour [turns] tours';
$translation_list[$temp_lang]['The hp change of [hp]']='Les hp change de [hp]';
$translation_list[$temp_lang]['The defense change of [defense]']='La défence change de [defense]';
$translation_list[$temp_lang]['The attack change of [attack]']='L\'attaque change de [attack]';
$translation_list[$temp_lang]['The hp change of <b>[hp]</b> during <b>[turns] steps</b>']='Les hp change de <b>[hp]</b> pendant <b>[turns] pas</b>';
$translation_list[$temp_lang]['The defense change of <b>[defense]</b> during <b>[turns] steps</b>']='La défence change de <b>[defense]</b> pendant <b>[turns] pas</b>';
$translation_list[$temp_lang]['The attack change of <b>[attack]</b> during <b>[turns] steps</b>']='L\'attaque change de <b>[attack]</b> pendant <b>[turns] pas</b>';
$translation_list[$temp_lang]['Monster']='Monstre';
$translation_list[$temp_lang]['Type']='Type';
$translation_list[$temp_lang]['Buff']='Buff';
$translation_list[$temp_lang]['In walk']='En marchant';
$translation_list[$temp_lang]['In fight']='En combat';
$translation_list[$temp_lang]['Item']='Item';
$translation_list[$temp_lang]['Material']='Matériel';
$translation_list[$temp_lang]['Product']='Produit';
$translation_list[$temp_lang]['Price']='Prix';
$translation_list[$temp_lang]['Unknown item']='Item inconnu';
$translation_list[$temp_lang]['Item to learn missing: ']='Item pour apprendre manquant: ';
$translation_list[$temp_lang]['Time to complet a cycle']='Temps pour completé le cycle';
$translation_list[$temp_lang]['s']='s';
$translation_list[$temp_lang]['mins']='mins';
$translation_list[$temp_lang]['hours']='heures';
$translation_list[$temp_lang]['days']='jours';
$translation_list[$temp_lang]['Cycle to be full']='Cycle pour être complet';
$translation_list[$temp_lang]['Resources']='Ressources';
$translation_list[$temp_lang]['Requirements']='Prérequis';
$translation_list[$temp_lang]['Quest:']='Quête:';
$translation_list[$temp_lang]['Bot']='Bot';
$translation_list[$temp_lang]['Map']='Carte';
$translation_list[$temp_lang]['Unknown map']='Carte inconnue';
$translation_list[$temp_lang]['Products']='Produits';
$translation_list[$temp_lang]['Industry']='Industrie';
$translation_list[$temp_lang]['Location']='Emplacement';
$translation_list[$temp_lang]['Description']='Description';
$translation_list[$temp_lang]['Trap']='Piége';
$translation_list[$temp_lang]['Bonus rate:']='Taux de bonus:';
$translation_list[$temp_lang]['Repel']='Rapel';
$translation_list[$temp_lang]['Plant']='Plante';
$translation_list[$temp_lang]['Seed']='Graine';
$translation_list[$temp_lang]['Sprouted']='Germe';
$translation_list[$temp_lang]['Taller']='Buisson';
$translation_list[$temp_lang]['Flowering']='En fleurs';
$translation_list[$temp_lang]['Fruits']='Fruits';
$translation_list[$temp_lang]['Rewards']='Récompense';
$translation_list[$temp_lang]['Less reputation in:']='Moins de réputation en:';
$translation_list[$temp_lang]['More reputation in:']='Plus de réputation en:';
$translation_list[$temp_lang]['Able to create clan']='Autorisé à creer un clan';
$translation_list[$temp_lang]['Allow']='Autorise';
$translation_list[$temp_lang]['Effect']='Effet';
$translation_list[$temp_lang]['Regenerate all the hp']='Régénére tout les hp';
$translation_list[$temp_lang]['Remove all the buff and debuff']='Suppression de tout les buff et debuff';
$translation_list[$temp_lang]['Remove the buff:']='Suppression du buff:';
$translation_list[$temp_lang]['Unknown buff']='Buff inconnu';
$translation_list[$temp_lang]['Do the item']='Faire l\'item';
$translation_list[$temp_lang]['Product by crafting']='Produit par crafting';
$translation_list[$temp_lang]['Used into crafting']='Utilisé en crafting';
$translation_list[$temp_lang]['Evolve from']='Évolue depuis';
$translation_list[$temp_lang]['Evolve with']='Évolue avec';
$translation_list[$temp_lang]['Evolve to']='Évolue vers';
$translation_list[$temp_lang]['Quantity']='Quantité';
$translation_list[$temp_lang]['Luck']='Chance';
$translation_list[$temp_lang]['Quests']='Quêtes';
$translation_list[$temp_lang]['Quantity rewarded']='Quantité récompensés';
$translation_list[$temp_lang]['Resource of the industry']='Ressource de l\'industrie';
$translation_list[$temp_lang]['Product of the industry']='Produit de l\'industrie';
$translation_list[$temp_lang]['Skill']='Compétances';
$translation_list[$temp_lang]['Type']='Type';
$translation_list[$temp_lang]['On the map']='Sur la carte';
$translation_list[$temp_lang]['Can\'t be sold']='Ne peu être vendu';
$translation_list[$temp_lang]['Description de carte']='';
$translation_list[$temp_lang]['Zone']='Zone';
$translation_list[$temp_lang]['Linked locations']='Endroits liés';
$translation_list[$temp_lang]['Border top']='Bordure du dessus';
$translation_list[$temp_lang]['Border bottom']='Bordure du dessous';
$translation_list[$temp_lang]['Border left']='Bordure gauche';
$translation_list[$temp_lang]['Border right']='Bordure droite';
$translation_list[$temp_lang]['Door']='Porte';
$translation_list[$temp_lang]['Teleporter']='Teleporteur';
$translation_list[$temp_lang]['Drop on ']='Déposer sur ';
$translation_list[$temp_lang]['Hidden on the map']='Caché sur la carte';
$translation_list[$temp_lang]['Levels']='Niveaux';
$translation_list[$temp_lang]['Rate']='Taux';
$translation_list[$temp_lang]['Content']='Contenu';
$translation_list[$temp_lang]['Text only']='Texte seulement';
$translation_list[$temp_lang]['Leader']='Leader';
$translation_list[$temp_lang]['Size']='Taille';
$translation_list[$temp_lang]['MB']='Mo';
$translation_list[$temp_lang]['Unknown zone']='Zone inconnue';
$translation_list[$temp_lang]['Gender ratio']='Ratio de genre';
$translation_list[$temp_lang]['Unknown gender']='Genre inconnu';
$translation_list[$temp_lang]['female']='femelle';
$translation_list[$temp_lang]['male']='male';
$translation_list[$temp_lang]['Kind']='Nature';
$translation_list[$temp_lang]['Habitat']='Habitat';
$translation_list[$temp_lang]['Catch rate']='Taux de capture';
$translation_list[$temp_lang]['Steps for hatching']='Pas pour l\'éclosion';
$translation_list[$temp_lang]['Body']='Corps';
$translation_list[$temp_lang]['Stat']='Stat';
$translation_list[$temp_lang]['Height']='Hauteur';
$translation_list[$temp_lang]['Hp']='Hp';
$translation_list[$temp_lang]['width']='largeur';
$translation_list[$temp_lang]['Attack']='Attaque';
$translation_list[$temp_lang]['Defense']='Defense';
$translation_list[$temp_lang]['Special attack']='Attaque spécial';
$translation_list[$temp_lang]['Special defense']='Defense spécial';
$translation_list[$temp_lang]['Speed']='Vitesse';
$translation_list[$temp_lang]['Weak to']='Faible contre';
$translation_list[$temp_lang]['Resistant to']='Résistant contre';
$translation_list[$temp_lang]['Immune to']='Insensible contre';
$translation_list[$temp_lang]['Endurance']='Endurance';
$translation_list[$temp_lang]['At level']='Au niveau';
$translation_list[$temp_lang]['With unknown item']='Avec un item inconnu';
$translation_list[$temp_lang]['After trade']='Aprés échange';
$translation_list[$temp_lang]['Time to grow']='Temps pour croitre';
$translation_list[$temp_lang]['Fruits produced']='Fruits produit';
$translation_list[$temp_lang]['minutes']='minutes';
$translation_list[$temp_lang]['repeatable']='répétable';
$translation_list[$temp_lang]['one time']='une fois';
$translation_list[$temp_lang]['Step']='Étape';
$translation_list[$temp_lang]['Effective against']='Éffectif contre';
$translation_list[$temp_lang]['Not effective against']='Peu éffectif contre';
$translation_list[$temp_lang]['Useless against']='Inutile contre';
$translation_list[$temp_lang]['Level']='Niveau';
$translation_list[$temp_lang]['Skill point (SP) to learn']='Point de compétance (SP) pour apprendre';
$translation_list[$temp_lang]['You can\'t learn this skill']='Vous ne pouvez pas apprendre cet compétance';
$translation_list[$temp_lang]['Add buff:']='Ajouter un buff:';
$translation_list[$temp_lang]['Success']='Succès';
$translation_list[$temp_lang]['Luck:']='Chance:';
$translation_list[$temp_lang]['Skill level']='Niveau de compétance';
$translation_list[$temp_lang]['Number of level']='Nombre de niveau';
$translation_list[$temp_lang]['Skin']='Skin';
$translation_list[$temp_lang]['Cash']='Argent';
$translation_list[$temp_lang]['Items']='Items';
$translation_list[$temp_lang]['Population']='Population';
$translation_list[$temp_lang]['No bots in this zone!']='Pas de bot dans cette zone!';
$translation_list[$temp_lang]['1 bot']='1 bot';
$translation_list[$temp_lang]['bots']='bots';
$translation_list[$temp_lang]['shop(s)']='boutique(s)';
$translation_list[$temp_lang]['bot(s) of fight']='bot(s) de combat';
$translation_list[$temp_lang]['bot(s) of heal']='bot(s) de soin';
$translation_list[$temp_lang]['bot(s) of learn']='bot(s) d\'apprentissage';
$translation_list[$temp_lang]['warehouse(s)']='entrepôt(s)';
$translation_list[$temp_lang]['market(s)']='marché(s)';
$translation_list[$temp_lang]['bot(s) to create clan']='bot(s) pour creer des clans';
$translation_list[$temp_lang]['bot(s) to sell your objects']='bot(s) pour vendre vos objets';
$translation_list[$temp_lang]['bot(s) to capture the zone']='bot(s) pour capturer la zone';
$translation_list[$temp_lang]['industries']='industries';
$translation_list[$temp_lang]['quests to start']='quêtes à démarrer';
$translation_list[$temp_lang]['Quantity needed']='Quantité nécessaire';
$translation_list[$temp_lang]['Life quantity:']='Quantité de vie:';
$translation_list[$temp_lang]['Reputations']='Reputations';
$translation_list[$temp_lang]['Drop luck of [luck]%']='Chance de drop [luck]%';
$translation_list[$temp_lang]['Industry #[industryid]']='Industrie #[industryid]';
$translation_list[$temp_lang]['After <b>[mins]</b> minutes you will have <b>[fruits]</b> fruits']='Après <b>[mins]</b> minutes vous allez avoir <b>[fruits]</b> fruits';
$translation_list[$temp_lang]['with luck of [luck]%']='avec une chance de [luck]%';
$translation_list[$temp_lang]['Cave']='Cave';
$translation_list[$temp_lang]['Water']='Eau';
$translation_list[$temp_lang]['Grass']='Herbe';
$translation_list[$temp_lang]['at night']='de nuit';
$translation_list[$temp_lang][' condition [condition] at [value]']=' condition [condition] à [value]';
$translation_list[$temp_lang]['in']='en';
$translation_list[$temp_lang]['Yes']='Oui';
$translation_list[$temp_lang]['No']='Non';
$translation_list[$temp_lang]['Repeatable']='Répétable';
$translation_list[$temp_lang]['Starting bot']='Bot de départ';

//generated path
$translation_list[$temp_lang]['maps/']='cartes/';
$translation_list[$temp_lang]['industries/']='industries/';
$translation_list[$temp_lang]['quests/']='quetes/';
$translation_list[$temp_lang]['monsters/']='monstres/';
$translation_list[$temp_lang]['items/']='items/';
$translation_list[$temp_lang]['zones/']='zones/';
$translation_list[$temp_lang]['bots/']='bots/';
//pages
$translation_list[$temp_lang]['bots.html']='bots.html';
$translation_list[$temp_lang]['buffs.html']='buffs.html';
$translation_list[$temp_lang]['crafting.html']='crafting.html';
$translation_list[$temp_lang]['industries.html']='industries.html';
$translation_list[$temp_lang]['items.html']='items.html';
$translation_list[$temp_lang]['maps.html']='cartes.html';
$translation_list[$temp_lang]['monsters.html']='monstres.html';
$translation_list[$temp_lang]['plants.html']='plantes.html';
$translation_list[$temp_lang]['quests.html']='quetes.html';
$translation_list[$temp_lang]['skills.html']='competances.html';
$translation_list[$temp_lang]['start.html']='depart.html';
$translation_list[$temp_lang]['types.html']='types.html';
//mediawiki categories
$translation_list[$temp_lang]['Bots:']='Bots:';
$translation_list[$temp_lang]['Items:']='Items:';
$translation_list[$temp_lang]['Maps:']='Cartes:';
$translation_list[$temp_lang]['Monsters:']='Monstres:';
$translation_list[$temp_lang]['Industries:']='Industries:';
$translation_list[$temp_lang]['Zones:']='Zones:';
$translation_list[$temp_lang]['Quests:']='Quêtes:';
$translation_list[$temp_lang]['Buffs:']='Buffs:';
$translation_list[$temp_lang]['Skills:']='Compétances:';
$translation_list[$temp_lang]['Monsters type:']='Type de monstres:';
