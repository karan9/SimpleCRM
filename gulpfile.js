var gulp = require('gulp');
var sass = require('gulp-sass');
var pug = require('gulp-pug');
var uglify = require('gulp-uglify');
var minify = require('gulp-minify');
var imagemin = require('gulp-imagemin');
var webserver = require('gulp-webserver');


// paths
var paths = {
    pug: {
        src: './source/pug/**/**/*.pug',
        dest: './public/'
    },
    styles: {
        src: "./source/sass/**/**/*.sass",
        dest: "./public/css/" 
    },
    scripts : {
        src: './source/js/**/**/*.js',
        dest: './public/js/',
    },
    php: {
        src: './source/php/**/**/*.php',
        dest: './public/php/'
    },
    assets: {
        src: './source/img/**/**/*.*',
        dest: './public/img/'
    },
    webserver: {
        dest: './public/'
    }
}


// compile pug
gulp.task('pug-compile', function() {
    return gulp.src(paths.pug.src)
        .pipe(pug())
        .pipe(gulp.dest(paths.pug.dest));
});

// compile sass
gulp.task('sass-compile', function() {
    return gulp.src(paths.styles.src)
        .pipe(sass().on('error', function(err) {
            console.log(err);
        }))
        .pipe(gulp.dest(paths.styles.dest))
});

// compile js
gulp.task('js-copy', function() {
    return gulp.src(paths.scripts.src)
        .pipe(uglify())
        .pipe(minify())
        .pipe(gulp.dest(paths.scripts.dest)); 
});

// compile php
gulp.task('php-copy', function() {
    return gulp.src(paths.php.src)
        .pipe(gulp.dest(paths.php.dest));
});

// compile assets
gulp.task('img-copy', function() {
    return gulp.src(paths.assets.src)
        .pipe(imagemin())
        .pipe(gulp.dest(paths.assets.dest));
});

// websever instance
gulp.task('webserver', function () {
  return gulp.src(paths.webserver.dest)
    .pipe(webserver({
      livereload: true,
      port: 8003
    }));
});

// init deh tasks
gulp.task('init', function() {
    return gulp.start(
        'pug-compile', 
        'sass-compile', 
        'js-copy', 
        'php-copy', 
        'img-copy'
    );
});

//let's start watching
gulp.task('lookup', function() {
      gulp.watch(paths.styles.src, ['sass-compile']);
      gulp.watch(paths.pug.src, ['pug-compile']);
      gulp.watch(paths.scripts.src, ['js-copy']);
      gulp.watch(paths.assets.src, ['img-copy']);
      gulp.watch(paths.php.src, ['php-copy']);
});

gulp.task('default', function() {
  return gulp.start('webserver', 'lookup');
});