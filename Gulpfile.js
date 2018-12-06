var gulp = require('gulp');
var cssmin = require('gulp-mini-css');
var jsHint = require('gulp-jshint');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var pump = require('pump');
var plumber = require('gulp-plumber');
var watch = require('gulp-watch');
var rename = require('gulp-rename');
var obfuscate = require('gulp-javascript-obfuscator');
var fs = require('fs');
var path = require('path');

//Task for CSS
gulp.task('css-default', function () {
    return gulp.src('public/assets/css/pages/*.css')
        .pipe(cssmin({ext: '.min.css'}))
        .pipe(gulp.dest('public/assets/css/dist/min'))
        .pipe(concat('cosmo.min.css'))
        .pipe(gulp.dest('public/assets/css/dist'));
});

//Task for javascript
gulp.task('javascript-pages', function () {
    var srcpath = 'public/assets/js/pages/';
    var directory = (fs.readdirSync(srcpath)
            .filter(file => fs.statSync(path.join(srcpath, file)).isDirectory()));

    directory.forEach(function(item) {
        gulp.src([ srcpath + item + '/*.js'  ])
            .pipe(plumber())
            .pipe(jsHint())
            .pipe(jsHint.reporter('default'))
            .pipe(uglify())
            .pipe(concat(item + '.min.js'))
            // .pipe(obfuscate())
            .pipe(plumber.stop())
            .pipe(gulp.dest(srcpath + item + '/dist'));
    });
});

gulp.task('javascript-service', function(cb) {
    pump([
        gulp.src(['public/assets/js/service/*.js']),
        jsHint(),
        jsHint.reporter('default'),
        uglify(),
        rename({ suffix: '.min' }),
        gulp.dest('public/assets/js/service/min', { overwrite: true }),
        concat('service.cosmo.min.js'),
        gulp.dest('public/assets/js/service/dist', { overwrite: true })
    ], cb);
});

//Gulp Watch
gulp.task('watch-default', function(){
    gulp.watch('public/assets/css/pages/*.css', ['css-default']);
    gulp.watch([ 'public/assets/js/pages/*js/*.js' ], ['javascript-pages']);
    gulp.watch(['public/assets/js/service/*.js'], ['javascript-service']);
});

