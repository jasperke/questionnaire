'use strict';

module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		BASE_PATH: '../',
		LIB_PATH: './bower_components/',
		JS_PATH: '../js/',

		banner: [
				 '/*',
				 '* Project: <%= pkg.name %>',
				 '* Version: <%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd HH:MM") %>)',
				 '* Development By: <%= pkg.author %>',
				 '* Copyright(c): <%= grunt.template.today("yyyy") %> Jasper Ke All rights reserved.',
				 '*/',
				 ''
		],

		concat: {
			// dist: {
			// 	src: ['./src/main.js','./src/quizPool.js'],
			// 	dest: './tmp/main.js'
			// },
			dev: {
				src: ['./src/main.js','./src/quizPool.js','./src/cancerPool.js','<%= JS_PATH %>src/datePicker.js','<%= JS_PATH %>src/CyberChart.js'],
				dest: '<%= JS_PATH %>main.min.js'
			}
		},
		// clean: {
		// 	dist: ['./tmp']
		// },
		uglify: {
			options: {
				banner: '<%= banner.join("\\n") %>',
				beautify : {ascii_only: false},
				mangle: false,
				sourceMap: '<%= JS_PATH %>main.min.map',
				sourceMapRoot: '<%= JS_PATH %>',
				sourceMapPrefix: 2,
				sourceMappingURL: 'main.min.map'
				//preserveComments: 'some'
			},
			dist: {
				files: {
					'<%= JS_PATH %>main.min.js': ['<%= JS_PATH %>src/quizPool.js','<%= JS_PATH %>src/cancerPool.js','<%= JS_PATH %>src/main.js','<%= JS_PATH %>src/datePicker.js','<%= JS_PATH %>src/CyberChart.js']
				}
			}
		},
		copy: {
			dist: {
				files: [
					{expand:true, cwd:'<%= LIB_PATH %>underscore', src:'underscore-min.js', dest:'<%= JS_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>jquery', src:'jquery.min.js', dest:'<%= JS_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>bootstrap/dist', src:'**', dest:'<%= BASE_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>datePicker', src:'datePicker.js', dest:'<%= JS_PATH %>src/'},
					{expand:true, cwd:'<%= LIB_PATH %>cyberChart', src:'CyberChart.js', dest:'<%= JS_PATH %>src/'},
					{expand:true, cwd:'./src', src:'*.js', dest:'<%= JS_PATH %>src/'}
				]
			},
			js: {
				files: [
					{expand:true, cwd:'./src', src:'*.js', dest:'<%= JS_PATH %>src/'}
				]
			},
			less: {
				files: [
					{expand:true, cwd:'./src', src:'*.less', dest:'<%= LIB_PATH %>bootstrap/less/'}
				]
			}
		},
		watch: {
			scripts: {
				files: ['./src/*.js'],
				tasks: ['debug'],
				options: {
					spawn: true,
				}
			}
		},
		bump: {
			options: {
				files: ['package.json'],
				updateConfigs: [],
				commit: true,
				commitMessage: 'Release v%VERSION%',
				commitFiles: ['package.json'], // '-a' for all files
				createTag: true,
				tagName: 'v%VERSION%',
				tagMessage: 'Version %VERSION%',
				push: false,
				pushTo: 'master',
				gitDescribeOptions: '--tags --always --abbrev=1 --dirty=-d' // options to use with '$ git describe'
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	//grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-bump');

	grunt.registerTask('lib', ['copy:dist']);

	//grunt.registerTask('product', ['concat:dist','uglify','clean']);
	grunt.registerTask('product', ['lib','uglify']);
	grunt.registerTask('dev', ['copy:js','uglify']);

	grunt.registerTask('debug', ['copy:js','concat']);

//	grunt.registerTask('development', ['concat:dev']);

	grunt.registerTask('default', ['product']);

	grunt.registerTask('rebuild_bootstrap', function () {
	    var done = this.async();
	    grunt.util.spawn({
	        grunt: true,
	        args: ['dist'],
	        opts: {
	            cwd: 'bower_components/bootstrap'
	        }
	    }, function (err, result, code) {
	        done();
	    });
	});
	grunt.registerTask('bootstrap', ['copy:less','rebuild_bootstrap','copy:dist']);
};