import React, { Component } from 'react';
import { Provider } from 'react-redux';
import { createStore, applyMiddleware, compose } from 'redux';
import store from '../jsclient/store/';
import  { loadDataType} from '../jsclient/actions';
import {parseExpression, stringifyExpression} from '../jsclient/lib/util';
import {fetchPlugins, setup} from '../jsclient/actions/';
import thunk from 'redux-thunk';


// import ReactNodeGraph from 'react-node-graph';

import ReactNodeGraph from '../jsclient/';

export default class App extends Component {

	constructor( props ) {
		super( props );
		this.state = {errors: '' };
		let graph = this.stringifyExpressionsInGraph( this.props.graph );

		const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;
		let loadedNodes = graph.nodes;
		this.state = graph;

		let nodeStore = createStore( store, {
			fieldTypes: graph.fieldTypes,
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
		}, composeEnhancers( applyMiddleware( thunk ) ) );

		nodeStore.dispatch( fetchPlugins() );

		this.nodeStore = nodeStore;
		window.TH.nodeStore = nodeStore;
		this.saveGraph();
	}
	stringifyExpressionsInGraph( graph ) {
		for ( let g in graph.nodes ) {
			for ( let e in graph.nodes[g].expressions ) {
				let expr = graph.nodes[g].expressions[e];
				graph.nodes[g].expressions[e] = stringifyExpression( expr );
			}
			graph.nodes[g].filters = this.stringifyFilters( graph.nodes[g].filters );
		}
		return graph;
	}
	parseExpression( nodeId, fieldName, expression, fieldType ) {

		let oType = fieldType;

		while  ( 'number' !== fieldType && 'boolean' !== fieldType && 'string' !== fieldType && 'html' !== fieldType ) {
			if ( ! this.nodeStore.getState().datatypes[fieldType]) {
				this.nodeStore.dispatch( loadDataType( fieldType ) );
				break;
			} else {
				if ( ! this.nodeStore.getState().datatypes[fieldType].base ) {
					break;
				}
				fieldType = this.nodeStore.getState().datatypes[fieldType].base;
			}
		}

		if ( 'string' !== fieldType && 'boolean' !== fieldType && 'html' !== fieldType ) {
			if ( 'number' == fieldType ) {
				if ( ! isNaN( parseFloat( expression ) ) && isFinite( expression ) ) {
					return expression;
				}
			}
			return parseExpression( expression, nodeId, fieldName, fieldType, this.nodeStore );
		} else {
			return expression;
		}
	}
	parseExpressions( nodeId, expressions ) {
		if ( ! expressions ) {
			return null;
		}
		let parsedExpressions = {};
		let fieldType = null;

		for ( let i in expressions ) {
			let expression = expressions[i];

			if ( this.nodeStore.getState().nodes[nodeId].fields ) {
				for ( let f in this.nodeStore.getState().nodes[nodeId].fields ) {
					if ( this.nodeStore.getState().nodes[nodeId].fields[f].name == i ) {
						fieldType = this.nodeStore.getState().nodes[nodeId].fields[f].type;
						break;
					}
				}
			}

			if ( fieldType ) {
				let existingType = this.nodeStore.getState().fieldTypes[nodeId + '.' + i];
				if ( 0 === fieldType.indexOf( '$' ) ) {
					fieldType = this.nodeStore.getState().fieldTypes[nodeId + '.' + fieldType.substring( 1 )];
				}

			}

			parsedExpressions[i] = this.parseExpression( nodeId, i, expression, fieldType );
		}

		return parsedExpressions;
	}

	parseFilterExpressions( nodeId, filterGroups ) {
		if ( ! filterGroups ) {
			return null;
		}
		let parseFilters = [];
		let fieldType = null;
		for ( let i in filterGroups ) {
			parseFilters[i] = [];
			for ( let j in filterGroups[i]) {
				let newFilter = {};
				newFilter.op = filterGroups[i][j].op;
				if ( ! filterGroups[i][j].left || ! filterGroups[i][j].left.expr ) {
					continue;
				}
				if ( filterGroups[i][j].left.expr.type ) {
					newFilter.left = filterGroups[i][j].left.expr;
				} else {
					newFilter.left = { expr: this.parseExpression( nodeId, '', filterGroups[i][j].left.expr, 'object' ), display: filterGroups[i][j].left.display };
				}
				newFilter.right = filterGroups[i][j].right;
				parseFilters[i].push( newFilter );
			}
		}
		return parseFilters;
	}
	stringifyFilters( filterGroups ) {
		if ( ! filterGroups ) {
			return null;
		}
		let parseFilters = [];
		let fieldType = null;
		for ( let i in filterGroups ) {
			parseFilters[i] = [];
			for ( let j in filterGroups[i]) {
				let newFilter = {};
				newFilter.op = filterGroups[i][j].op;
				newFilter.left = { expr: stringifyExpression( filterGroups[i][j].left.expr ), display: filterGroups[i][j].left.display };
				newFilter.right = filterGroups[i][j].right;
				parseFilters[i].push( newFilter );
			}
		}
		return parseFilters;
	}

	getNodesForSave( nodes, defs ) {
		let prevNode = null;
		let newNodes = Object.values( nodes ).map( ( n )=> {
			let def = defs && defs[n.type] || false;
			let newNode = {
				nid: n.nid,
				x: n.x,
				y: n.y,
				type: n.type,
				expressions: this.parseExpressions( n.nid, n.expressions ),
				filters: this.parseFilterExpressions( n.nid, n.filters )
			};
			return newNode;
		});
		let actionOrTriggerNodes = newNodes.filter( n=>defs && n.type && defs[n.type] && ( 'action' == defs[n.type].nodeType || 'condition' == defs[n.type].nodeType || 'trigger' == defs[n.type].nodeType ) );
		for ( let i = 1; i < actionOrTriggerNodes.length; i++ ) {
			actionOrTriggerNodes[i - 1].next = [ actionOrTriggerNodes[i].nid ];
		}
		return newNodes;
	}
	saveGraph() {
	  let self = this;

	  this.props.linkedField.value = ( JSON.stringify({
		  nodes: self.getNodesForSave( self.nodeStore.getState().nodes, self.nodeStore.getState().definitions ),
		  fieldTypes: self.nodeStore.getState().fieldTypes
	  }) );
	}

	componentDidMount() {
		this.nodeStore.subscribe( this.saveGraph.bind( this ) );
	}
	render() {
		return (
			<Provider store={this.nodeStore}>
				<ReactNodeGraph
					graph={this.state}
					errors={this.state.errors}
				/>
			</Provider>
		);
	}
}
