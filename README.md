# LearnServer 📚

> Application client-serveur de gestion des cours pour étudiants et enseignants.

---

## Présentation

LearnServer est une plateforme web (et mobile) centralisée qui permet aux établissements scolaires et universitaires de gérer leurs cours, ressources pédagogiques, emplois du temps, devoirs et communications — le tout depuis une seule interface.

---

## Fonctionnalités principales

- **Gestion des cours** — création, organisation en modules, upload de ressources (PDF, vidéos, liens)
- **Devoirs & évaluations** — dépôt de fichiers, correction, notation, feedback
- **Emploi du temps** — calendrier interactif avec rappels et notifications
- **Suivi des présences** — prise de présence par séance, alertes d'absentéisme
- **Messagerie** — communication directe entre enseignants et étudiants
- **Tableau de bord** — vue personnalisée selon le rôle (étudiant, enseignant, admin)

---

## Rôles utilisateurs

| Rôle | Accès |
|------|-------|
| **Etudiant** | Consulter les cours, rendre des devoirs, voir ses notes et son emploi du temps |
| **Enseignant** | Créer et gérer des cours, corriger, prendre les présences, envoyer des annonces |
| **Administrateur** | Gérer les comptes, les affectations, la configuration globale |

---

## Architecture

L'application suit une architecture **client-serveur 3-tiers** :

```
[ Client Web / Mobile ]  →  [ API REST + WebSocket ]  →  [ Base de données ]
```

- **Frontend** : application web (SPA) + version mobile (PWA ou app native)
- **Backend** : serveur API REST avec authentification JWT
- **Base de données** : relationnelle (PostgreSQL) + cache (Redis)
- **Stockage fichiers** : cloud ou serveur local

---

## Installation

```bash
# Cloner le dépôt
git clone https://github.com/ton-repo/LearnServer.git
cd LearnServer

# Configurer les variables d'environnement
cp .env.example .env

# Installer les dépendances et lancer
# (voir la documentation spécifique au stack choisi)
```

---

## Documentation

Le dossier `/docs` contient :
- Cahier des charges
- Modélisation MERISE (MCD, MLD)
- Diagrammes UML (use cases, classes, séquences)
- Documentation API (Swagger)

---