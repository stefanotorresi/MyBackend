<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

return [
    'asset_manager' => [
        'resolver_configs' => [
            'collections' => [

                // main output files
                'my-backend/css/main.css' => [
                    'my-backend/sass/backend.scss',
                ],
                'my-backend/js/header.js' => [
                    'application/js/yepnope-bundle.js',
                    'application/vendor/modernizr/modernizr.js',
                ],
                'my-backend/js/scripts.js' => [
                    'my-backend/js/jquery-bundle.js',
                    'my-backend/js/bootstrap.js',
                ],

                // js collections
                'my-backend/js/jquery-bundle.js' => [
                    'application/vendor/jquery/dist/jquery.js',
                ],
                'my-backend/js/bootstrap.js' => [
                    'application/vendor/yatsatrap/js/bootstrap-transition.js',
                    'application/vendor/yatsatrap/js/bootstrap-alert.js',
                    'application/vendor/yatsatrap/js/bootstrap-button.js',
                    'application/vendor/yatsatrap/js/bootstrap-carousel.js',
                    'application/vendor/yatsatrap/js/bootstrap-collapse.js',
                    'application/vendor/yatsatrap/js/bootstrap-dropdown.js',
                    'application/vendor/yatsatrap/js/bootstrap-modal.js',
                    'application/vendor/yatsatrap/js/bootstrap-tooltip.js',
                    'application/vendor/yatsatrap/js/bootstrap-popover.js',
                    'application/vendor/yatsatrap/js/bootstrap-scrollspy.js',
                    'application/vendor/yatsatrap/js/bootstrap-tab.js',
                    'application/vendor/yatsatrap/js/bootstrap-typeahead.js',
                    'application/vendor/yatsatrap/js/bootstrap-affix.js',
                ],

            ],

            'paths' => [
                __DIR__ . '/../assets',
            ],
        ],
        'filters' => [
            'my-backend/sass/backend.scss' => [
                [ 'service' => 'MyAsseticSassFilter' ],
            ],
        ],
    ],
];
