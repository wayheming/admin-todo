'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const { series } = require('gulp');
const minify = require('gulp-minify');
const rename = require('gulp-rename');


function buildStyles() {
	return gulp.src('./assets/sass/*.scss')
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(rename('styles.min.css'))
		.pipe(gulp.dest('./assets/css/'));
}

function buildScripts() {
	return gulp.src(['./assets/js/*.js', '!./assets/js/*.min.js'])
		.pipe(minify(
			{
				ext: {
					min: '.min.js',
				}
			}
		))
		.pipe(gulp.dest('./assets/js'))
}

exports.build = series(buildStyles, buildScripts);
