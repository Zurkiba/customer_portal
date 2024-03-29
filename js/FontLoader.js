import { Font } from '../extras/core/Font.js';
import { FileLoader } from './FileLoader.js';
import { DefaultLoadingManager } from './LoadingManager.js';

/**
 * @author mrdoob / http://mrdoob.com/
 */

function FontLoader( manager ) {

	this.manager = ( manager !== undefined ) ? manager : DefaultLoadingManager;

}

Object.assign( FontLoader.prototype, {

	load: function ( url, onLoad, onProgress, onError ) {

		var scope = this;

		var loader = new FileLoader( this.manager );
		loader.setPath( this.path );
		loader.load( url, function ( text ) {

			var json;

			try {

				json = JSON.parse( text );

			} catch ( e ) {

				console.warn( 'THREE.FontLoader: typeface.js support is being deprecated. Use typeface.json instead.' );
				json = JSON.parse( text.substring( 65, text.length - 2 ) );

			}

			var font = scope.parse( json );

			if ( onLoad ) onLoad( font );

		}, onProgress, onError );

	},

	parse: function ( json ) {

		return new Font( json );

	},

	setPath: function ( value ) {

		this.path = value;
		return this;

	}

} );


export { FontLoader };