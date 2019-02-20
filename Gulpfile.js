var gulp        = require('gulp');
var sass        = require('gulp-sass');
var rename      = require('gulp-rename');
var uglify      = require('gulp-uglify');
var cleanCSS    = require('gulp-clean-css');
var gp_concat   = require('gulp-concat');
var browserSync = require('browser-sync').create();
var imagemin    = require('gulp-imagemin'); 
var uglifycss = require('gulp-uglifycss');

 
/*
Sass Issue?
npm uninstall --save-dev gulp-sass
npm install --save-dev gulp-sass@2
*/


// Images
gulp.task('images', function() {
    gulp.src('htdocs/src/img/*')
         .pipe(imagemin())
        .pipe(gulp.dest('htdocs/img/'))
});

// Styles
gulp.task('styles', function() {
    gulp.src('htdocs/src/sass/main.scss')
        .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
        .pipe(sass().on('error', sass.logError))
        .pipe(uglifycss().on('error', function(e){
            console.log('uglify ' + e);
         }))
        .pipe(gulp.dest('htdocs/css/'))
});

// Scripts
gulp.task('scripts', function() {
  return gulp.src([
         'htdocs/src/js/framework/jquery-1.11.3.js',
         'htdocs/src/js/framework/bootstrap.js',
			   'htdocs/src/js/plugins/**/*.js',
			   'htdocs/src/js/action/**/*.js',
			   'htdocs/src/js/main.js' 
  		])
  		.pipe(gp_concat('main.js').on('error', function(e){
            console.log('CONCAT MAIN' + e);
         }))
        .pipe(gulp.dest('htdocs/js')) 
        .pipe(uglify().on('error', function(e){
            console.log('UGLIFY MAIN' + e);
         }))
        .pipe(gulp.dest('htdocs/js'));
});

 


// Scripts
gulp.task('admin_scripts', function() {
  return gulp.src([
         'htdocs/src/js/framework/jquery-1.11.3.js',
         'htdocs/src/js/framework/bootstrap.js',
         'htdocs/src/js/plugins/**/*.js',
         'htdocs/src/js/action/**/*.js',
         'htdocs/src/js/main.js',
         'htdocs/src/js/admin/**/*.js',
  		])
  		.pipe(gp_concat('admin.js').on('error', function(e){
            console.log('CONCAT ADMIN' + e);
         }))
        .pipe(gulp.dest('htdocs/js')) 
        /*
        .pipe(uglify().on('error', function(e){
            console.log('UGLIFY ADMIN' + e);
         }))
            */
        .pipe(gulp.dest('htdocs/js'));
});



//Watch task
gulp.task('default',function() {
    browserSync.init({
        proxy: "dev.imc2019.vm"
    });
    // ,"application/**/**/**/**/**/*.php","application/**/**/**/**/**/*.html"
	//gulp.watch(["htdocs/**/*.php"] ).on('change', browserSync.reload);
    gulp.watch('htdocs/src/sass/**/*.scss',['styles']).on('change', browserSync.reload);
    gulp.watch('htdocs/src/js/**/**/**/*.js',['scripts']).on('change', browserSync.reload);
    gulp.watch(['htdocs/src/js/admin/**/**/*.js','htdocs/src/js/**/**/**/*.js'],['admin_scripts']).on('change', browserSync.reload);
    gulp.watch('htdocs/src/img/*',['scripts']).on('images', browserSync.reload);
    //gulp.watch('htdocs/src/js/facebook/*.js',['fb-scripts']).on('change', browserSync.reload);
	//gulp.watch('htdocs/src/js/admin/**/*.js',['admin-scripts']).on('change', browserSync.reload);
});