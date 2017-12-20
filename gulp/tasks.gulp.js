import plugins from './plugins.gulp';

var reload = plugins.bsync.reload;

module.exports = ((gulp, config) => {

    var hlp = require('./helpers.gulp.js');
    var exports = {};

    var _file_watch = (cb, src, dest, callback) => {
        var _dest = hlp.getDestPath(cb.path, src, dest);
        var name = hlp.isAsset(hlp.getFileName(cb.path));

        if (cb.event === "unlink") {
            plugins.del.sync(_dest + "/" + name);
            console.log("Eliminado:", _dest + "/" + name);
        } else {
            callback(cb.path, _dest);
            if (cb.event === 'add') {
                console.log("Agregado:", _dest + "/" + name);
            } else {
                console.log("Modificado:", _dest + "/" + name);
            }
        }
    };

    var _php = (src, dest) => {
        return gulp.src(src)
                .pipe(plugins.if(config.isProd, plugins.phpmin(config.phpmin)))
                .pipe(gulp.dest(dest))
                .pipe(plugins.if(!config.isProd, reload(config.bSyncReload)));
    };

    var _js = (src, dest) => {
        return gulp.src(src)
                .pipe(plugins.plumber())
                .pipe(plugins.if(config.isProd, plugins.uglify()))
                .pipe(plugins.rename(config.RenameAssets))
                .pipe(gulp.dest(dest))
                .pipe(plugins.if(!config.isProd, reload(config.bSyncReload)));
    };

    var _css = (src, dest) => {
        return gulp.src(src)
                .pipe(plugins.plumber())
                .pipe(plugins.if(config.isProd, plugins.cleancss({compatibility: 'ie8'})))
                .pipe(plugins.rename(config.RenameAssets))
                .pipe(gulp.dest(dest))
                .pipe(plugins.if(!config.isProd, reload(config.bSyncReload)));
    };
    
    var _json = (src, dest) => {
        return gulp.src(src)
                .pipe(gulp.dest(dest));
    };

    var js = () => {
        gulp.task('js', (cb) => _js(['./src/frontend/scripts/**/*.js'], './dist/web/static/js/'));
    };
    var css = () => {
        gulp.task('css', (cb) => _css(['./src/frontend/styles/**/*.css'], './dist/web/static/css/'));
    };

    var backend = () => {
        gulp.task('backend', (cb) => _php(['./src/backend/**/*.php'], './dist/'));
        gulp.task('json', (cb) => _json(['./src/backend/**/*.json'], './dist/'));
    };

    var frontend = () => {
        gulp.task('frontend', () => {
            return gulp.src(['./src/**/web/**/*', './src/**/web/**/.htaccess'])
                    .pipe(gulp.dest('./dist/'));
        });
    };

    var build = () => {
        if (!config.isProd) {
            gulp.task('build', () => {
                console.log("task build not running");
            });
        } else {
            gulp.task('build', ['backend', 'frontend']);
        }
    };

    var clean = () => {
        gulp.task('clean', () => {
            if (!config.isProd)
                return false;

            return gulp.src(['./dist/*', '!./dist/vendor', '!./dist/runtime', '!./dist/web/assets'], {read: false})
                    .pipe(plugins.clean());
        });
    };

    var watch = () => {
        if (config.isProd) {
            gulp.task('watch', () => {
                console.log("task watch not running");
            });
        } else {
            gulp.task('watch', () => {
                plugins.watch(['./src/backend/**/*.php'], (cb) => {
                    _file_watch(cb, '/src/backend', './dist/', (src, dest) => {
                        _php(src, dest);
                    });
                });

                plugins.watch(['./src/frontend/scripts/**/*.js'], (cb) => {
                    _file_watch(cb, '/src/frontend/scripts/', './dist/web/static/js/', (src, dest) => {
                        _js(src, dest);
                    });
                });

                plugins.watch(['./src/frontend/styles/**/*.css'], (cb) => {
                    _file_watch(cb, '/src/frontend/styles/', './dist/web/static/css/', (src, dest) => {
                        _css(src, dest);
                    });
                });
               
            });
        }
    };

    exports.init = () => {
        backend();
        frontend();
        clean();
        build();
        js();
        css();
        watch();
    };
    return exports;
});
