/**
 * MIT License.
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.14
 * @link       TBA
 */

var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
//var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');
var imagemin = require('gulp-imagemin');
var autoprefixer = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var watch = require('gulp-watch');
//var cache = require('gulp-cache');
var plumber = require('gulp-plumber');

/**
 * Paths
 */
function paths (folder) {
    var assets = "./public/assets/";
    var paths = {
        back: {
            CSS: assets + folder + "/back/css",
            JS:  assets + folder + "/back/js",
            IMG: assets + folder + "/back/img"
        },
        front: {
            CSS: assets + folder + "/front/css",
            JS:  assets + folder + "/front/js",
            IMG: assets + folder + "/front/img"
        }
    }

    if (folder === "dev") {
        paths.common = {
            CSS: assets + folder + "/common/css",
            JS:  assets + folder + "/common/js",
            IMG: assets + folder + "/common/img"
        }
    }
    return paths;
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
        .pipe(gulp.dest(paths("prod").front.CSS));
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
        .pipe(gulp.dest(paths("prod").front.JS));
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
        .pipe(gulp.dest(paths("prod").front.IMG));
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
        .pipe(gulp.dest(paths("prod").back.CSS));
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
        .pipe(gulp.dest(paths("prod").back.JS));
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
        .pipe(gulp.dest(paths("prod").back.IMG));
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
