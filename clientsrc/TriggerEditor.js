import React, { Component } from 'react';
import { Provider } from 'react-redux';
import { createStore, applyMiddleware, compose } from 'redux';
import store from '../jsclient/triggerStore/';
import  { loadDataType } from '../jsclient/actions';
import {parseExpression, stringifyExpression} from '../jsclient/lib/util';
import {fetchPlugins, setup} from '../jsclient/actions/';
import thunk from 'redux-thunk';


// import ReactNodeGraph from 'react-node-graph';

import Components from '../jsclient/components';

export default class TriggerEditor extends Component {

	constructor( props ) {
		super( props );
		this.state = {errors: '' };

		const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

		let defaultStore = {fields: [],
			datatypes: {
				'boolean': {
					base: null,
					choices: [
						{ id: false, text: 'No' },
						{ id: true, text: 'Yes' }
					],
					ajax: false
				},
				'string': {
					base: null,
					schema: {
						methods: {
							'toUpperCase': {
								description: 'To Upper Case',
								'type': 'string',
								'fields': { }
							},
							'toLowerCase': {
								description: 'To Lower Case',
								'type': 'string',
								'fields': { }
							}
						}
					}
				},
				'array': {
					base: null,
					schema: {
						methods: {
							'getItem': {
								description: 'Get Item',
								'type': 'number',
								'fields': {
									'key': {
										'type': 'string',
										'description': 'The item key'
									}
								}
							}
						}
					}
				}
			}
		};
		this.nodeStore = createStore( store, defaultStore, composeEnhancers( applyMiddleware( thunk ) ) );


	}

	componentDidMount() {
	//	this.nodeStore.subscribe( this.saveGraph.bind( this ) );
	}
	render() {
		let TriggerFields = Components.TriggerFields;
		return (
			<Provider store={this.nodeStore}>
				<TriggerFields
					data={this.state}
				/>
			</Provider>
		);
	}
}
