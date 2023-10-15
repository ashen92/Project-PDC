const gulp = require("gulp");
const esbuild = require("esbuild");
const sass = require("gulp-sass")(require("sass"));
const autoprefixer = require("gulp-autoprefixer");

// Build task
gulp.task("build-js", function (done) {
    esbuild.build({
        entryPoints: ["./src/wwwroot/js/*.js"],
        bundle: true,
        minify: true,
        sourcemap: false,
        outdir: "./public/js",
    }).then(() => done()).catch(() => done("Build failed"));
});

// Watch task
gulp.task("watch-js", function () {
    gulp.watch("./src/wwwroot/js/*.js", gulp.series("build-js"));
});

gulp.task("scss", function () {
    return gulp.src("./src/wwwroot/scss/main.scss")
        .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
        .pipe(autoprefixer({
            overrideBrowserslist: [
                ">0.15%",
                "defaults",
                "not dead"
            ],
            cascade: false
        }))
        .pipe(gulp.dest("./public/css"));
});