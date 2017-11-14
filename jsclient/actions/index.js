import {replaceSelfInExpression} from '../lib/util';
import axios from 'axios';
export const editNode = ( node, type ) => {
	return {
		type: 'EDIT_NODE',
		node: node,
		editType: type || ''
	};
};
export const setErrorMessage = ( nodeId, fieldName, error ) => {
	return {
		type: 'SET_ERROR_MESSAGE',
		nodeId,
		fieldName,
		message: error
  };
};
export const clearErrorMessage = ( nodeId, fieldName ) => {
	return {
		type: 'SET_ERROR_MESSAGE',
		nodeId,
		fieldName,
		message: ''
	};
};
export const setFieldType = ( nodeId, fieldName, fieldType ) => {
	return {
		type: 'SET_FIELD_TYPE',
		nodeId,
		fieldName,
		fieldType
	};
};

export const setNodeTitle = ( nodeId, nodeTitle ) => {
	return {
		type: 'SET_NODE_TITLE',
		nodeId,
		nodeTitle
	};
};


const loadingDataType = ( dataTypeId ) => {
	return ({ type: 'LOADING_DATA_TYPE', dataTypeId});
};
const loadedDataType = ( dataTypeId, json ) => ({ type: 'LOADED_DATA_TYPE', dataTypeId, datatype: json});
let dataTypesBeingLoaded = {};
export const loadDataType = dataTypeId => {
	return dispatch=>{
		if ( null == dataTypeId || dataTypesBeingLoaded[dataTypeId]) {
			return;
		}
		dataTypesBeingLoaded[dataTypeId] = true;
		dispatch( loadingDataType( dataTypeId ) );
		return fetch( TH.rest_api_url + 'wpflow/v1/types/' + dataTypeId + '?_wpnonce=' + document.getElementById( 'triggerhappy-x-nonce' ).value, { credentials: 'same-origin' }).then( response => {
			if ( 200 != response.status ) {
				return null;
			}
			return response.json();
		}).then( json => dispatch( loadedDataType( dataTypeId, json ) ) );
	};
};

export const testNode = node => {
	return {
		type: 'TEST_NODE',
		node
	};
};
export const navigate = ( page, options ) => {
	return {
		type: 'NAVIGATE',
		page,
		options
	};
};
export const nextStep = () => ( dispatch, getState ) => {
	let state = getState();
	let panelType = state.ui.panelType;
	let panelOptions = state.ui.panelOptions;

	let next = false;
	let found = null;
	let allSteps = [].concat.apply([], Object.values( state.steps ) );
	for ( let s in allSteps ) {
		let step = allSteps[s];
		if ( next ) {
			return dispatch( navigate( step.page, step.options ) );
		}
		next = ( panelType == step.page && Object.keys( panelOptions ).reduce( ( p, k )=> p && step.options[k] && panelOptions[k] == step.options[k], true ) );
	}
	return dispatch( navigate( 'CreateNew' ) );
};


export const deleteNode = ( nodeId ) => ( dispatch, getState ) => {
		dispatch( navigate( 'CreateNew' ) );
		dispatch({
			type: 'REMOVE_NODE',
			nodeId: nodeId
		});
};
export const addNode = ( node, nid ) => ( dispatch, getState ) => {

return new Promise( ( resolve, reject ) => {

	let state = getState();

	let nId = nid || Object.values( state.nodes ).reduce( function( acc, next ) {
		return next.nid > acc ? next.nid : acc;
	}, 0 ) + 1;
	let newNode = {
		cat: node.cat || '',
		description: node.description || '',
		name: node.name,
		type: node.type,
		nodeType: node.nodeType || 'data',
		nid: nId,
		fields: [ ...( node.fields || []) ]
	};


	dispatch({
		type: 'ADD_NODE',
		node: newNode
	});

	dispatch({
		type: 'CLEAR_STEPS',
		nodeId: newNode.nid
	});

	let autoImport = state.definitions[node.type] && state.definitions[node.type].autoImport;
	if ( autoImport ) {
		for ( let i in autoImport ) {
			let autoImportDef = state.definitions[autoImport[i]];
			if ( autoImportDef ) {
				dispatch( addNode( autoImportDef ) );
			}
		}
	}
	if ( node.expressions ) {
		for ( let x in node.expressions ) {
			let expr = node.expressions[x];
			expr = expr.replace( '{{.', '{{_N' + newNode.nid + '.' );
			dispatch({
				type: 'SET_NODE_EXPRESSION',
				nodeId: newNode.nid,
				connectorId: x,
				expr: expr
			});
		}
	}
	if ( node.filters ) {
		let filters = [];
		for (let g in node.filters) {
			let filterGroup = [];
			filters.push(filterGroup);
			for (let i in node.filters[g]) {
				filterGroup.push({
					left: { expr: replaceSelfInExpression(node.filters[g][i].left.expr, newNode.nid) },
					op:node.filters[g][i].op,
					right: { expr: replaceSelfInExpression(node.filters[g][i].right.expr, newNode.nid) }
				});
			}
		}
		dispatch({
			type: 'SET_NODE_FILTERS',
			nodeId: newNode.nid,
			filters: filters
		});
	}
	if ( newNode.fields.return !== undefined ) {
		dispatch({
			type: 'ADD_STEP',
			nodeId: newNode.nid,

			step: {
				isReturn: true,
					text: 'Edit Value',
					icon: 'fa-pencil',
				page: 'NodeSettings',
				options: {
					editType: 'return',
					title: 'Edit Value',
					node: newNode.nid
				}
			}
		});
	}

	if ( 'trigger' == newNode.nodeType && 0 == newNode.fields.filter( r=>'in' == r.dir ).length ) {

	} else if ( newNode.fields && 0 < newNode.fields.length ) {
		dispatch({
			type: 'ADD_STEP',
			nodeId: newNode.nid,
			step: {
				text: 'Edit Settings',
				icon: 'fa-pencil',
				page: 'NodeSettings',
				options: {
					editType: 'in',
					title: 'Edit Node',
					node: newNode.nid
				}
			}
		});
	
	}
	if ( node.allowFilters ) {
		dispatch({
			type: 'ADD_STEP',
			nodeId: newNode.nid,

			step: {
				text: 'Edit filters',
				icon: 'fa-filter',
				page: 'NodeFilters',
				options: {
					editType: 'in',
					title: 'Edit Filters',
					node: newNode.nid
				}
			}
		});
	}
	state = getState();

	let nextStep = state.steps[newNode.nid] && state.steps[newNode.nid].filter( r=>! r.isReturn )[0];
	if ( state.ui.prevStep ) {
		nextStep = state.ui.prevStep;
	}
	if ( nextStep ) {
		dispatch( navigate( nextStep.page, nextStep.options ) );
	}

		resolve();
	});
};


export const showCreateNew = () => {
	return {
		type: 'SHOW_CREATE_NEW'
	};
};

export const resetUI = () => {
	return {
		type: 'RESET_UI'
	};
};

export const showSelectPluginActions = ( plugin ) => {

	return {
		type: 'SHOW_SELECT_ACTION',
		plugin
	};
};

export const setExpression = ( nodeId, connectorId, expr ) => {

	return dispatch=> {

		dispatch({
			type: 'SET_NODE_EXPRESSION',
			nodeId,
			connectorId,
			expr
		});
	};
};
export const setFilters = ( nodeId, filters ) => {

	return {
		type: 'SET_NODE_FILTERS',
		filters,
		nodeId
	};
};

export function fetchActions( plugin ) {
	if ( ! plugin ) {
		plugin = '';
	}

	return dispatch => {
		dispatch( requestActions() );

		return fetch( TH.rest_api_url + 'wpflow/v1/nodes/?plugin=' + plugin + '&_wpnonce=' + document.getElementById( 'triggerhappy-x-nonce' ).value, { credentials: 'same-origin' })
			.then( response => response.json() )
			.then( json => dispatch( receiveActions( json ) ) );
	};
}
export function fetchPlugins() {
	return dispatch => {

	dispatch( requestPlugins() );

	return fetch( TH.rest_api_url + 'wpflow/v1/plugins' + '?_wpnonce=' + document.getElementById( 'triggerhappy-x-nonce' ).value, { credentials: 'same-origin' })
		.then( response => response.json() )
		.then( json => dispatch( receivePlugins( json ) ) );
	};
}
export function requestPlugins() {

	return {
		type: 'REQUEST_PLUGINS'
	};
}

export const selectTrigger = ()=> ( dispatch, getState ) => {
	dispatch({ type: 'ADD_TRIGGER' });

};

export function receivePlugins( plugins ) {

	return {
		type: 'RECEIVED_PLUGINS',
		plugins
	};
}

export function requestActions() {

	return {
		type: 'REQUEST_ACTIONS'
	};
}

export function receiveActions( actions ) {

	return {
		type: 'RECEIVED_ACTIONS',
		actions
	};
}
