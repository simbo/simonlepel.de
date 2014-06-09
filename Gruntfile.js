module.exports = function(grunt) {
    'use strict';

    // Load all grunt tasks matching the `grunt-*` pattern
    require('load-grunt-tasks')(grunt);

    // Force use of Unix newlines
    grunt.util.linefeed = '\n';

    /* =============================================================================
       Project Configuration
       ========================================================================== */

    grunt.initConfig({

        /* =============================================================================
           Get NPM data
           ========================================================================== */

        pkg: grunt.file.readJSON('package.json'),

        /* =============================================================================
           Task config: Clean
           ========================================================================== */

        clean: {
            deps: [
                'assets/fonts/vendor/*',
                'assets/js/vendor/*',
                '!assets/js/vendor/modernizr',
                'includes/vendor/*',
                'lang/vendor/*'
            ],
            css: [
                'assets/css/*'
            ],
            js: [
                'assets/js/*',
                '!assets/js/vendor/**'
            ]
        },

        /* =============================================================================
           Task Config: Copy dependency files
           ========================================================================== */

        copy: {

            // roots libs
            roots_libs: {
                expand: true,
                src: [
                    'bower_components/roots/lib/utils.php',
                    'bower_components/roots/lib/wrapper.php',
                    'bower_components/roots/lib/sidebar.php',
                    'bower_components/roots/lib/cleanup.php',
                    'bower_components/roots/lib/nav.php',
                    'bower_components/roots/lib/comments.php',
                    'bower_components/roots/lib/relative-urls.php',
                    'bower_components/roots/lib/titles.php',
                ],
                dest: 'includes/vendor/roots/',
                flatten: true
            },

            // roots translations
            roots_lang: {
                expand: true,
                src: 'bower_components/roots/lang/*',
                dest: 'lang/vendor/roots/',
                flatten: true
            },

            // custom metaboxes
            metaboxes: {
                expand: true,
                cwd: 'bower_components/Custom-Metaboxes-and-Fields-for-WordPress/',
                src: [
                    'init.php',
                    '*.min.css',
                    '*/*'
                ],
                dest: 'includes/vendor/metabox/'
            },

            // font awesome
            fontawesome: {
                expand: true,
                src: 'bower_components/font-awesome/fonts/*',
                dest: 'assets/fonts/vendor/font-awesome/',
                flatten: true
            },

            // local jquery
            jquery: {
                expand: true,
                src: 'bower_components/jquery/dist/*',
                dest: 'assets/js/vendor/jquery/',
                flatten: true
            }

        },

        /* =============================================================================
           Task config: Update json
           ========================================================================== */

        update_json: {
            bower: {
                src: 'package.json',
                dest: 'bower.json',
                fields: [
                    'name',
                    'version',
                    'description'
                ]
            }
        },

        /* =============================================================================
           Task Config: Concatenation
           ========================================================================== */

        concat: {

            // create style.css for Wordpress theme
            theme: {
                options: {
                    banner: '/* \n'+
                        '  Theme Name:  <%= pkg.name %>\n'+
                        '  Theme URI:   <%= pkg.homepage %>\n'+
                        '  Author:      <%= pkg.author.name %>\n'+
                        '  Author URI:  <%= pkg.author.url %>/\n'+
                        '  Description: <%= pkg.description %>\n'+
                        '  Version:     <%= pkg.version %>\n'+
                        '  License:     <%= pkg.license.type %>\n'+
                        '  Tags:        <%= pkg.keywords %>\n'+
                        '  Text Domain: <%= pkg.name %>\n'+
                        '*/'
                },
                src: [],
                dest: 'style.css'
            }

        },

        /* =============================================================================
           Task Config: LESS
           ========================================================================== */

        less: {
            options: {
                strictMath: true,
                sourceMap: true,
                strictImports: true,
                outputSourceFiles: true,
                report: 'min',
                compress: true
            },
            theme: {
                options: {
                    sourceMapURL: 'styles.min.css.map',
                    sourceMapFilename: 'assets/css/styles.min.css.map'
                },
                files: {
                    'assets/css/styles.min.css': 'assets/less/styles.less'
                }
            },
            fontawesome: {
                options: {
                    sourceMapURL: 'fontawesome-font.min.css.map',
                    sourceMapFilename: 'assets/css/fontawesome-font.min.css.map'
                },
                files: {
                    'assets/css/fontawesome-font.min.css': 'assets/less/fontawesome-font.less'
                }
            },
            // editor: {
            //     options: {
            //         sourceMapURL: 'editor-styles.min.css.map',
            //         sourceMapFilename: 'assets/css/editor-styles.min.css.map'
            //     },
            //     files: {
            //         'assets/css/editor-styles.min.css': 'assets/less/editor-styles.less'
            //     }
            // }
        },

        /* =============================================================================
           Task config: Autoprefixer
           ========================================================================== */

        autoprefixer: {
            options: {
                browsers: [
                    'last 2 versions',
                    'ie 9',
                    'android 2.3',
                    'android 4',
                    'opera 12'
                ],
                map: true
            },
            theme: {
                src: 'assets/css/styles.min.css'
            },
            // editor: {
            //     src: 'assets/css/editor-styles.min.css'
            // }
        },

        /* =============================================================================
           Task Config: CSSLint
           ========================================================================== */

        csslint: {
            options: {
                'adjoining-classes': false,
                'unique-headings': false,
                'important': false,
                'unqualified-attributes': false,
                'outline-none': false,
                'box-sizing': false,
                'compatible-vendor-prefixes': false,
                'universal-selector': false,
                'regex-selectors': false,
                'zero-units': false,
                'box-model': false,
                'known-properties': false,
                'shorthand': false,
                'qualified-headings': false,
                'gradients': false,
                'font-sizes': false,
                'floats': false,
                'text-indent': false,
                'overqualified-elements': false,
                'ids': false,
                'duplicate-properties': false,
                'fallback-colors': false,
                'empty-rules': false,
                'vendor-prefix': false
            },
            src: [
                'assets/css/styles.min.css',
                'assets/css/editor-styles.min.css',
            ]
        },

        /* =============================================================================
           Task config: Coffeescript
           ========================================================================== */

        coffee: {
            options: {
                separator: '\n',
                bare: true,
                join: false,
                sourceMap: true
            },
            compile: {
                files: {
                    'assets/js/main.js': [
                        'assets/coffee/main.coffee'
                    ]
                }
            }
        },

        /* =============================================================================
           Task Config: JSHint
           ========================================================================== */

        jshint: {
            options: {
                'indent'   : 2,
                'quotmark' : 'single'
            },
            js: {
                src: 'assets/js/main.js'
            },
            grunt: {
                options: {
                    'indent': 4
                },
                src: 'Gruntfile.js'
            }
        },

        /* =============================================================================
           Task Config: Uglify
           ========================================================================== */

        uglify: {
            options: {
                sourceMap: true
            },
            js: {
                files: {
                    'assets/js/main.min.js': [
                        'bower_components/jquery-easing-original/jquery.easing.1.3.js',
                        'bower_components/jquery-hoverIntent/jquery.hoverIntent.js',
                        'bower_components/bootstrap/js/transition.js',
                        // 'bower_components/bootstrap/js/alert.js',
                        // 'bower_components/bootstrap/js/button.js',
                        // 'bower_components/bootstrap/js/carousel.js',
                        // 'bower_components/bootstrap/js/collapse.js',
                        // 'bower_components/bootstrap/js/dropdown.js',
                        // 'bower_components/bootstrap/js/modal.js',
                        'bower_components/bootstrap/js/tooltip.js',
                        // 'bower_components/bootstrap/js/popover.js',
                        // 'bower_components/bootstrap/js/scrollspy.js',
                        // 'bower_components/bootstrap/js/tab.js',
                        // 'bower_components/bootstrap/js/affix.js',
                        'assets/js/main.js'
                    ]
                },
            }
        },

        /* =============================================================================
           Task Config: Wordpress Versioning
           ========================================================================== */

        version: {
            assets: {
                options: {
                    algorithm: 'md5',
                    length: 4,
                    format: true,
                    rename: false,
                    minify: true,
                    minifyname: 'min',
                    encoding: 'utf8',
                    querystring: {
                        style: 'theme-styles',
                        script: 'theme-scripts'
                    }
                },
                files: {
                    'includes/scriptsnstyles.php': [
                        'assets/css/styles.min.css',
                        'assets/js/main.min.js'
                    ]
                }
            }
        },

        /* =============================================================================
           Task Config: Watch
           ========================================================================== */

        watch: {
            php: {
                files: [
                    '*.php',
                    'templates/*.php',
                    'includes/*.php',
                    'includes/lib/*.php',
                    'includes/lib/shortcodes/*.php'
                ],
                options: {
                    livereload: true
                }
            },
            theme: {
                files: [
                    'package.json'
                ],
                tasks: [
                    'build-theme',
                    'notify:theme'
                ]
            },
            less: {
                files: [
                    'assets/less/*.less',
                    'assets/less/mixins/*.less'
                ],
                tasks: [
                    'build-css',
                    'version',
                    'notify:less'
                ],
                options: {
                    livereload: true
                }
            },
            coffee: {
                files: [
                    'assets/coffee/*.coffee'
                ],
                tasks: [
                    'build-js',
                    'version',
                    'notify:coffee'
                ],
                options: {
                    livereload: true
                }
            },
            grunt: {
                files: [
                    'Gruntfile.js'
                ],
                tasks: [
                    'jshint:grunt',
                    'notify:grunt'
                ]
            }
        },

        /* =============================================================================
           Task Config: Notifications
           ========================================================================== */

        notify: {
            theme: {
                options: {
                    title: 'JSON Update',
                    message: 'Merged bower.json with package.json, built style.css.'
                }
            },
            less: {
                options: {
                    title: 'LESS',
                    message: 'CSS generated, linted and minified.'
                }
            },
            coffee: {
                options: {
                    title: 'Coffeescript',
                    message: 'Javascript generated, linted and minified.'
                }
            },
            grunt: {
                options: {
                    title: 'Gruntfile',
                    message: 'No hints.'
                }
            }
        }

    });

    /* =============================================================================
       Custom Tasks
       ========================================================================== */

    grunt.registerTask( 'copy-deps', [
        'clean:deps',
        'copy:roots_libs',
        'copy:roots_lang',
        'copy:metaboxes',
        'copy:fontawesome',
        'copy:jquery'
    ]);
    grunt.registerTask( 'build-theme', [
        'update_json',
        'concat:theme'
    ]);
    grunt.registerTask( 'build-css', [
        'clean:css',
        'less',
        'autoprefixer',
        'csslint'
    ]);
    grunt.registerTask( 'build-js', [
        'clean:js',
        'coffee',
        'jshint:js',
        'uglify'
    ]);
    grunt.registerTask( 'build', [
        'build-theme',
        'copy-deps',
        'build-css',
        'build-js',
        'version'
    ]);
    grunt.registerTask( 'default', [
        // 'build',
        'watch'
    ]);

};
