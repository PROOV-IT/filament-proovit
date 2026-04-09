<?php

return [
    'navigation' => [
        'label' => 'ProovIT',
        'overview' => 'Overview',
        'proofs' => 'Proofs',
        'certificates' => 'Certificates',
        'settings' => 'Settings',
    ],

    'overview' => [
        'heading' => 'ProovIT overview',
        'actions' => [
            'test_connection' => 'Test connection',
            'refresh' => 'Refresh',
        ],
        'notifications' => [
            'connection_successful_title' => 'ProovIT connection successful',
            'connection_failed_title' => 'ProovIT connection failed',
        ],
        'cards' => [
            'company_name' => 'Company',
            'login_email' => 'Login',
            'base_url' => 'Base URL',
            'mode' => 'Mode',
        ],
    ],

    'widgets' => [
        'connection' => [
            'heading' => 'ProovIT connection',
            'label' => 'Label',
            'company_name' => 'Company',
            'login_email' => 'Login',
            'base_url' => 'Base URL',
            'app_url' => 'App URL',
            'selected_company_uuid' => 'Selected company UUID',
            'mode' => 'Mode',
            'connected' => 'Connected',
            'widgets_enabled' => 'Widgets enabled',
            'yes' => 'yes',
            'no' => 'no',
            'enabled' => 'enabled',
            'disabled' => 'disabled',
            'unknown' => 'unknown',
            'not_configured' => 'not configured',
        ],
        'recent_proofs' => [
            'heading' => 'Recent proofs',
            'name' => 'Name',
            'status' => 'Status',
            'description' => 'Description',
            'empty' => 'No proofs found.',
            'no_description' => 'No description',
        ],
    ],

    'settings' => [
        'title' => 'ProovIT settings',
        'navigation' => 'Settings',
        'note' => [
            'heading' => 'Connection settings',
            'body' => 'Authenticate with ProovIT, then select the company to bind to this panel.',
        ],
        'actions' => [
            'save' => 'Save settings',
            'reload' => 'Reload',
            'authenticate' => 'Authenticate',
        ],
        'notifications' => [
            'authenticated_title' => 'Authenticated',
            'authenticated_body' => 'The ProovIT bearer token and company list have been refreshed.',
            'authenticate_failed_title' => 'Authentication failed',
            'saved_title' => 'Settings saved',
            'saved_body' => 'The persisted configuration has been updated.',
            'save_failed_title' => 'Settings not saved',
            'save_failed_body' => 'The settings store is unavailable or the configuration could not be persisted.',
        ],
        'modes' => [
            'production' => 'Production',
            'staging' => 'Staging',
            'sandbox' => 'Sandbox',
            'local' => 'Local',
        ],
        'sections' => [
            'connection' => 'Connection',
            'connection_description' => 'Main account and endpoint settings used by the SDK and the panel.',
            'api' => 'API',
            'api_description' => 'Low-level API routing and versioning.',
            'features' => 'Features',
            'features_description' => 'Enable or disable SDK capabilities.',
            'certificates' => 'Certificates',
            'certificates_description' => 'Certificate file naming defaults.',
            'exports' => 'Exports',
            'exports_description' => 'Local export storage defaults.',
            'audit' => 'Audit',
            'audit_description' => 'Audit trail transport defaults.',
            'docs' => 'Documentation',
            'docs_description' => 'Generated documentation publishing defaults.',
        ],
        'fields' => [
            'company_name' => 'Company',
            'login_email' => 'Login email',
            'base_url' => 'API base URL',
            'selected_company' => 'Selected company',
            'selected_company_uuid' => 'Selected company UUID',
            'password' => 'Password',
        ],
        'helpers' => [
            'base_url' => 'Use the API base URL, including the /api prefix.',
            'selected_company' => 'Authenticate first to load the list of companies you can bind to.',
        ],
    ],

    'proofs' => [
        'title' => 'ProovIT proofs',
        'navigation' => 'Proofs',
        'heading' => 'Proof lifecycle',
        'description' => 'Review recent proofs, open their certificates, and revoke them when your legal process allows it.',
        'actions' => [
            'refresh' => 'Refresh',
            'view' => 'View',
            'open_certificate' => 'Open certificate',
            'revoke' => 'Revoke',
        ],
        'notifications' => [
            'revoked_title' => 'Proof revoked',
            'revoked_body' => 'The selected proof has been revoked in ProovIT.',
        ],
        'columns' => [
            'name' => 'Name',
            'status' => 'Status',
            'signed_at' => 'Signed at',
            'description' => 'Description',
            'certificate' => 'Certificate',
        ],
        'status' => [
            'unknown' => 'Unknown',
        ],
        'placeholders' => [
            'untitled' => 'Untitled proof',
            'no_description' => 'No description',
            'not_signed' => 'Not signed yet',
            'not_available' => 'Not available',
        ],
        'empty' => [
            'heading' => 'No proofs found',
            'description' => 'Create or import a proof in ProovIT to start monitoring its lifecycle here.',
        ],
    ],

    'certificates' => [
        'title' => 'ProovIT certificates',
        'navigation' => 'Certificates',
        'heading' => 'Certificates ready to open',
        'description' => 'These proofs already expose a certificate link. Open the certificate or review the proof details.',
        'actions' => [
            'refresh' => 'Refresh',
        ],
        'empty' => [
            'heading' => 'No certificates available',
            'description' => 'Once a proof is signed, the certificate appears here and can be opened from the related proof.',
        ],
    ],

    'proof_view' => [
        'title' => 'Proof details',
        'navigation' => 'Proof details',
        'note' => [
            'heading' => 'Proof detail',
            'body' => 'Review the proof payload, history, and certificate link from a single screen.',
        ],
        'actions' => [
            'refresh' => 'Refresh',
            'open_certificate' => 'Open certificate',
            'revoke' => 'Revoke',
        ],
        'notifications' => [
            'revoked_title' => 'Proof revoked',
            'revoked_body' => 'The proof has been revoked in ProovIT.',
        ],
        'sections' => [
            'summary' => 'Summary',
            'metadata' => 'Metadata and history',
        ],
        'fields' => [
            'name' => 'Name',
            'title' => 'Title',
            'seq' => 'Sequence',
            'status' => 'Status',
            'signed_at' => 'Signed at',
            'certificate_url' => 'Certificate URL',
            'description' => 'Description',
            'metadata' => 'Metadata',
            'history' => 'History',
        ],
    ],
];
