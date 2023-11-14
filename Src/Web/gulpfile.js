const gulp = require("gulp");
const esbuild = require("esbuild");
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
    gulp.watch("./App/src/wwwroot/js/*.js", gulp.series("build-js"));
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
    gulp.watch("./App/src/wwwroot/css/*.css", gulp.series("build-css"));
});

// Default task
gulp.task("default", gulp.parallel(gulp.series("build-js", "watch-js"), gulp.series("build-css", "watch-css")));