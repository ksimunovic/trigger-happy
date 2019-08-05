import React from 'react';
const Entities = require( 'html-entities' ).AllHtmlEntities;

import NodeFieldList from './NodeFieldList';
import {
	setExpression,
	showSelectPluginActions,
	fetchActions,
	addNode,
	editNode
} from '../actions';
import _ from 'lodash';
import {
	connect
} from 'react-redux';
require( 'isomorphic-fetch' );
class CreateNewPanel extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			selectedPlugin: null,
			showAll: false
		};
	}
	selectPlugin( plugin ) {
		this.setState({
			'selectedPlugin': plugin
		});
		this.props.loadActions( plugin );
	}
	renderActionList( actions, key ) {
		let selectAction = ( p ) => {
			this.setState({
				'selectedPlugin': null,
				search: ''
			});
			let id = null;
			if ( 'trigger' == p.nodeType ) {
				id = 1;
			}
			this.props.selectAction( p, id );
		};
		return ( <div className="action-list" key={key}>
			{_.sortBy( actions, i=>i.name ).map( function( p ) {
				return (
					<div key={p.name} className="action-item" onClick={()=>selectAction( p )}>
						{'condition' == p.nodeType &&	<div className="action-tag">{p.nodeType}</div>	}
						<strong>{p.name}</strong>
						<div>{p.description}</div>
					</div>
				);
		})}	</div> );
	}
	renderBrowseByPlugin() {
		if ( this.state.search || 0 == this.props.plugins.length ) {
			return null;
		}
		let selectPlugin = this.selectPlugin.bind( this );
		return (
			<div key="browse-by-plugin">
				<h5>Browse by plugin</h5>
				<div className="create-new-list">
					{this.props.plugins.map( function( p ) {
						return (
								<div key={p.name} className="create-new-item" onClick={()=>selectPlugin( p.name )}>
									<img src={p.icon} className="create-new-item__image" />
									<p >{Entities.decode( p.label )}</p>
								</div>
						);
					})}
				</div>
			</div>
	);
	}
	checkIfCanShow( action ) {
		if ( 'condition' == action.nodeType &&  this.props.preferredNodeTypes !== false  ) {
			return ! action.conditionType || this.props.preferredNodeTypes !== false &&  this.props.preferredNodeTypes.indexOf( action.conditionType ) >= 0;
		} else if ( ( 'action' == action.nodeType ) && ( action.actionType || this.props.preferredNodeTypes ) && ! this.state.showAll ) {
			let nodeTypes = this.props.preferredNodeTypes;
			return action.actionType && this.props.preferredNodeTypes !== false && this.props.preferredNodeTypes.indexOf( action.actionType ) >= 0;
		}
		return true;
	}
	catParts() {
		if ( this.props.categories ) {
			let vals = Object.values( this.props.categories );
			return _.sortBy( vals, i => i.name ).filter( c => 0 < c.actions.length ).map( c => {
				let actions = c.actions.filter( i => 0 <= this.props.allowTypes.indexOf( i.nodeType ) && ( ! this.state.search || 0 <= i.name.toLowerCase().indexOf( this.state.search.toLowerCase() ) || 0 <= i.description.toLowerCase().indexOf( this.state.search.toLowerCase() ) ) && this.checkIfCanShow( i ) );
				if ( 0 == actions.length ) {
					return null;
				}
				return ( <div key={c.name}><h5>Category: {c.name}</h5>{this.renderActionList( actions, c.name )}</div> );
			});
		}
		return [];
	}
	setSearch( value ) {
		this.setState({
			search: value
		});
	}
	renderFilterPanel() {
		return <div  key="node-filter-panel" className="node-filter-panel" ><input type="text" className="node-search-bar" value={this.state.search} placeholder="Search available actions..." onChange={( e )=>this.setSearch( e.target.value )} /></div>;
	}
	renderParts() {
		if ( this.state && this.state.selectedPlugin ) {
			return this.renderActionList( this.props.actions, 'plugin-parts' );
		}
		let cats = this.catParts();
		return [ this.renderFilterPanel(), this.renderBrowseByPlugin(), ...cats ];
	}
	showAllNodes() {
		this.setState({
			showAll: true
		});
	}
	render() {
		let selectPlugin = this.selectPlugin;
		return (
			<div key="create-new-panel">
			<div className="node-settings-plugin">Add New</div>
			<h4 className="node-settings-title">{this.props.title}</h4>
			{this.renderParts()}
			{this.props.preferredNodeTypes && <a href="javascript:void(0)" onClick={()=>this.showAllNodes()}>Show All Actions</a>}
			</div>
		);
	}
}

function getDefinitionsByCategory( definitions ) {
	let ret = {};
	for ( let d in definitions ) {
		let def = definitions[d];
		if ( def.cat ) {
			if ( ! ret[def.cat]) {
				ret[def.cat] = {
					name: def.cat,
					actions: []
				};
			}
			ret[def.cat].actions.push( def );
		}
	}
	return ret;
}
const mapStateToProps = state => {
	let isTrigger = ( state.ui.addTrigger || 0 == Object.keys( state.nodes ).filter( r => 'trigger' == state.nodes[r].nodeType ).length );
	let preferredNodeTypes = Object.keys( state.nodes ).filter( r => state.definitions[ state.nodes[r].type ].triggerType || state.definitions[ state.nodes[r].type ].actionType );
	if ( 0 < preferredNodeTypes.length ) {
		preferredNodeTypes = preferredNodeTypes.map( r => state.definitions[ state.nodes[r].type ].triggerType || state.definitions[ state.nodes[r].type ].actionType );
	} else {
		preferredNodeTypes = false;
	}

	return {
		title: isTrigger ? 'Select a trigger' : 'Add an action',
		preferredNodeTypes: preferredNodeTypes,
		allowTypes: isTrigger ? [ 'trigger' ] : [ 'action', 'condition' ],
		plugins: state.plugins && state.plugins.plugins || [],
		actions: state.actions && state.actions.actions || [],
		categories: state.definitions && getDefinitionsByCategory( state.definitions ) || []
	};
};

const mapDispatchToProps = dispatch => {
	return {
		selectAction: ( action, id ) => {
			return dispatch( addNode( action, id ) );
		},
		loadActions: ( plugin ) => {
			return dispatch( fetchActions( plugin ) );
		}
	};
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)( CreateNewPanel );
