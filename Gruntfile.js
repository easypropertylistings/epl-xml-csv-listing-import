/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig({

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'languages',
				potHeaders: {
					'report-msgid-bugs-to' : 'https://github.com/easypropertylistings/epl-xml-csv-listing-import/issues',
					'last-translator' : 'Merv Barrett <support@easypropertylistings.com.au>',
					'language-team' : 'Real Estate Connected <support@realestateconnected.com.au>',
					'Plural-Forms': 'nplurals=2; plural=(n > 1);',
					'X-Poedit-SourceCharset' : 'UTF-8',
					'X-Poedit-KeywordsList' : '__;_e;_x;_ex;_n',
					'X-Poedit-Basepath' : '..',
					'X-Poedit-SearchPath-0' : '.',
					'X-Poedit-SearchPathExcluded-0' : 'node_modules',
					'X-Poedit-SearchPathExcluded-1' : 'epl-apidocs',
					'X-Poedit-SearchPathExcluded-2' : 'apigen',
					'X-Poedit-SearchPathExcluded-3' : 'Gruntfile.js',
					'X-Poedit-SearchPathExcluded-4' : 'apigen.neon',
					'X-Poedit-SearchPathExcluded-5' : 'package.json',
					'X-Poedit-SearchPathExcluded-6' : 'README.md'
				}
			},
			dist: {
				options: {
					potFilename: 'epl-wpimport.pot',
					exclude: [
						'apigen/.*',
						'tests/.*',
						'tmp/.*'
					]
				}
			}
		}
	});

	grunt.loadNpmTasks( 'grunt-wp-i18n' );

	// Register tasks.
	grunt.registerTask( 'default', [
		'makepot'
	]);

	grunt.registerTask( 'dev', [
		'makepot'
	]);

};