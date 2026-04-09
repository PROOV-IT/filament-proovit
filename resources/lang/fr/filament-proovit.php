<?php

return [
    'navigation' => [
        'label' => 'ProovIT',
        'overview' => 'Vue générale',
        'certificates' => 'Certificats',
        'proofs' => 'Preuves',
        'settings' => 'Configuration',
    ],

    'overview' => [
        'heading' => 'Vue générale ProovIT',
        'actions' => [
            'test_connection' => 'Tester la connexion',
            'refresh' => 'Rafraîchir',
        ],
        'notifications' => [
            'connection_successful_title' => 'Connexion ProovIT réussie',
            'connection_failed_title' => 'Connexion ProovIT échouée',
        ],
        'cards' => [
            'company_name' => 'Société',
            'login_email' => 'Connexion',
            'base_url' => 'URL API',
            'mode' => 'Mode',
        ],
    ],

    'widgets' => [
        'connection' => [
            'heading' => 'Connexion ProovIT',
            'label' => 'Libellé',
            'company_name' => 'Société',
            'login_email' => 'Connexion',
            'base_url' => 'URL API',
            'app_url' => 'URL application',
            'selected_company_uuid' => 'UUID de la société sélectionnée',
            'mode' => 'Mode',
            'connected' => 'Connecté',
            'widgets_enabled' => 'Widgets activés',
            'yes' => 'oui',
            'no' => 'non',
            'enabled' => 'activé',
            'disabled' => 'désactivé',
            'unknown' => 'inconnu',
            'not_configured' => 'non configuré',
        ],
        'recent_proofs' => [
            'heading' => 'Preuves récentes',
            'name' => 'Nom',
            'status' => 'Statut',
            'description' => 'Description',
            'empty' => 'Aucune preuve trouvée.',
            'no_description' => 'Aucune description',
        ],
    ],

    'proofs' => [
        'title' => 'Preuves ProovIT',
        'navigation' => 'Preuves',
        'heading' => 'Cycle de vie des preuves',
        'description' => 'Consulte les preuves récentes, ouvre leurs certificats et révoque-les lorsque ton processus légal le permet.',
        'actions' => [
            'refresh' => 'Rafraîchir',
            'view' => 'Voir',
            'open_certificate' => 'Ouvrir le certificat',
            'revoke' => 'Révoquer',
        ],
        'notifications' => [
            'revoked_title' => 'Preuve révoquée',
            'revoked_body' => 'La preuve sélectionnée a été révoquée dans ProovIT.',
        ],
        'columns' => [
            'name' => 'Nom',
            'status' => 'Statut',
            'signed_at' => 'Signée le',
            'description' => 'Description',
            'certificate' => 'Certificat',
        ],
        'status' => [
            'unknown' => 'Inconnu',
        ],
        'placeholders' => [
            'untitled' => 'Preuve sans titre',
            'no_description' => 'Aucune description',
            'not_signed' => 'Pas encore signée',
            'not_available' => 'Non disponible',
        ],
        'empty' => [
            'heading' => 'Aucune preuve trouvée',
            'description' => 'Crée ou importe une preuve dans ProovIT pour suivre son cycle de vie ici.',
        ],
    ],

    'certificates' => [
        'title' => 'Certificats ProovIT',
        'navigation' => 'Certificats',
        'heading' => 'Certificats prêts à ouvrir',
        'description' => 'Ces preuves disposent déjà d’un lien de certificat. Tu peux l’ouvrir ou consulter le détail de la preuve.',
        'actions' => [
            'refresh' => 'Rafraîchir',
        ],
        'empty' => [
            'heading' => 'Aucun certificat disponible',
            'description' => 'Dès qu’une preuve est signée, son certificat apparaît ici et peut être ouvert depuis la preuve associée.',
        ],
    ],

    'proof_view' => [
        'title' => 'Détail de la preuve',
        'navigation' => 'Détail de la preuve',
        'note' => [
            'heading' => 'Détail de la preuve',
            'body' => 'Consulte le payload de la preuve, son historique et son certificat depuis un seul écran.',
        ],
        'actions' => [
            'refresh' => 'Rafraîchir',
            'open_certificate' => 'Ouvrir le certificat',
            'revoke' => 'Révoquer',
        ],
        'notifications' => [
            'revoked_title' => 'Preuve révoquée',
            'revoked_body' => 'La preuve a été révoquée dans ProovIT.',
        ],
        'sections' => [
            'summary' => 'Résumé',
            'metadata' => 'Métadonnées et historique',
        ],
        'fields' => [
            'name' => 'Nom',
            'title' => 'Titre',
            'seq' => 'Séquence',
            'status' => 'Statut',
            'signed_at' => 'Signée le',
            'certificate_url' => 'URL du certificat',
            'description' => 'Description',
            'metadata' => 'Métadonnées',
            'history' => 'Historique',
        ],
    ],

    'settings' => [
        'title' => 'Configuration ProovIT',
        'navigation' => 'Configuration',
        'note' => [
            'heading' => 'Connexion ProovIT',
            'body' => 'Choisis l’endpoint API, saisis tes identifiants, teste la connexion, puis sélectionne la société à rattacher à ce panel.',
        ],
        'actions' => [
            'save' => 'Enregistrer',
            'test_connection' => 'Tester la connexion',
        ],
        'notifications' => [
            'authenticated_title' => 'Authentifié',
            'authenticated_body' => 'Le jeton bearer ProovIT et la liste des sociétés ont été rafraîchis.',
            'authenticate_failed_title' => 'Authentification échouée',
            'saved_title' => 'Configuration enregistrée',
            'saved_body' => 'La configuration persistée a été mise à jour.',
            'save_failed_title' => 'Configuration non enregistrée',
            'save_failed_body' => 'Le stockage de configuration est indisponible ou l’enregistrement a échoué.',
        ],
        'modes' => [
            'production' => 'Production',
            'staging' => 'Préproduction',
            'sandbox' => 'Sandbox',
            'local' => 'Local',
        ],
        'sections' => [
            'connection' => 'Connexion',
            'connection_description' => 'Paramètres de compte et d’endpoint utilisés par le SDK et le panel.',
            'api' => 'API',
            'api_description' => 'Versioning et routage API bas niveau.',
            'features' => 'Fonctionnalités',
            'features_description' => 'Activer ou désactiver les capacités du SDK.',
            'certificates' => 'Certificats',
            'certificates_description' => 'Valeurs par défaut pour les noms de fichiers de certificats.',
            'exports' => 'Exports',
            'exports_description' => 'Valeurs par défaut de stockage local des exports.',
            'audit' => 'Audit',
            'audit_description' => 'Transport par défaut du journal d’audit.',
            'docs' => 'Documentation',
            'docs_description' => 'Valeurs par défaut de publication de la documentation générée.',
        ],
        'fields' => [
            'company_name' => 'Société',
            'login_email' => 'Email de connexion',
            'base_url' => 'URL de base API',
            'selected_company' => 'Société sélectionnée',
            'selected_company_uuid' => 'UUID de la société sélectionnée',
            'password' => 'Mot de passe',
        ],
        'helpers' => [
            'base_url' => 'Choisis l’endpoint API utilisé par le SDK. Le préfixe /api est déjà inclus.',
            'selected_company' => 'Teste d’abord la connexion pour charger la liste des sociétés disponibles.',
        ],
        'base_urls' => [
            'production' => 'Production',
            'staging' => 'Préproduction',
        ],
    ],
];
