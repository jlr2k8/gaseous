module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		cwd: 'app/assets-src',
		concat: {
			options: {
				stripBanners: true
			},
			css: {
				src: ['app/assets-src/css/**/*.css'],
				dest: 'app/assets-src/styles.concat.css'
			},
			js: {
				// make sure jquery is first
				src: ['app/assets-src/js/jquery*.js', 'app/assets-src/js/**/*.js'],
				dest: 'app/assets-src/js.concat.js'
			}
		},
		uglify: {
			options: {
				mangle: true
			},
			js: {
				files: {
					'app/assets-src/js.min.js': ['app/assets-src/js.concat.js']
				}
			}
		},
		cssmin: {
			css: {
				files: [{
					'app/assets-src/styles.min.css': ['app/assets-src/styles.concat.css']
				}]
			}
		},
		compress: {
			css_and_js: {
				options: {
					mode: 'gzip'
				},
				files: [
					{
						'app/assets/js.gz.js': ['app/assets-src/js.min.js']
					},
					{
						'app/assets/styles.gz.css': ['app/assets-src/styles.min.css']
					}
				]
			}
		}
		
	});

	// Load plugins
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-compress');

	// Default tasks
	grunt.registerTask('default', ['concat','uglify','cssmin','compress']);
};