const { defineConfig } = require( 'cypress' );

module.exports = defineConfig( {
	fixturesFolder: 'tests/cypress/fixtures',
	screenshotsFolder: 'tests/cypress/screenshots',
	videosFolder: 'tests/cypress/videos',
	downloadsFolder: 'tests/cypress/downloads',
	supportFile: 'tests/cypress/support/e2e.js',
	video: true,
	viewportWidth: 1200,
	viewportHeight: 660,
	requestTimeout: 900000,
	responseTimeout: 900000,
	env: {},
} );
