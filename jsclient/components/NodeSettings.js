import React from 'react';
import NodeFieldList from './NodeFieldList';
import {
	setExpression,
	setNodeTitle
} from '../actions';

import {
	connect
} from 'react-redux';
class NodeSettings extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {};
	}
	getCaption() {
		return this.state.title || this.props.selectedNode.title || this.props.selectedNode.name.replace( /\$[0-9]\|.*\$/, ( v ) => this.getInputValue( v ) ).trim();
	}
	getPlugin() {
		let pluginPrefix = this.props.selectedNode.plugin ? this.props.selectedNode.plugin : '';
		return pluginPrefix;
	}
	saveTitle() {
		this.props.setNodeTitle( this.props.selectedNode.nid, this.state.title );
		this.setState({
			editTitle: false
		});
	}
	setCaption( val ) {
		this.setState({
			title: val
		});
		this.props.setNodeTitle( this.props.selectedNode.nid, val );
	}
	render() {
		return ( <div>	<div className="node-settings-plugin">{this.getPlugin()}</div>	<h4 className="node-settings-title">	<input className="node-editable-title" value={this.getCaption()} onChange={( e )=>this.setCaption( e.target.value )} />	 </h4>	 {this.props.selectedNode.helpText && ( <p className="node-help-text">{this.props.selectedNode.helpText}	 </p> )}	<NodeFieldList fieldTypes={this.props.fieldTypes} nodeIndex={this.props.nodeIndex} setExpression={this.props.setExpression} fetchNode={this.props.fetchNode} node={this.props.selectedNode} type="input" items={this.props.settings} />	</div> );
	}
}
const mapStateToProps = ( state, ownProps ) => {
	let node = state.nodes[ownProps.node];
	let nodeIndex = Object.values( state.nodes ).indexOf( node );

	return {
		fetchNode: ( nid ) => {
			let nodes = state.nodes[nid];
			if ( nodes && nodes.length ) {
				return nodes[0];
			}
			return null;
		},
		fieldTypes: state.fieldTypes,
		nodeIndex: nodeIndex,
		selectedNode: node,
		settings: ( node.fields || []).filter( n => n.dir == ownProps.editType )
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
		setNodeTitle: ( nodeId, nodeTitle ) => dispatch( setNodeTitle( nodeId, nodeTitle ) )
	};
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)( NodeSettings );
