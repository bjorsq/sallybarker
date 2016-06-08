var gulp = require('gulp');

var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var cleanCSS = require('gulp-clean-css');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var header = require('gulp-header');
var fs = require('fs');

var pkg = JSON.parse(fs.readFileSync('./package.json'));

var cssbanner = ['/*',
  'Theme Name:          <%= pkg.themename %>',
  'Description:         <%= pkg.description %>',
  'Author:              <%= pkg.author %>',
  'Version:             <%= pkg.version %>',
  'Theme URI:           <%= pkg.homepage %>',
  'Bitbucket Theme URI: <%= pkg.homepage %>',
  'License:             <%= pkg.licensename %>',
  'License URI:         <%= pkg.licenseuri %>',
  '*/',
  ''].join('\n');

// Lint Task
gulp.task('lint', function() {
    return gulp.src('js/src/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// Compile Our Sass
gulp.task('sass', function() {
    return gulp.src('scss/*.scss')
        .pipe(sass({includePaths:[
        	'./sass',
        	'./bower_components/foundation-sites/scss',
        	'./bower_components/motion-ui/src'
        ]}))
        .pipe(gulp.dest('css'));
});

// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src(['js/src/*.js'])
        .pipe(concat('all.js'))
        .pipe(gulp.dest('js'))
        .pipe(rename('all.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('js'));
});

gulp.task('stylecss', function() {
	return gulp.src('css/style.css')
	    .pipe(sourcemaps.init())
        .pipe(cleanCSS())
        .pipe(sourcemaps.write('css'))
		.pipe(header(cssbanner, { pkg : pkg } ))
		.pipe(gulp.dest('./'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('js/src/*.js', ['lint', 'scripts']);
    gulp.watch('scss/**/*.scss', ['sass', 'stylecss']);
    gulp.watch('package.json', ['stylecss'])
});

// Default Task
gulp.task('default', ['lint', 'sass', 'stylecss', 'scripts', 'watch']);