/*
 * Load Plugins
 */
var gulp = ( require( 'gulp' ) ),
    exec = require('gulp-exec'),
    rsync = require('gulp-rsync'),
    fs = require('fs'),
    del = require('del');

const { exec: childExec } = require('child_process');


/**
 * Copy theme folder outside in a build folder, recreate styles before that
 */
function copyFolder(cb) {
    var dir = process.cwd();
    return gulp.src('./*')
        .pipe(exec('rm -Rf ./../build; mkdir -p ./../build/pixtypes;', {
            silent: true,
            continueOnError: true // default: false
        }))
        .pipe(rsync({
            root: dir,
            destination: '../build/pixtypes/',
            // archive: true,
            progress: false,
            silent: false,
            compress: false,
            recursive: true,
            emptyDirectories: true,
            clean: true,
            exclude: ['node_modules']
        }));
}

/**
 * Clean the folder of unneeded files and folders
 */
function build() {
    // files that should not be present in build zip
    files_to_remove = [
		'**/codekit-config.json',
		'node_modules',
		'config.rb',
		'gulpfile.js',
		'package.json',
        'package-lock.json',
		'wpgrade-core/vendor/redux2',
		'wpgrade-core/features',
		'wpgrade-core/tests',
		'wpgrade-core/**/*.less',
		'wpgrade-core/**/*.scss',
		'wpgrade-core/**/*.rb',
		'wpgrade-core/**/sass',
		'wpgrade-core/**/scss',
		'pxg.json',
		'build',
        '.idea',
        '.editorconfig',
        '**/.svn*',
        '**/*.css.map',
        '**/.sass*',
        '.sass*',
        '**/.git*',
        '*.sublime-project',
        '.DS_Store',
        '**/.DS_Store',
        '__MACOSX',
        '**/__MACOSX',
        'README.md',
        '.csscomb',
        '.csscomb.json',
        '.codeclimate.yml',
        'tests',
        'circle.yml',
        '.circleci',
        '.labels',
        '.jscsrc',
        '.jshintignore',
        'browserslist'
    ];

    files_to_remove.forEach(function(e, k) {
        files_to_remove[k] = '../build/pixtypes/' + e;
    });

    return del(files_to_remove, { force: true }); // Using the force option here
}

/**
 * Create a zip archive out of the cleaned folder and delete the folder
 */
function zip(cb) {
    childExec('cd ./../; rm -rf pixtypes.zip; cd ./build/; zip -r -X ./../pixtypes.zip ./pixtypes; cd ./../; rm -rf build', function(err, stdout, stderr) {
        if (err) {
            console.error('Error executing zip command:', err);
            cb(err);  // Signal completion with an error if there's an issue.
            return;
        }
        console.log(stdout);
        cb();  // Signal completion when done.
    });
}


// Now, we set up our tasks for Gulp v4:
gulp.task('copy-folder', copyFolder);
gulp.task('build', gulp.series(copyFolder, build));
gulp.task('zip', gulp.series(copyFolder, build, zip));
