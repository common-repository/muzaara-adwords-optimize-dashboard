const path = require( "path" )

module.exports = {
	mode : "development",
	entry : "./index.js",
	output : {
		filename : "muzaara.js",
		path : path.resolve( __dirname, "../assets/js" ),
		publicPath : "assets"
	},
	module : {
		rules : [
			{
				test : /\.(js|jsx)?$/,
				loader : "babel-loader",
				options : {
					presets : ["@babel/env", "@babel/react"]
				}
			},
			{
				test : /\.css?$/,
				loader : ["style-loader","css-loader"]
			}
		]
	}
}