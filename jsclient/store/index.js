import { combineReducers } from 'redux';
import nodes from './nodes';
import expressions from './expressions';
import definitions from './definitions';
import connections from './definitions';
import fieldTypes from './fieldTypes';
import datatypes from './datatypes';
import globals from './globals';
import steps from './steps';
import ui from './ui';

function plugins(
	state = {
		isFetching: false,
		didInvalidate: false,
		plugins: []
	},
	action
) {
	switch ( action.type ) {

		case 'REQUEST_PLUGINS':
			return Object.assign({}, state, {
				isFetching: true,
				didInvalidate: false
			});

		case 'RECEIVED_PLUGINS':
			return Object.assign({}, state, {
				didInvalidate: false,
				isFetching: false,
				plugins: action.plugins
			});


		default:
			return state;
	}
}

function actions(
	state = {
		isFetching: false,
		didInvalidate: false,
		actions: []
	},
	action
) {
	switch ( action.type ) {

		case 'REQUEST_ACTIONS':
			return Object.assign({}, state, {
				isFetching: true,
				didInvalidate: false
			});
		case 'RECEIVED_ACTIONS':
			return Object.assign({}, state, {
				isFetching: false,
				didInvalidate: false,
				actions: action.actions
			});


		default:
			return state;
	}
}


export default combineReducers({ expressions, nodes, definitions, fieldTypes, ui, plugins, actions, datatypes, steps, globals });
