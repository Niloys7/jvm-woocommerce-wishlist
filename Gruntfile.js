/**
 * Complete WP example at: http://code.tutsplus.com/tutorials/using-grunt-with-wordpress-development--cms-21743 
 */
module.exports = function(grunt) {
 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        
        makepot: {
            target: {
                options: {
                    domainPath: 'languages',
                    exclude: [
                        'node_modules/*',
                        'tests/*'
                    ], 
                    //mainFile: 'style.css',
                    type: 'wp-plugin' // `wp-plugin` or `wp-theme`
                }
            }
        }
    });
 
    grunt.loadNpmTasks('grunt-wp-i18n');
 
    grunt.registerTask('default', [
        'makepot'
    ]);
};