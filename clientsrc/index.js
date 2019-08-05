import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import TriggerEditor from './TriggerEditor';
import { CookiesProvider } from 'react-cookie';
let element = document.getElementById( 'flow-editor-container' );

let json = {
  'nodes': {
  },
  'connections': []
};

if ( null != document.getElementById( 'flow-editor-data-source' ) ) {
	try {
		if ( 0 < document.getElementById( 'flow-editor-data-source' ).innerText.length ) {
			json = JSON.parse( document.getElementById( 'flow-editor-data-source' ).innerText );
		}
	} catch ( e ) {
		window.attemptedJson = element.innerText;
	}
	let field = document.getElementById( 'flow-editor-data' );

	ReactDOM.render(
		<CookiesProvider>
			<App graph={json} linkedField={field} />
		</CookiesProvider>,
		element
	);
}


if ( null != document.getElementById( 'triggerhappy-trigger-data-source' ) ) {
	let element = document.getElementById( 'triggerhappy-trigger-editor-container' );
	try {
		if ( 0 < document.getElementById( 'triggerhappy-trigger-data-source' ).innerText.length ) {
			json = JSON.parse( document.getElementById( 'triggerhappy-trigger-data-source' ).innerText );
		}
	} catch ( e ) {
		window.attemptedJson = element.innerText;
	}
	let field = document.getElementById( 'triggerhappy-trigger-data' );

	ReactDOM.render(
		<CookiesProvider>
			<TriggerEditor graph={json} linkedField={field} />
		</CookiesProvider>,
		element
	);
}
