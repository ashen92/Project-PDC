const gulp = require("gulp");
const esbuild = require("esbuild");

// Build task
gulp.task("build-js", function (done) {
    esbuild.build({
        entryPoints: ["/src/wwwroot/js/*.js"],
        bundle: true,
        minify: false,
        sourcemap: true,
        outdir: "/public/js",
    }).then(() => done()).catch(() => done("Build failed"));
});

// Watch task
gulp.task("watch-js", function () {
    gulp.watch("/src/wwwroot/js/*.js", gulp.series("build-js"));
});

// Default task
gulp.task("default", gulp.series("build-js", "watch-js"));