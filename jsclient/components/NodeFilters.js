import React from 'react';
import NodeFieldList from './NodeFieldList';
import {
	setExpression,
	setFilters
} from '../actions';
import NodeFilter from './NodeFilter';
import {
	connect
} from 'react-redux';

class NodeSettings extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			filterGroups: props.filters || [
				[ '' ]
			]
		};
	}
	componentWillReceiveProps( props ) {
		if ( props.filters ) {
			this.setState({
				filterGroups: props.filters
			});
		}
	}
	getCaption() {
		return this.props.selectedNode.name.replace( /\$[0-9]\|.*\$/, ( v ) => this.getInputValue( v ) ).trim();
	}
	getPlugin() {
		let pluginPrefix = this.props.selectedNode.plugin ? this.props.selectedNode.plugin : '';
		return pluginPrefix;
	}
	selectLeftValue( n, v, e ) {}
	setFilter( group, row, filterData ) {
		let newState = [ ...this.state.filterGroups ];
		newState[group][row] = filterData;
		this.setState({
			filterGroups: newState
		});
		this.props.setFilters( this.props.selectedNode.nid, newState );
	}
	render() {
		return (
			<div>
				<h5>Execute this flow if:</h5>
				<div className="node-filter-container">
					{this.state.filterGroups.map( ( group, i ) => (
						<div className="node-filter-group">
							{group.map( ( row, i2 ) => (
								<div>
									{0 < i2 && <span className='node-filter-label--and'>AND</span>}
									{0 == i2 && 0 < i && <span className='node-filter-label-or'>OR</span>}
									<NodeFilter
										filter={row}
										availableFields={this.props.availableFields}
										onChange={( data )=>this.setFilter( i, i2, data )}
										fetchNode={this.props.fetchNode}
										filter={this.state.filterGroups[i][i2]}
									/>
								</div>
							) )}
							<a style={{marginLeft: 20}} className="button button-small" onClick={()=>this.addAndFilter( i )}>+ And</a>
						</div>
					) )}
					<a className="button button-small" onClick={()=>this.addOrFilter()}>+ Or</a>
				</div>
			</div>
		);
	}
	addAndFilter( groupindex ) {
		let groups = [ ...this.state.filterGroups ];
		groups[groupindex].push({});
		this.setState({
			filterGroups: groups
		});
	}
	addOrFilter() {
		let groups = [ ...this.state.filterGroups, [ {} ] ];
		this.setState({
			filterGroups: groups
		});
	}
}
const mapStateToProps = ( state, ownProps ) => {
	let node = state.nodes[ownProps.node];
	if ( ! node ) {
		return {
			availableFields: []
		};
	}
	let nodeIndex = Object.values( state.nodes ).indexOf( node );
	let availableFields = Object.values( state.nodes ).filter( ( el, i ) => {
		let def = state.definitions[el.type];
		return i <= nodeIndex;
	}).map( ( n, i ) => {
		let def = state.definitions[n.type];
		let allFields = [];
		if ( def && def.fields ) {
			allFields = ( def.fields && def.fields.filter( f => 'flow' !== f.type && ( 'out' == f.dir || 'start' == f.dir ) ) );
		}
		return {
			nid: n.nid,
			name: n.type,
			name2: def.name,
			plugin: def.plugin,
			fields: allFields
		};
	});
	let globalFields = Object.values( state.globals );
	availableFields.push({
		nid: 'Global',
		name: '',
		name2: '',
		plugin: '',
		fields: globalFields,
		type: 'start'
	});

	return {
		availableFields,
		fetchNode: ( nid ) => {
			let nodes = state.nodes[nid];
			if ( nodes && nodes.length ) {
				return nodes[0];
			}
			return null;
		},
		filters: node.filters,
		nodeIndex: nodeIndex,
		selectedNode: node,
		settings: ( node.fields || []).filter( n => 'flow' != n.type && 'start' == n.dir ),
		value: {}
	};
};

const mapDispatchToProps = dispatch => {
	return {
		setExpression: ( nodeId, pinId, value ) => {
			dispatch( setExpression( nodeId, pinId, value ) );
		},
		onNodeEdit: id => {

			dispatch( editNode( id ) );
		},
		setFilters: ( nodeId, filters ) => {
			dispatch( setFilters( nodeId, filters ) );
		}
	};
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)( NodeSettings );
