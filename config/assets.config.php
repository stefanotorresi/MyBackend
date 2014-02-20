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
                    'my-backend/js/yepnope-bundle.js',
                    'my-backend/vendor/modernizr/modernizr.js',
                ],
                'my-backend/js/scripts.js' => [
                    'my-backend/js/jquery-bundle.js',
                    'my-backend/js/bootstrap.js',
                ],

                // js collections
                'my-backend/js/jquery-bundle.js' => [
                    'my-backend/vendor/jquery/dist/jquery.js',
                ],
                'my-backend/js/yepnope-bundle.js' => [
                    'my-backend/vendor/yepnope/yepnope.js',
                    'my-backend/vendor/yepnope/plugins/yepnope.css.js',
                    'my-backend/vendor/yepnope/prefixes/yepnope.css-prefix.js',
                    'my-backend/vendor/yepnope/prefixes/yepnope.ie-prefix.js',
                    'my-backend/vendor/yepnope/prefixes/yepnope.preload.js',
                ],
                'my-backend/js/bootstrap.js' => [
                    'my-backend/vendor/yatsatrap/js/bootstrap-transition.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-alert.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-button.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-carousel.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-collapse.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-dropdown.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-modal.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-tooltip.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-popover.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-scrollspy.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-tab.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-typeahead.js',
                    'my-backend/vendor/yatsatrap/js/bootstrap-affix.js',
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
