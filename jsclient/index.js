import React from 'react';
import { connect } from 'react-redux';
import Components from './components';
import { nextStep, addNode } from './actions';

/**
 * Loads the node definitions and globals from the API
 */
function loadDefinitions() {
	return ( dispatch ) => {
		return {
			then: function( cb ) {
				let completed = 0;
				fetch( TH.rest_api_url + 'wpflow/v1/nodes/?_wpnonce=' + document.getElementById( 'triggerhappy-x-nonce' ).value, {
					credentials: 'same-origin'
				}).then( response => response.json() ).then( data => {
					dispatch({
						type: 'SET_DEFINITIONS',
						definitions: data
					});
					completed++;
					if ( 2 == completed ) {
						cb();
					}
				});
				fetch( TH.rest_api_url + 'wpflow/v1/globals/?_wpnonce=' + document.getElementById( 'triggerhappy-x-nonce' ).value, {
					credentials: 'same-origin'
				}).then( response => response.json() ).then( data => {
					dispatch({
						type: 'SET_GLOBALS',
						globals: data
					});
					completed++;
					if ( 2 == completed ) {
						cb();
					}
					return true;
				});
			}
		};
	};
}

/**
 * Loads the nodes from the provided Node graph into the store
 */
function loadNodeGraph( graph ) {
	return ( dispatch, getState ) => {
		let state = getState();
		for ( let i in graph.nodes ) {
			let nodeToLoad = graph.nodes[i];
			dispatch( addNode( Object.assign({}, state.definitions[nodeToLoad.type], nodeToLoad ), nodeToLoad.nid ) );
		}
	};
}

/**
 * Redux Connect MapDispatchToProps function for ReactGraph
 */
function mapDispatchToProps( dispatch ) {
	return {
		loadDefinitions: () => dispatch( loadDefinitions() ),
		loadNodeGraph: ( data ) => dispatch( loadNodeGraph( data ) )
	};
}

/**
 * Redux Connect MapStateToProps function for ReactGraph
 */
function mapStateToProps( state ) {
	return {
		panelType: state.ui.panelType,
		panelOptions: state.ui.panelOptions,
		nextButtonText: state.ui.nextButtonText,
		editType: state.ui.editType,
		showPanel: state.ui.showPanel,
		selectedNodeId: state.ui.selectedNodeId
	};
}

/**
 * React Node Graph component
 * @example
 * <ReactGraph
 *  panelType="panelName"
 * />
 * @param {string} panelType The type of panel to display
 */
class ReactGraph extends React.Component {
	componentDidMount() {
		this.props.loadDefinitions().then( () => {
			this.props.loadNodeGraph( this.props.graph );
		});
	}
	render() {
		let Control = Components[this.props.panelType || 'CreateNew'];
		return ( <div>	<div className="node-sidebar">	<Components.NodeList />	</div>	<div className="node-settings">	<Control {...this.props.panelOptions} />	</div>	</div> );
	}
}

export default connect( mapStateToProps, mapDispatchToProps )( ReactGraph );
