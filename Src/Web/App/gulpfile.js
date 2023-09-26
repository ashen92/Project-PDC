const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const esbuild = require("esbuild");

// Compile SCSS to minified CSS
gulp.task("scss", function () {
    return gulp.src("src/wwwroot/scss/*.scss")
        .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
        .pipe(gulp.dest("public/css"));
});

// Minify and Bundle JS
gulp.task("js", function (done) {
    esbuild.build({
        entryPoints: ["src/wwwroot/js/*.js"],
        bundle: true,
        minify: true,
        sourcemap: false,
        outdir: "public/js",
    }).then(() => done()).catch(() => done("Build failed"));
});

// Default task to run both SCSS and JS tasks
gulp.task("default", gulp.parallel("scss", "js"));
