const mix = require( 'laravel-mix' );
const wpPot = require( 'wp-pot' );
const { default: ImageminPlugin } = require( 'imagemin-webpack-plugin' );
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );
const path = require( 'path' );



mix
	.setPublicPath( 'assets/dist' )
	.sourceMaps( false )
	// Admin
	.js( 'assets/src/js/admin-main.js', 'js/wpbr-admin-main.js' )
	.js( 'assets/src/js/system-info.js', 'js/wpbr-system-info.js' )
	.js( 'assets/src/js/blocks.js', 'js/wpbr-blocks.js' )
	.sass( 'assets/src/css/admin-main.scss', 'css/wpbr-admin-main.css' )
	// Public
	.js( 'assets/src/js/public-main.js', 'js/wpbr-public-main.js' )
	.sass( 'assets/src/css/public-main.scss', 'css/wpbr-public-main.css' )
	// Images
	.copy("assets/src/images", "assets/dist/images")
	.options({
		processCssUrls: false
	});


mix.webpackConfig({
	externals: {
		$: 'jQuery',
		jquery: 'jQuery',
		lodash: 'lodash',
	},
	plugins: [
		new CleanWebpackPlugin(),
		new ImageminPlugin({
			test: /\.(jpe?g|png|gif|svg)$/i,
			disable: ! mix.inProduction()
		})
	],
    resolve: {
        alias: {
            '@wpbr/js': path.resolve(__dirname, 'assets/src/js/'),
            '@wpbr/blocks': path.resolve(__dirname, 'includes/blocks/'),
            '@wpbr/components': path.resolve(__dirname, 'includes/blocks/components/'),
        },
    },
});

if ( mix.inProduction() ) {
	wpPot({
		package: 'WP Business Reviews',
		domain: 'wp-business-reviews',
		destFile: 'languages/wp-business-reviews.pot',
		relativeTo: './',
		src: 'includes/**/*.php',
		bugReport: 'https://wpbusinessreviews.com/support',
		team: 'WP Business Reviews <info@wpbusinessreviews.com>'
	});
}
