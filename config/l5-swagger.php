<?php

return [

    'default' => 'default',

    'documentations' => [

        'default' => [
            'api' => [
                'title' => env('L5_SWAGGER_API_TITLE', 'Gestion Dette API'),
                'description' => env('L5_SWAGGER_API_DESCRIPTION', 'API Documentation for Gestion Dette'),
                'version' => env('L5_SWAGGER_API_VERSION', '1.0.0'),
                'termsOfService' => env('L5_SWAGGER_API_TERMS_OF_SERVICE', null),
                'contact' => [
                    'name' => env('L5_SWAGGER_API_CONTACT_NAME', 'Support Team'),
                    'email' => env('L5_SWAGGER_API_CONTACT_EMAIL', 'support@domain.com'),
                    'url' => env('L5_SWAGGER_API_CONTACT_URL', null),
                ],
                'license' => [
                    'name' => env('L5_SWAGGER_API_LICENSE_NAME', 'Apache 2.0'),
                    'url' => env('L5_SWAGGER_API_LICENSE_URL', 'https://www.apache.org/licenses/LICENSE-2.0.html'),
                ],
            ],

            'routes' => [
                'api' => 'api/documentation',
                'docs' => 'docs',
                'oauth2_callback' => 'api/documentation/oauth2-callback',
                'middleware' => [
                    'api' => [],
                    'asset' => [],
                    'docs' => [],
                    'oauth2_callback' => [],
                ],
                'group_options' => [],
            ],

            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_GENERATE_ABSOLUTE_PATH', false),
                'docs' => storage_path('api-docs'),
                'docs_yaml' => base_path('resources/swagger/swagger.yaml'), // Path to the YAML file
                'format_to_use_for_docs' => env('L5_SWAGGER_FORMAT_TO_USE_FOR_DOCS', 'yaml'), // Format of the docs
                'annotations' => base_path('app'),
                'excludes' => [],
                'base' => env('L5_SWAGGER_BASE_PATH', null),
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
                'views' => base_path('resources/views/vendor/l5-swagger'),
            ],

            'securityDefinitions' => [
                'securitySchemes' => [
                    'oauth2_security' => [
                        'type' => 'oauth2',
                        'description' => 'OAuth2 Security',
                        'flow' => 'password',
                        'tokenUrl' => env('L5_SWAGGER_OAUTH2_TOKEN_URL', 'http://localhost:8000/oauth/token'),
                        'scopes' => []
                    ],
                ],
                'security' => [
                    [
                        'oauth2_security' => []
                    ],
                ],
            ],

            'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),
            'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', true),

            'swagger_version' => env('L5_SWAGGER_VERSION', '3.0'),

            'proxy' => false,

            'additional_config_url' => null,

            'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

            'validator_url' => null,

            'constants' => [
                'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8000'),
            ],
        ],
    ],

    'defaults' => [
        'use_definitions' => true,
        'use_jwt_auth' => false,
        'rebuild_per_version' => false,
        'parse_excludes' => [],
        'rebuild_always' => false,
        'use_full_parser' => false,
        'skip_empty_parser' => false,
        'proxy' => false,
        'middlewares' => [],
    ],
];
