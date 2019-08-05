import React from 'react';
import SimpleNode from './SimpleNode';
import {
	connect
} from 'react-redux';
import {navigate} from '../actions';

function renderAddPlaceholder( createNewClick ) {
	return (
		<div onClick={createNewClick}>
			<section  className='node node--add-new' >
				<span className='button node-title button-large'><i className='fa fa-plus'></i> &nbsp; Add new Action</span>
			</section>
		</div>
	);

}

function isCurrentStep() {
	return false;
}

function renderNode( node, i, onSelect, onTest, fetchNode, steps ) {
	let totalOutputNonFlowPins = ( node.fields.out || []).filter( p => 'flow' !== p.type ).length;
	let totalInputNonFlowPins = ( node.fields.in || []).filter( p => 'flow' !== p.type ).length;
	let autocollapse = ( 0 == totalInputNonFlowPins && 0 == totalOutputNonFlowPins );

	return (
		<SimpleNode
			index={i}
			nodeId={node.nid}
			fetchNode={fetchNode}
			selectNode={( nid, type )=>onSelect( nid, type )}
			index={i++}
			nid={node.nid}
			color='#000000'
			title={node.type}
			inputs={node.fields.in}
			outputs={node.fields.out}
			pos={{x: node.x, y: node.y}}
			key={node.nid}
			node={node}
		/>
	);
}

const NodeList = ({
	dataNodes,
	fetchNode,
	navigate,
	nodeDefinitions,
	triggerNodes,
	actionNodes,
	resultNode,
	onNodeEdit,
	onNodeTest,
	showCreateNew,
	steps
}) => {
	return ( <div className='node-list'>
	<div className='node-wrapper'>
	{triggerNodes && triggerNodes.map( ( node, i ) => renderNode( node, i, onNodeEdit, onNodeTest, fetchNode, steps ) )}
	{actionNodes && actionNodes.map( ( node, i ) => renderNode( node, i, onNodeEdit, onNodeTest,  fetchNode, steps ) )}
	</div>
	{ renderAddPlaceholder( showCreateNew )}


 </div> );
};

const mapStateToProps = state => {
	let defs = state.definitions;

	let resultNodes = Object.values( state.nodes ).filter( n => defs && defs[n.type] && defs[n.type].resultLabel && '' !== defs[n.type].resultLabel )
		.map( n => Object.assign({
			nodeType: 'result'
		}, n, state.definitions[n.type], {
			nodeType: 'result',
			name: defs[n.type].resultLabel,
			description: defs[n.type].resultDesc
		}) );
	return {
		steps: state.steps,
		fetchNode: ( nid ) => state.nodes.filter( n => n.nid == nid ).pop(),
		triggerNodes: Object.values( state.nodes ).map( n => Object.assign({}, n, state.definitions[n.type]) ).filter( n => 'trigger' == n.nodeType ),
		actionNodes: Object.values( state.nodes ).map( n => Object.assign({}, n, state.definitions[n.type]) ).filter( n => 'action' == n.nodeType || 'condition' == n.nodeType ),
		dataNodes: Object.values( state.nodes ).map( n => Object.assign({}, n, state.definitions[n.type]) ).filter( n => 'data' == n.nodeType ),
		resultNode: ( 0 < resultNodes.length ? resultNodes[0] : null )
	};
};

const mapDispatchToProps = dispatch => {
	return {
		navigate: ( step, options ) => dispatch( navigate( step, options ) ),
		onNodeEdit: ( id, type ) => {

			dispatch( editNode( id, type ) );
		},
		onNodeTest: id => {

			dispatch( testNode( id ) );
		},
		showCreateNew: () => {
			return dispatch( navigate( 'CreateNew' ) );
		}

	};
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)( NodeList );
