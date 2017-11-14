import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import { CookiesProvider } from 'react-cookie';
let element = document.getElementById( 'flow-editor-container' );

let json = {
  'nodes': {
  },
  'connections': []
};
try {
	if ( document.getElementById( 'flow-editor-data-source' ).innerText.length > 0) {
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
