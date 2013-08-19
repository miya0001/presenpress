module.exports = function(grunt) {
    'use strict';
    grunt.initConfig({
        pkg : grunt.file.readJSON('package.json'),
        cssmin : {
            compress : {
                files : {
                    'css/presenpress.min.css' : [
                        'css/presenpress.css'
                    ]
                }
            },
        },
        uglify: {
            my_target: {
                files: {
                    'js/reveal-package.min.js': [
                        'reveal/lib/js/head.min.js',
                        'reveal/js/reveal.min.js'
                    ],
                    'js/presenpress.min.js': [
                        'node_modules/leapjs/leap.min.js',
                        'node_modules/jquery-leapmotion/jquery.leapmotion.min.js',
                        'js/presenpress.js'
                    ]
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.registerTask('default', ['cssmin', 'uglify']);
};
