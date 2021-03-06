'use strict';

module.exports = function (grunt) {

  //const acasdigital_frontend = './node_modules/@acas-digital-innovation/acas-frontend/';
  const acasdigital_frontend = './acas-frontend/';

  grunt.initConfig({
    shell: {
      all: {
        command: 'drush cache-clear theme-registry'
      }
    },
    
    watch: {
      options: {
        livereload: true
      },
      sass: {
        files: [acasdigital_frontend + 'scss/{,**/}*.{scss,sass}'],
        tasks: ['compass:dev'],
        options: {
          livereload: false
        }
      },
      registry: {
        files: ['*.info', '{,**}/*.{php,inc}'],
        tasks: ['shell'],
        options: {
          livereload: false
        }
      },
      images: {
        files: ['images/**']
      },
      css: {
        files: ['css/{,**/}*.css']
      },
      js: {
        files: [acasdigital_frontend + 'js/{,**/}*.js', '!js/{,**/}*.min.js'],
        tasks: ['jshint', 'uglify:dev']
      },
      livereload: {
        files: [
          'dist/css/{,**/}*.css',
          'dist/js/{,**/}*.js',
          'dist/images/{,**/}*.{png,jpg,jpeg,gif,webp,svg}'
        ]
      }
    },
    
    compass: {
      options: {
        config: 'config.rb',
        bundleExec: true,
        force: true
      },
      dev: {
        options: {
          environment: 'development'
        }
      },
      dist: {
        options: {
          environment: 'production'
        }
      }
    },



    copy: {
      dist: {
        files: [{
          expand: true,
          dot: true,
          cwd: acasdigital_frontend + 'assets/',
          dest: 'dist',
          src: [
            '*.{ico,png,txt}',
            'images/{,*/}*.{png,jpeg,jpg,gif,webp,svg}',
            'fonts/{,*/}*.{ttf,otf,woff,woff2,eot}'
          ]
        }]
      }
    },

    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: ['src/js/{,**/}*.js', '!js/{,**/}*.min.js']
    },

    sass: {
      dist: {
        files: [{
          expand: true,
          cwd: acasdigital_frontend + 'scss',
          dest: 'dist/css',
          src: [ '**/*.scss' ],
          ext: '.css'
        }]
      }
    },

    uglify: {
      dev: {
        options: {
          mangle: false,
          compress: false,
          beautify: true
        },
        files: [{
          expand: true,
          flatten: true,
          cwd: acasdigital_frontend + 'js',
          dest: 'dist/js',
          src: ['**/*.js', '!**/*.min.js'],
          rename: function(dest, src) {
            var folder = src.substring(0, src.lastIndexOf('/'));
            var filename = src.substring(src.lastIndexOf('/'), src.length);
            filename = filename.substring(0, filename.lastIndexOf('.'));
            return dest + '/' + folder + filename + '.min.js';
          }
        }]
      },
  	  jshint: {
  		  all: [
  			'Gruntfile.js',
  			acasdigital_frontend + '**/*.js',
  		  ],
  		  options: {
    			jshintrc: '.jshintrc',
    			jshintignore: '.jshintignore'
  		  }
  		},
      dist: {
        options: {
          mangle: true,
          compress: true
        },
        files: [{
          expand: true,
          flatten: true,
          cwd: acasdigital_frontend + 'js',
          dest: 'dist/js',
          src: ['**/*.js', '!**/*.min.js'],
          rename: function(dest, src) {
            var folder = src.substring(0, src.lastIndexOf('/'));
            var filename = src.substring(src.lastIndexOf('/'), src.length);
            filename = filename.substring(0, filename.lastIndexOf('.'));
            return dest + '/' + folder + filename + '.min.js';
          }
        }]
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-shell');

  grunt.registerTask('build', [
    'uglify:dev',
    'sass:dist',
    'copy:dist'
  ]);

};
