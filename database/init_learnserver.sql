-- =====================================================
-- LearnServer - Initialisation base de donnees locale
-- Base cible: gestion_cours_db
-- Date: 2026-07-16
-- =====================================================
SET
    NAMES utf8mb4;

SET
    FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- TABLES DE REFERENCE
-- =====================================================
CREATE TABLE
    IF NOT EXISTS filieres (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_filiere VARCHAR(120) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    IF NOT EXISTS niveaux (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_niveau VARCHAR(120) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    IF NOT EXISTS classes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_classe VARCHAR(120) NOT NULL,
        filiere_id INT NULL,
        niveau_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_classe_filiere_niveau (nom_classe, filiere_id, niveau_id),
        CONSTRAINT fk_classes_filiere FOREIGN KEY (filiere_id) REFERENCES filieres (id) ON DELETE SET NULL ON UPDATE CASCADE,
        CONSTRAINT fk_classes_niveau FOREIGN KEY (niveau_id) REFERENCES niveaux (id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =====================================================
-- TABLES UTILISATEURS
-- =====================================================
CREATE TABLE
    IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(120) NOT NULL,
        prenom VARCHAR(120) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        mot_de_passe VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    IF NOT EXISTS enseignants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        matricule VARCHAR(50) NOT NULL UNIQUE,
        nom VARCHAR(120) NOT NULL,
        prenom VARCHAR(120) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        mot_de_passe VARCHAR(255) NOT NULL,
        telephone VARCHAR(30) NULL,
        specialite VARCHAR(150) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    IF NOT EXISTS etudiants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        matricule VARCHAR(50) NOT NULL UNIQUE,
        nom VARCHAR(120) NOT NULL,
        prenom VARCHAR(120) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        mot_de_passe VARCHAR(255) NOT NULL,
        telephone VARCHAR(30) NULL,
        filiere_id INT NULL,
        niveau_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_etudiants_filiere FOREIGN KEY (filiere_id) REFERENCES filieres (id) ON DELETE SET NULL ON UPDATE CASCADE,
        CONSTRAINT fk_etudiants_niveau FOREIGN KEY (niveau_id) REFERENCES niveaux (id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =====================================================
-- TABLES PEDAGOGIQUES
-- =====================================================
CREATE TABLE
    IF NOT EXISTS matieres (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_matiere VARCHAR(150) NOT NULL,
        code_matiere VARCHAR(30) NOT NULL UNIQUE,
        coefficient DECIMAL(4, 2) DEFAULT 1.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    IF NOT EXISTS emplois_temps (
        id INT AUTO_INCREMENT PRIMARY KEY,
        matiere_id INT NOT NULL,
        enseignant_id INT NOT NULL,
        filiere_id INT NOT NULL,
        niveau_id INT NOT NULL,
        jour VARCHAR(20) NOT NULL,
        heure_debut TIME NOT NULL,
        heure_fin TIME NOT NULL,
        salle VARCHAR(50) NULL,
        type_cours VARCHAR(50) NULL,
        semestre VARCHAR(20) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_emplois_matiere FOREIGN KEY (matiere_id) REFERENCES matieres (id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_emplois_enseignant FOREIGN KEY (enseignant_id) REFERENCES enseignants (id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_emplois_filiere FOREIGN KEY (filiere_id) REFERENCES filieres (id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_emplois_niveau FOREIGN KEY (niveau_id) REFERENCES niveaux (id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    IF NOT EXISTS notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        etudiant_id INT NOT NULL,
        matiere_id INT NOT NULL,
        note DECIMAL(4, 2) NOT NULL,
        session VARCHAR(50) NOT NULL,
        date_note DATE NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_notes_etudiant FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_notes_matiere FOREIGN KEY (matiere_id) REFERENCES matieres (id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =====================================================
-- DONNEES DE BASE (REFERENTIEL)
-- =====================================================
INSERT INTO
    filieres (nom_filiere)
VALUES
    ('Informatique'),
    ('Reseaux et Telecom'),
    ('Gestion') ON DUPLICATE KEY
UPDATE nom_filiere =
VALUES
    (nom_filiere);

INSERT INTO
    niveaux (nom_niveau)
VALUES
    ('Licence 1'),
    ('Licence 2'),
    ('Licence 3') ON DUPLICATE KEY
UPDATE nom_niveau =
VALUES
    (nom_niveau);

INSERT INTO
    classes (nom_classe, filiere_id, niveau_id)
SELECT
    'INFO-L1',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Informatique'
    AND n.nom_niveau = 'Licence 1' ON DUPLICATE KEY
UPDATE nom_classe =
VALUES
    (nom_classe);

INSERT INTO
    classes (nom_classe, filiere_id, niveau_id)
SELECT
    'INFO-L2',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Informatique'
    AND n.nom_niveau = 'Licence 2' ON DUPLICATE KEY
UPDATE nom_classe =
VALUES
    (nom_classe);

INSERT INTO
    classes (nom_classe, filiere_id, niveau_id)
SELECT
    'RT-L1',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Reseaux et Telecom'
    AND n.nom_niveau = 'Licence 1' ON DUPLICATE KEY
UPDATE nom_classe =
VALUES
    (nom_classe);

INSERT INTO
    matieres (nom_matiere, code_matiere, coefficient)
VALUES
    ('Programmation Web', 'INF101', 3.00),
    ('Bases de Donnees', 'INF102', 4.00),
    ('Reseaux Informatiques', 'RT201', 3.00),
    ('Comptabilite Generale', 'GES101', 2.00) ON DUPLICATE KEY
UPDATE nom_matiere =
VALUES
    (nom_matiere),
    coefficient =
VALUES
    (coefficient);

-- =====================================================
-- COMPTES UTILISATEURS INITIAUX
-- Mot de passe par defaut pour tous: Learn@1234
-- Hash bcrypt PHP: $2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6
-- =====================================================
INSERT INTO
    admins (nom, prenom, email, mot_de_passe)
VALUES
    (
        'Super',
        'Admin',
        'admin@learnserver.local',
        '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6'
    ) ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe);

INSERT INTO
    enseignants (
        matricule,
        nom,
        prenom,
        email,
        mot_de_passe,
        telephone,
        specialite
    )
VALUES
    (
        'ENS001',
        'Diallo',
        'Moussa',
        'teacher1@learnserver.local',
        '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
        '770000001',
        'Programmation'
    ),
    (
        'ENS002',
        'Ndiaye',
        'Aminata',
        'teacher2@learnserver.local',
        '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
        '770000002',
        'Bases de Donnees'
    ),
    (
        'ENS003',
        'Traore',
        'Ibrahima',
        'teacher3@learnserver.local',
        '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
        '770000003',
        'Reseaux'
    ) ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe),
    telephone =
VALUES
    (telephone),
    specialite =
VALUES
    (specialite);

INSERT INTO
    etudiants (
        matricule,
        nom,
        prenom,
        email,
        mot_de_passe,
        telephone,
        filiere_id,
        niveau_id
    )
SELECT
    'ETU001',
    'Ba',
    'Fatou',
    'student1@learnserver.local',
    '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
    '780000001',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Informatique'
    AND n.nom_niveau = 'Licence 1' ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe),
    telephone =
VALUES
    (telephone),
    filiere_id =
VALUES
    (filiere_id),
    niveau_id =
VALUES
    (niveau_id);

INSERT INTO
    etudiants (
        matricule,
        nom,
        prenom,
        email,
        mot_de_passe,
        telephone,
        filiere_id,
        niveau_id
    )
SELECT
    'ETU002',
    'Sow',
    'Mariam',
    'student2@learnserver.local',
    '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
    '780000002',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Informatique'
    AND n.nom_niveau = 'Licence 1' ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe),
    telephone =
VALUES
    (telephone),
    filiere_id =
VALUES
    (filiere_id),
    niveau_id =
VALUES
    (niveau_id);

INSERT INTO
    etudiants (
        matricule,
        nom,
        prenom,
        email,
        mot_de_passe,
        telephone,
        filiere_id,
        niveau_id
    )
SELECT
    'ETU003',
    'Fall',
    'Cheikh',
    'student3@learnserver.local',
    '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
    '780000003',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Informatique'
    AND n.nom_niveau = 'Licence 2' ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe),
    telephone =
VALUES
    (telephone),
    filiere_id =
VALUES
    (filiere_id),
    niveau_id =
VALUES
    (niveau_id);

INSERT INTO
    etudiants (
        matricule,
        nom,
        prenom,
        email,
        mot_de_passe,
        telephone,
        filiere_id,
        niveau_id
    )
SELECT
    'ETU004',
    'Kane',
    'Awa',
    'student4@learnserver.local',
    '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
    '780000004',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Reseaux et Telecom'
    AND n.nom_niveau = 'Licence 1' ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe),
    telephone =
VALUES
    (telephone),
    filiere_id =
VALUES
    (filiere_id),
    niveau_id =
VALUES
    (niveau_id);

INSERT INTO
    etudiants (
        matricule,
        nom,
        prenom,
        email,
        mot_de_passe,
        telephone,
        filiere_id,
        niveau_id
    )
SELECT
    'ETU005',
    'Camara',
    'Yacine',
    'student5@learnserver.local',
    '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
    '780000005',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Gestion'
    AND n.nom_niveau = 'Licence 1' ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe),
    telephone =
VALUES
    (telephone),
    filiere_id =
VALUES
    (filiere_id),
    niveau_id =
VALUES
    (niveau_id);

INSERT INTO
    etudiants (
        matricule,
        nom,
        prenom,
        email,
        mot_de_passe,
        telephone,
        filiere_id,
        niveau_id
    )
SELECT
    'ETU006',
    'Keita',
    'Salif',
    'student6@learnserver.local',
    '$2y$10$4A1LR/zpeX.rIT5cddNURub82tpqJG294vOmSFfvGm2tRJAdjL/V6',
    '780000006',
    f.id,
    n.id
FROM
    filieres f,
    niveaux n
WHERE
    f.nom_filiere = 'Gestion'
    AND n.nom_niveau = 'Licence 2' ON DUPLICATE KEY
UPDATE nom =
VALUES
    (nom),
    prenom =
VALUES
    (prenom),
    mot_de_passe =
VALUES
    (mot_de_passe),
    telephone =
VALUES
    (telephone),
    filiere_id =
VALUES
    (filiere_id),
    niveau_id =
VALUES
    (niveau_id);

-- =====================================================
-- DONNEES MINIMALES (SCHEDULE + NOTES)
-- =====================================================
INSERT INTO
    emplois_temps (
        matiere_id,
        enseignant_id,
        filiere_id,
        niveau_id,
        jour,
        heure_debut,
        heure_fin,
        salle,
        type_cours,
        semestre
    )
SELECT
    m.id,
    e.id,
    f.id,
    n.id,
    'Lundi',
    '08:00:00',
    '10:00:00',
    'A101',
    'Cours',
    'S1'
FROM
    matieres m,
    enseignants e,
    filieres f,
    niveaux n
WHERE
    m.code_matiere = 'INF101'
    AND e.matricule = 'ENS001'
    AND f.nom_filiere = 'Informatique'
    AND n.nom_niveau = 'Licence 1'
    AND NOT EXISTS (
        SELECT
            1
        FROM
            emplois_temps et
        WHERE
            et.matiere_id = m.id
            AND et.enseignant_id = e.id
            AND et.filiere_id = f.id
            AND et.niveau_id = n.id
            AND et.jour = 'Lundi'
            AND et.heure_debut = '08:00:00'
    );

INSERT INTO
    notes (etudiant_id, matiere_id, note, session, date_note)
SELECT
    s.id,
    m.id,
    15.50,
    'Normale',
    CURDATE ()
FROM
    etudiants s,
    matieres m
WHERE
    s.matricule = 'ETU001'
    AND m.code_matiere = 'INF101'
    AND NOT EXISTS (
        SELECT
            1
        FROM
            notes n
        WHERE
            n.etudiant_id = s.id
            AND n.matiere_id = m.id
            AND n.session = 'Normale'
    );

SET
    FOREIGN_KEY_CHECKS = 1;