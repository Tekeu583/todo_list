# todo_list

## liste_de_tâches

Liste de tâches du projet
Bienvenue dans le projet ToDo List ! Une application web collaborative de gestion de tâches, permettant à chaque utilisateur d’ajouter, modifier ou supprimer ses propres tâches. L’administrateur peut gérer toutes les tâches.
Ce fichier explique clairement commenter chaque membre doit travailler pour garantir une bonne organisation avec Git et GitHub.

## Objectif du projet

Créez une application de gestion de tâches (ToDo List) avec les fonctionnalités suivantes :

- Ajouter une tâche
- Modifier une tâche
- Supprimer une tâche
- Un utilisateur ne peut modifier ou supprimer qu'une tâche qu'il a créée
- L'administrateur peut modifier ou supprimer n'importe quelle tâche

## Répartition des rôles

Chef de projet (Tekeu) travail sur le Backend & gestion et sur la branche `feature/backend`
Membre 1 travaillant sur la Page connexion et sur la branche `feature/login`
Membre 2 travaile sur la Page inscription et sur la branche `feature/register`
Membre 3 travaile sur la Page ajouter une tâche et sur la branche `feature/tasks`

## Structure des branches

-`main` : Branche principale (version finale du projet) 🔐(verroullée) -`dev` : Branche intermédiaire (version test validée) 🔐(verroullée) -`feature/*` : Branches de développement personnel
Exemples : -`fonctionnalité/connexion` -`fonctionnalité/enregistrement` -`fonctionnalité/backend` -`fonctionnalité/tâches`

## Étapes de travail pour chaque membre

### 1. Cloner le projet

creer un dossier nommer <<todo_list>> puis placer vous dans ce dossier et ouvrez git puis saissisez:
`bash`
git init

git clone https://github.com/Tekeu583/todo_list.git

cd todo_list

git checkout nom_de_votre_branche

git pull

### 2-Fusion et validation

-Le chef de projet valide les Pull Requests (PR) vers dev
-Une fois tout validé/testé dans dev, le chef de projet crée une PR vers main
-Seul le chef de projet peut fusionner vers main
⚠️ Règles à respecter
À ne pas faire
❌ Ne jamais travailler dans main ou dev( sa ne va pas d'abord prendre sauf si c'est moi)
❌ Ne jamais modifier le travail d'un autre sans accord
❌ Ne jamais faire de push direct vers main ou dev (avec `git pull` )
À faire absolument
✅ Travailler uniquement dans votre branche `feature/login`
✅ Mettre à jour avec dev avant de coder (avec `git pull` )
✅ Créer un PR propre et clair
✅ Suivre les validations du chef de projet
📌 Astuces utiles
`git branch` → voir la branche actuelle
`git status` → voir les fichiers modifiés
`git log --oneline` → voir l'historique des commits
`git ajouter .` -> ajouter toutes les modifications dans la zone d'indexation
`git commit -m" description du commit"`
`git push -u origin nom_de_la_branche`

chef de projet : TEKEU
