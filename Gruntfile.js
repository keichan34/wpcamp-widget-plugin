module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // Compile steps
    // = CoffeeScript => JavaScript
    coffee: {
      glob_to_multiple: {
        expand: true,
        flatten: true,
        cwd: 'js/src',
        src: ['*.js.coffee'],
        dest: 'js/src',
        ext: '.js'
      }
    },
    // SCSS => CSS
    sass: {
      dist: {
        options: {
          style: 'expanded'
        },
        files: [
          {
            expand: true,
            cwd: 'css/src',
            src: ['*.scss'],
            dest: 'css',
            ext: '.css'
          }
        ]
      }
    },
    // Compression steps
    // = JavaScript minify
    uglify: {
      dist: {
        files: [
          {
            expand: true,
            cwd: 'js/src',
            src: ['*.js'],
            dest: 'js',
            ext: '.min.js'
          }
        ]
      }
    },
    // = CSS Minify
    cssmin: {
      dist: {
        files: [
          {
            expand: true,
            cwd: 'css',
            src: ['*.css', '!*.min.css'],
            dest: 'css',
            ext: '.min.css'
          }
        ]
      }
    },
    // Watch for file changes
    watch: {
      scripts: {
        files: ['js/src/*.js.coffee'],
        tasks: ['newer:coffee', 'newer:uglify']
      },
      styles: {
        files: ['css/src/*.scss'],
        tasks: ['newer:sass', 'newer:cssmin']
      },
      templates: {
        files: ['*.slim'],
        tasks: ['newer:slim']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-coffee');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-newer');

  grunt.registerTask('default', ['watch']);

};
