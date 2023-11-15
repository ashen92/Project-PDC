const gulp = require("gulp");
const esbuild = require("esbuild");
const sass = require("gulp-sass")(require("sass"));
const autoprefixer = require("gulp-autoprefixer");
const sourcemaps = require('gulp-sourcemaps');
const cleanCSS = require("gulp-clean-css");
const rename = require("gulp-rename");

// JavaScript tasks

gulp.task("build-js", function (done) {
    esbuild.build({
        entryPoints: ["./App/src/wwwroot/js/*.js"],
        bundle: true,
        minify: false,
        sourcemap: true,
        outdir: "./App/public/js",
    }).then(() => done()).catch(() => done("Build failed"));
});

gulp.task("watch-js", function () {
    gulp.watch("./App/src/wwwroot/js/**/*", gulp.series("build-js"));
});

// CSS tasks

gulp.task("build-css", () => {
    return gulp.src("./App/src/wwwroot/css/*.css")
        .pipe(cleanCSS({ compatibility: "ie8" }))
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(gulp.dest("./App/public/css"));
});

gulp.task("watch-css", function () {
    gulp.watch("./App/src/wwwroot/css/**/*", gulp.series("build-css"));
});

// SCSS tasks

gulp.task("build-scss", function () {
    return gulp.src("./App/src/wwwroot/scss/main.scss")
        .pipe(sourcemaps.init())
        .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
        .pipe(autoprefixer({
            overrideBrowserslist: [
                ">0.15%",
                "defaults",
                "not dead"
            ],
            cascade: false
        }))
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest("./App/public/css"));
});

gulp.task("watch-scss", function () {
    gulp.watch("./App/src/wwwroot/scss/**/*", gulp.series("build-scss"));
});

// Default task
gulp.task(
    "default",
    gulp.parallel(
        gulp.series("build-js", "watch-js"),
        gulp.series("build-css", "watch-css"),
        gulp.series("build-scss", "watch-scss")
    ));