/*!
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.21
 * @link       TBA
 */

var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var imagemin = require('gulp-imagemin');
var autoprefixer = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var plumber = require('gulp-plumber');

/**
 * Paths
 */
function paths (folder) {
    var devFolders = "./public/assets/";
    var prodFolders = "./public/themes/";
    var filePaths = {
        // these are the development folder
        back: {
            CSS: devFolders + folder + "/back/css",
            JS:  devFolders + folder + "/back/js",
            IMG: devFolders + folder + "/back/img"
        },
        front: {
            CSS: devFolders + folder + "/front/css",
            JS:  devFolders + folder + "/front/js",
            IMG: devFolders + folder + "/front/img"
        },
        // these are the production folders
        defaultThemeBack: {
            CSS: prodFolders + folder + "/back/css",
            JS:  prodFolders + folder + "/back/js",
            IMG: prodFolders + folder + "/back/img",
        },
        defaultThemeFront: {
            CSS: prodFolders + folder + "/front/css",
            JS:  prodFolders + folder + "/front/js",
            IMG: prodFolders + folder + "/front/img",
        }
    }

    // this is also a dev folder
    if (folder === "dev") {
        filePaths.common = {
            CSS: devFolders + folder + "/common/css",
            JS:  devFolders + folder + "/common/js",
            IMG: devFolders + folder + "/common/img"
        }
    }
    return filePaths;
}

/**
 * Front-end
 */
gulp.task('styles-f', function () {
    return gulp.src([
            paths("dev").front.CSS + "/*.css",
            paths("dev").common.CSS + "/*.css"
        ])
        .pipe(plumber({
            errorHandler: function (error) {
                console.error(error.message);
                this.emit('end');
            }}
        ))
        .pipe(sourcemaps.init())
            .pipe(autoprefixer('last 10 version'))
            .pipe(concat("front.min.css"))
            .pipe(minifycss({compatibility: 'ie8'}))
        .pipe(sourcemaps.write("./"))
        .pipe(plumber.stop())
        .pipe(gulp.dest(paths("default").defaultThemeFront.CSS));
});

gulp.task('scripts-f', function () {
    return gulp.src([
            paths("dev").common.JS + "/*.js",
            paths("dev").front.JS + "/*.js"
        ])
        .pipe(plumber({
            errorHandler: function (error) {
                console.error(error.message);
                this.emit('end');
            }}
        ))
        .pipe(sourcemaps.init({loadMaps: true}))
            .pipe(concat('front.min.js'))
            .pipe(uglify())
        .pipe(sourcemaps.write("./"))
        .pipe(plumber.stop())
        .pipe(gulp.dest(paths("default").defaultThemeFront.JS));
});

gulp.task('images-f', function () {
    return gulp.src([
            paths("dev").common.IMG + "/*",
            paths("dev").front.IMG + "/*",
        ])
        .pipe(plumber({
            errorHandler: function (error) {
                console.error(error.message);
                this.emit('end');
            }}
        ))
        .pipe(imagemin({
                optimizationLevel: 3,
                progressive: true,
                interlaced: true,
                multipass: true
            })
        )
        .pipe(plumber.stop())
        .pipe(gulp.dest(paths("default").defaultThemeFront.IMG));
});

/**
 * Back-end
 */
gulp.task('styles-b', function () {
    return gulp.src([
            paths("dev").back.CSS + "/*.css",
            paths("dev").common.CSS + "/*.css"
        ])
        .pipe(plumber({
            errorHandler: function (error) {
                console.error(error.message);
                this.emit('end');
            }}
        ))
        .pipe(sourcemaps.init())
            .pipe(autoprefixer({ browsers: ['last 10 version'] }))
            .pipe(concat("back.min.css"))
            .pipe(minifycss())
        .pipe(sourcemaps.write("./"))
        .pipe(plumber.stop())
        .pipe(gulp.dest(paths("default").defaultThemeBack.CSS));
});

gulp.task('scripts-b', function () {
    return gulp.src([
            paths("dev").common.JS + "/*.js",
            paths("dev").back.JS + "/*.js"
        ])
        .pipe(plumber({
            errorHandler: function (error) {
                console.error(error.message);
                this.emit('end');
            }}
        ))
        .pipe(sourcemaps.init({loadMaps: true}))
            .pipe(concat('back.min.js'))
            .pipe(uglify())
        .pipe(sourcemaps.write("./"))
        .pipe(plumber.stop())
        .pipe(gulp.dest(paths("default").defaultThemeBack.JS));
});

gulp.task('images-b', function () {
    return gulp.src([
            paths("dev").common.IMG + "/*",
            paths("dev").back.IMG + "/*",
        ])
        .pipe(plumber({
            errorHandler: function (error) {
                console.error(error.message);
                this.emit('end');
            }}
        ))
        .pipe(imagemin({
                optimizationLevel: 3,
                progressive: true,
                interlaced: true,
                multipass: true
            })
        )
        .pipe(plumber.stop())
        .pipe(gulp.dest(paths("default").defaultThemeBack.IMG));
});

/**
 * Watch Files For Changes
 */
gulp.task('watch', function () {
    gulp.watch(paths("dev").front.CSS + "/*.css", ['styles-f']);
    gulp.watch(paths("dev").front.JS  + "/*.js",  ['scripts-f']);
    gulp.watch(paths("dev").front.IMG + "/*",     ['images-f']);

    gulp.watch(paths("dev").common.CSS + "/*", ['styles-f', 'styles-b']);
    gulp.watch(paths("dev").common.JS + "/*",  ['scripts-f', 'scripts-b']);
    gulp.watch(paths("dev").common.IMG + "/*", ['images-f', 'images-b']);

    gulp.watch(paths("dev").back.CSS + "/*.css", ['styles-b']);
    gulp.watch(paths("dev").back.JS  + "/*.js",  ['scripts-b']);
    gulp.watch(paths("dev").back.IMG + "/*",     ['images-b']);
});

// Default Task
gulp.task('default', ['images-b', 'images-f', 'styles-f', 'styles-b', 'scripts-f', 'scripts-b', 'watch']);
