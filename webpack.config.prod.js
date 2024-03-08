const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const fs = require ('fs');

 var config = {
	mode:"production",
	//devtool: 'source-map',
	module: {
		rules: [
			{
		        test: /\.js$/,
		        exclude: /(node_modules|bower_components)/,
		        loader: 'babel-loader',
		        query: {
		            presets: ['@babel/preset-env']
		        }
			},
			{
				test: /\.(vue)$/,
				loader: 'vue-loader'
			},
			{
				test: /\.css$/,
				use: [
					'vue-style-loader',
					'css-loader'
				]
			}
		]
	}, 
	resolve: {
		alias: {
	    	'vue$': 'vue/dist/vue.esm.js'
		}
	},
    plugins: [
		new VueLoaderPlugin()
    ]
};

var configOpac = Object.assign({}, config, {
    entry() {
		var entries = {};
		let opacDirectories = fs.readdirSync('./opac_css/includes/templates/vuejs/');
		for(let opacDir in opacDirectories){
			let opacViews = fs.readdirSync('./opac_css/includes/templates/vuejs/'+ opacDirectories[opacDir]);
			for(let opacView in opacViews){
				if(fs.existsSync('./opac_css/includes/templates/vuejs/'+opacDirectories[opacDir]+"/"+opacViews[opacView]+"/"+opacViews[opacView]+'.js')){
					entries[opacDirectories[opacDir]+"/"+opacViews[opacView]] = './opac_css/includes/templates/vuejs/'+opacDirectories[opacDir]+"/"+opacViews[opacView]+"/"+opacViews[opacView]+'.js';
				}
			}
		}
		return entries;
	},
	output: {
		path: path.resolve( __dirname, './opac_css/includes/javascript/vuejs' ),
		filename: '[name].js'
	}
});

var configGestion = Object.assign({}, config, {
    entry() {
		var entries = {};
		let directories = fs.readdirSync('./includes/templates/vuejs/');
		for(let dir in directories){
			let views = fs.readdirSync('./includes/templates/vuejs/'+ directories[dir]);
			for(let view in views){
				if(fs.existsSync('./includes/templates/vuejs/'+directories[dir]+"/"+views[view]+"/"+views[view]+'.js')){
					entries[directories[dir]+"/"+views[view]] = './includes/templates/vuejs/'+directories[dir]+"/"+views[view]+"/"+views[view]+'.js';
				}
			}
		}
		return entries;
	},
	output: {
		path: path.resolve( __dirname, './javascript/vuejs' ),
		filename: '[name].js'
	}
});

module.exports = [configGestion, configOpac];
