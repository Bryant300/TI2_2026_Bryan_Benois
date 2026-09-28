# TI2 - Livre d'or

Projet de formation de Bryan en PHP procédural, PDO, MariaDB, HTML, CSS et JavaScript. La structure modèle/vue et le style violet d'origine sont conservés.

## Installation locale
1. Démarrer MariaDB dans WampServer. Vérifier son port (3307 est le réglage d'exemple).
2. Importer `data/ti2web2026.sql` dans MariaDB. Il crée une base distincte `ti2_portfolio`, sans supprimer de base ni de table. Il ne migre pas une table existante incompatible.
3. Copier `config.php.ini` en `config.php`, puis adapter l'hôte, le port, l'utilisateur et le mot de passe. Ne jamais publier le vrai fichier de configuration.
4. Utiliser PHP 8 ou plus avec `pdo_mysql` et `mbstring`.
5. Depuis le dossier du projet, lancer :
```powershell
.\lancer-local.ps1
```
6. Ouvrir http://127.0.0.1:8082/.

Le lanceur active les extensions disponibles pour ce processus seulement, sans modifier php.ini. Il crée une copie de configuration si elle manque. Avec un hôte virtuel WampServer, exposer uniquement `public/` et activer les extensions dans le PHP d'Apache.

Le compte root sans mot de passe est uniquement un exemple pour un environnement local WampServer. Pour un hébergement, utiliser un compte dédié à la base et des identifiants privés.

## Fonctionnalités
Ajout d'un message, validation PHP et JavaScript, affichage échappé, pagination de trois messages, compteur et bouton de thème. Les noms, l'email, le mobile belge, le code postal et le message sont requis selon le fonctionnement d'origine.
Limite harmonisée : message de 10 à 300 caractères, noms de 2 à 100 caractères.

## Corrections apportées
Confirmation conservée après redirection ; données de formulaire non textuelles refusées ; longueurs bornées ; téléphone normalisé ; token CSRF ; pagination stable et résistante aux paramètres incorrects ; erreur de connexion sans détail sensible ; erreurs de lecture signalées au lieu d'afficher une liste vide.
Les consignes initiales du cours sont conservées dans `data/CONSIGNES-ORIGINALES.md`.

## Vérification
Enregistrement et lecture réellement testés avec MariaDB 11.5.2 dans une instance de test isolée. Tests : cas valide, doublon au rafraîchissement évité par redirection, pagination, caractères accentués, texte contenant du HTML, champs invalides et CSRF absent/invalide.
Les données de test ne sont pas incluses dans le script de création.

## Limites
Pas d'espace administrateur, d'antispam avancé ou de suppression des messages ajouté. Ce sont des évolutions distinctes. L'import crée la structure, pas des comptes ni un jeu de données réel. GitHub Pages ne peut pas exécuter PHP/MariaDB.
