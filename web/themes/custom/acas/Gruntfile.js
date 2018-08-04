'use strict';

module.exports = function (grunt) {

  grunt.initConfig({
    watch: {
      options: {
        livereload: true
      },
      sass: {
        files: ['src/acas-frontend/{,**/}*.{scss,sass}', 'src/bootstrap/assets/stylesheets/bootstrap/*.scss'],
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
        files: ['src/js/{,**/}*.js', '!js/{,**/}*.min.js'],

      }
    },

    shell: {
      all: {
        command: 'drush cache-clear theme-registry'
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
          cwd: 'src',
          dest: 'dist',
          src: [
            '*.{ico,png,txt}',
            'images/{,*/}*.{png,jpeg,jpg,gif,webp,svg}',
            'fonts/{,*/}*.{ttf,otf,woff,eot}'
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
          cwd: 'src/js',
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
  			'src/**/*.js',
  			'test/**/*.js',
  			'!test/mocha.js',
  			'!test/expect.js'
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
          cwd: 'src/js',
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
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-shell');

  grunt.registerTask('build', [
    'uglify:dev',
    'compass:dev',
    'copy:dist'
  ]);

};
