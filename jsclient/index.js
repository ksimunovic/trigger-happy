import React from 'react';
import { connect } from 'react-redux';
import SimpleNode from './components/SimpleNode';
import NodeFilters from './components/NodeFilters';
import NodeSettings from './components/NodeSettings';
import SelectActionPanel from './components/SelectActionPanel';
import CreateNewPanel from './components/CreateNewPanel';
import NodeTest from './components/NodeTest';
import VerticalNodeList from './containers/VerticalNodeList';
import {resolve} from 'react-resolver';
import {nextStep, addNode} from './actions';
import Sticky  from 'react-sticky-el';


import {computeOutOffsetByIndex, computeInOffsetByIndex} from './lib/util';
const controls =  {
	CreateNew: CreateNewPanel,
	NodeSettings: NodeSettings,
	NodeFilters: NodeFilters,
	NodeTest: NodeTest
}
function loadDefinitions () {

	 return (dispatch) => {
		 return { then: function(cb) {
			 var done1 = false;
			 var done2 = false;

			 fetch('/wp-json/wpflow/v1/nodes/?_wpnonce=' + document.getElementById('triggerhappy-x-nonce').value, { credentials: 'same-origin' })
			  .then(response => response.json()).then(data=>{
				  dispatch({type:'SET_DEFINITIONS', definitions: data});
				  done1 = true;
				  if (done2) {
					 cb();
				   }
			  })

		   fetch('/wp-json/wpflow/v1/globals/?_wpnonce=' + document.getElementById('triggerhappy-x-nonce').value, { credentials: 'same-origin' })
		   .then(response => response.json()).then(data=>{
			 dispatch({type:'SET_GLOBALS', globals: data});
			 done2 = true;
			 if (done1) {
			    cb();
			  }
			 return true;
		 });
	 }};
	}
}

function loadNodeGraph (graph) {

	 return (dispatch,getState) => {
		 let state = getState();

		 for (var i in graph.nodes) {
			 let nodeToLoad = graph.nodes[i];

			 dispatch(addNode(Object.assign({},state.definitions[nodeToLoad.type],nodeToLoad), nodeToLoad.nid));
		 }
//       dispatch({type:'SET_DEFINITIONS', definitions: data});


	}
}

function mapDispatchToProps(dispatch) {
  return {
	nextButtonClick: () => dispatch(nextStep()),
    loadDefinitions: () => dispatch(loadDefinitions()),
	loadNodeGraph: (data) => dispatch(loadNodeGraph(data))
  };
}
function mapStateToProps(state) {

return {
	panelType: state.ui.panelType,
	panelOptions: state.ui.panelOptions,
	nextButtonText: state.ui.nextButtonText,
	editType: state.ui.editType,
	showPanel: state.ui.showPanel,
	selectedNodeId: state.ui.selectedNodeId
};
}

class index extends React.Component {

	componentDidMount() {
		this.props.loadDefinitions().then(()=>{

			this.props.loadNodeGraph(this.props.graph);
		});

	}
	componentDidUpdate() {
			this.props.onChange();
	}
	fillOutNodes(nodes) {
		let nodes1 =  nodes.map((n)=> {
 			let fetchedNode = this.getNodeByType(n.type);
			let completeNode = Object.assign({},JSON.parse(JSON.stringify(fetchedNode)),n);

			if (n.values && n.values.in)
				for (var l in n.values.in) {
 					let val = n.values.in[l];

					completeNode.fields.in.filter(p=>p.name == val.name)[0].value = val.value;
				}
			return completeNode;
		});

		return nodes1;

	}
	getNodeByType(nodeType) {
	  let matches = this.getAvailableNodes().filter(n=>n.type == nodeType);

		return matches && matches[0];
	}
	getAvailableNodes() {
		return this.props.availableNodes || [];
	}
	selectNode(nid) {
		for (var i = 0; i < this.props.data.nodes.length; i++) {
			if (this.props.data.nodes[i].nid == nid) {

				this.setState({ selectedNode: this.props.data.nodes[i] });
				return;
			}
		}
	}
	render() {
		let editTitle = this.props.showPanel == '' ? "Edit Settings" : "Edit Filters";
		let editType = this.props.editType == '' ? 'in':  this.props.editType;
		let Control = controls[this.props.panelType || 'CreateNew'];

		return (
			<div >

					<div className="node-sidebar">


						<VerticalNodeList />
					</div>
					<div className="node-settings">

						<Control {...this.props.panelOptions} />
						<a href="javascript:void(0)" className="button button-primary flow-button-right" onClick={()=>this.props.nextButtonClick()}>Next</a>
					</div>

			</div>
		);
	}
}

export default connect(mapStateToProps,mapDispatchToProps)(index);
/*
{  (this.props.selectedNodeId && this.props.showPanel == 'TestNode' ? (<NodeTest node={this.props.selectedNodeId}/>) : null) }
{  (this.props.selectedNodeId && this.props.showPanel !== 'TestNode' ? (<NodeSettings editType={editType} title={editTitle} node={this.props.selectedNodeId}/>) : null) }
{  (this.props.showPanel == 'CreateNew' ? (<CreateNewPanel />) : null) }
{  (this.props.showPanel == 'SelectAction' ? (<SelectActionPanel />) : null) }
{ this.props.nextButtonText && (<a className="node-next-button button button-primary button-large"  onClick={()=>this.props.nextButtonClicked()}>{this.props.nextButtonText}</a>)}
*/
