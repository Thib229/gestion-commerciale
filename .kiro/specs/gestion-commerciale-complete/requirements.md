# Document de Exigences — Gestion Commerciale Complète

## Introduction

Ce document décrit les exigences fonctionnelles manquantes pour compléter le logiciel SaaS de gestion commerciale destiné au marché béninois. Le système existant (Laravel 12, MySQL, Tailwind CSS, Alpine.js) gère déjà les clients, produits, factures, paiements et abonnements FedaPay. Les fonctionnalités ci-dessous viennent compléter ce socle pour offrir une solution professionnelle et sécurisée.

---

## Glossaire

- **System** : L'application SaaS de gestion commerciale
- **User** : Utilisateur authentifié (propriétaire d'un compte)
- **Client** : Client commercial d'un User
- **Facture** : Document de facturation lié à un User et un Client
- **Paiement** : Règlement partiel ou total d'une Facture
- **Profil_Entreprise** : Informations légales et visuelles d'une entreprise associée à un User
- **PDF_Generator** : Composant de génération de documents PDF
- **Lien_Public** : URL partageable sans authentification pour consulter une Facture
- **Historique_Abonnement** : Enregistrement des transactions d'abonnement FedaPay
- **Activity_Log** : Journal des actions effectuées par les utilisateurs
- **Email_Service** : Composant d'envoi d'emails transactionnels
- **Paginator** : Composant de pagination des listes
- **Search_Filter** : Composant de recherche et filtrage des listes
- **Plan** : Niveau d'abonnement (Basique, Pro, Premium)
- **Numéro_Facture** : Identifiant séquentiel unique par User au format FAC-AAAA-XXXX

---

## Exigences

### Exigence 1 : Numérotation automatique des factures

**User Story :** En tant qu'utilisateur, je veux que chaque facture reçoive automatiquement un numéro séquentiel unique, afin de respecter les obligations légales de facturation et de faciliter le suivi.

#### Critères d'acceptation

1. WHEN une Facture est créée, THE System SHALL générer un Numéro_Facture au format `FAC-AAAA-XXXX` où `AAAA` est l'année courante et `XXXX` est un compteur à 4 chiffres remis à zéro chaque année, par User.
2. THE System SHALL garantir l'unicité du Numéro_Facture par User et par année.
3. WHEN deux Factures sont créées simultanément par le même User, THE System SHALL attribuer des Numéro_Facture distincts sans collision.
4. THE System SHALL stocker le Numéro_Facture dans la table `factures` comme champ non modifiable après création.

---

### Exigence 2 : Statut automatique des factures

**User Story :** En tant qu'utilisateur, je veux voir le statut de chaque facture (payée, partiellement payée, impayée) mis à jour automatiquement, afin de connaître instantanément l'état de mes créances.

#### Critères d'acceptation

1. THE System SHALL calculer le statut d'une Facture selon les règles suivantes : `impayée` si le montant payé est 0, `partiellement payée` si le montant payé est supérieur à 0 et inférieur au total, `payée` si le montant payé est égal au total.
2. WHEN un Paiement est enregistré ou supprimé, THE System SHALL recalculer et mettre à jour le statut de la Facture associée.
3. THE System SHALL afficher le statut de chaque Facture dans la liste des factures avec un indicateur visuel distinct pour chaque état.
4. THE System SHALL stocker le statut calculé dans la table `factures` pour permettre le filtrage en base de données.

---

### Exigence 3 : Profil entreprise

**User Story :** En tant qu'utilisateur, je veux renseigner les informations de mon entreprise (nom, logo, adresse, téléphone, email, numéro fiscal), afin qu'elles apparaissent sur mes documents PDF et pages publiques.

#### Critères d'acceptation

1. THE System SHALL permettre à chaque User de créer et modifier un Profil_Entreprise contenant : nom de l'entreprise, logo (image), adresse, téléphone, email professionnel, numéro fiscal (IFU).
2. WHEN un User soumet le formulaire de Profil_Entreprise avec des données valides, THE System SHALL sauvegarder les informations et confirmer la mise à jour.
3. IF le champ logo est fourni, THEN THE System SHALL valider que le fichier est une image (jpeg, png, webp) d'une taille maximale de 2 Mo et le stocker dans le système de fichiers.
4. THE System SHALL associer un seul Profil_Entreprise par User.
5. WHERE le Profil_Entreprise est incomplet, THE System SHALL afficher un avertissement invitant le User à compléter son profil avant de générer un PDF.

---

### Exigence 4 : Template PDF professionnel

**User Story :** En tant qu'utilisateur Pro ou Premium, je veux générer des factures PDF professionnelles avec le logo et les informations de mon entreprise, afin d'envoyer des documents crédibles à mes clients.

#### Critères d'acceptation

1. WHEN un User avec `pdf_enabled = true` demande le PDF d'une Facture, THE PDF_Generator SHALL produire un document incluant : logo et nom de l'entreprise, adresse et contacts du Profil_Entreprise, numéro fiscal (IFU), Numéro_Facture, date de la facture, informations du Client, tableau des produits avec quantité et prix unitaire, total HT, montant payé, reste à régler, statut de la Facture, et les conditions de paiement configurées.
2. IF le User n'a pas `pdf_enabled`, THEN THE System SHALL retourner une réponse HTTP 403 avec un message indiquant que cette fonctionnalité nécessite un abonnement Pro ou Premium.
3. THE PDF_Generator SHALL produire un fichier PDF valide téléchargeable par le navigateur.
4. WHERE le Profil_Entreprise ne contient pas de logo, THE PDF_Generator SHALL générer le PDF sans espace réservé au logo.

---

### Exigence 5 : Page de facture publique partageable

**User Story :** En tant qu'utilisateur, je veux partager un lien de facture avec mon client sans qu'il ait besoin de se connecter, afin de faciliter la consultation et le suivi des paiements.

#### Critères d'acceptation

1. THE System SHALL générer un token unique et non devinable pour chaque Facture lors de sa création.
2. WHEN un visiteur accède à l'URL publique `/factures/public/{token}`, THE System SHALL afficher les informations de la Facture (Numéro_Facture, date, produits, total, statut, informations du Profil_Entreprise) sans requérir d'authentification.
3. IF le token fourni ne correspond à aucune Facture, THEN THE System SHALL retourner une page d'erreur HTTP 404.
4. THE System SHALL afficher le lien public copiable depuis la page de détail de la Facture pour les utilisateurs authentifiés.
5. THE System SHALL ne pas exposer d'informations sensibles sur d'autres factures ou clients via la page publique.

---

### Exigence 6 : Historique des paiements d'abonnement

**User Story :** En tant qu'utilisateur, je veux consulter l'historique de mes paiements d'abonnement, afin de suivre mes dépenses et obtenir des justificatifs.

#### Critères d'acceptation

1. THE System SHALL enregistrer chaque transaction d'abonnement FedaPay dans une table `subscription_payments` avec : montant, devise, statut de la transaction, référence FedaPay, plan souscrit, et date.
2. WHEN un User accède à la page d'historique d'abonnement, THE System SHALL afficher la liste paginée de ses transactions d'abonnement, triée par date décroissante.
3. THE System SHALL afficher le statut de chaque transaction (réussie, échouée, en attente) avec un indicateur visuel distinct.
4. WHEN un paiement d'abonnement FedaPay est confirmé par webhook, THE System SHALL enregistrer la transaction dans `subscription_payments`.

---

### Exigence 7 : Pagination sur toutes les listes

**User Story :** En tant qu'utilisateur, je veux que toutes les listes (clients, produits, factures, paiements) soient paginées, afin de naviguer efficacement même avec un grand volume de données.

#### Critères d'acceptation

1. THE Paginator SHALL afficher les listes de Clients, Produits, Factures et Paiements par pages de 15 éléments par défaut.
2. WHEN un User navigue vers une page spécifique, THE Paginator SHALL afficher les éléments correspondants à cette page sans recharger l'intégralité des données.
3. THE Paginator SHALL afficher le nombre total d'éléments et la page courante.
4. WHILE des filtres de recherche sont actifs, THE Paginator SHALL conserver les paramètres de filtrage lors du changement de page.

---

### Exigence 8 : Recherche et filtres

**User Story :** En tant qu'utilisateur, je veux pouvoir rechercher et filtrer mes données (par nom client, date, statut), afin de retrouver rapidement les informations dont j'ai besoin.

#### Critères d'acceptation

1. THE Search_Filter SHALL permettre de rechercher les Clients par nom ou email avec une correspondance partielle (LIKE).
2. THE Search_Filter SHALL permettre de filtrer les Factures par : nom du Client associé, plage de dates (date début / date fin), et statut (impayée, partiellement payée, payée).
3. THE Search_Filter SHALL permettre de rechercher les Produits par nom.
4. WHEN un User soumet un filtre, THE Search_Filter SHALL retourner les résultats filtrés paginés sans recharger la page entière.
5. THE Search_Filter SHALL permettre de réinitialiser tous les filtres actifs en un seul clic.

---

### Exigence 9 : Emails transactionnels

**User Story :** En tant qu'utilisateur, je veux recevoir des emails de confirmation lors de la création d'une facture et de l'enregistrement d'un paiement, afin d'avoir une trace écrite de mes opérations.

#### Critères d'acceptation

1. WHEN une Facture est créée, THE Email_Service SHALL envoyer un email de confirmation au User contenant le Numéro_Facture, le nom du Client, le total et la date.
2. WHEN un Paiement est enregistré sur une Facture, THE Email_Service SHALL envoyer un email de notification au User contenant le montant reçu, le Numéro_Facture, le nouveau statut de la Facture et le reste à régler.
3. IF l'envoi d'email échoue, THEN THE Email_Service SHALL logger l'erreur sans interrompre le flux principal de l'application.
4. THE Email_Service SHALL envoyer les emails de manière asynchrone via une queue Laravel pour ne pas bloquer la réponse HTTP.

---

### Exigence 10 : Logs d'activité

**User Story :** En tant qu'utilisateur Premium, je veux consulter un journal des actions effectuées dans mon compte, afin de surveiller l'activité et détecter toute utilisation non autorisée.

#### Critères d'acceptation

1. THE Activity_Log SHALL enregistrer les actions suivantes avec l'identité du User, la date/heure, l'action et l'entité concernée : création/modification/suppression de Client, Facture, Produit, Paiement, et modification du Profil_Entreprise.
2. WHEN un User Premium accède à la page des logs d'activité, THE System SHALL afficher la liste paginée des actions de son compte, triée par date décroissante.
3. IF un User non Premium tente d'accéder aux logs d'activité, THEN THE System SHALL retourner une réponse HTTP 403.
4. THE Activity_Log SHALL conserver les entrées pendant 90 jours puis les supprimer automatiquement.
5. WHERE le plan Premium inclut plusieurs utilisateurs, THE Activity_Log SHALL identifier quel sous-utilisateur a effectué chaque action.

---

### Exigence 11 : Vérification email à l'inscription

**User Story :** En tant qu'administrateur du système, je veux que les nouveaux utilisateurs vérifient leur adresse email avant d'accéder à l'application, afin de garantir la validité des comptes et réduire les inscriptions frauduleuses.

#### Critères d'acceptation

1. WHEN un User s'inscrit, THE System SHALL envoyer un email de vérification contenant un lien signé à durée limitée (24 heures).
2. WHILE l'email d'un User n'est pas vérifié, THE System SHALL restreindre l'accès aux fonctionnalités principales et afficher un bandeau d'invitation à vérifier l'email.
3. WHEN un User clique sur le lien de vérification valide, THE System SHALL marquer l'email comme vérifié et rediriger vers le dashboard.
4. IF le lien de vérification est expiré, THEN THE System SHALL permettre au User de demander un nouvel email de vérification.
5. THE System SHALL utiliser le mécanisme natif `MustVerifyEmail` de Laravel pour la gestion de la vérification.

---

### Exigence 12 : Sécurité renforcée

**User Story :** En tant qu'utilisateur, je veux que toutes les nouvelles fonctionnalités soient sécurisées contre les accès non autorisés et les attaques courantes, afin de protéger mes données commerciales.

#### Critères d'acceptation

1. THE System SHALL appliquer une vérification d'appartenance (ownership check) sur chaque ressource (Facture, Client, Produit, Paiement, Profil_Entreprise) pour s'assurer qu'un User ne peut accéder qu'à ses propres données.
2. THE System SHALL protéger tous les formulaires contre les attaques CSRF via les tokens Laravel.
3. WHEN un User tente d'accéder à une ressource appartenant à un autre User, THE System SHALL retourner une réponse HTTP 403.
4. THE System SHALL valider et assainir toutes les entrées utilisateur avant traitement ou stockage en base de données.
5. THE System SHALL appliquer un rate limiting de 60 requêtes par minute sur les routes authentifiées et de 10 tentatives par minute sur les routes de connexion.
6. IF un fichier uploadé (logo) ne respecte pas les contraintes de type et de taille, THEN THE System SHALL rejeter le fichier et retourner un message d'erreur explicite.
