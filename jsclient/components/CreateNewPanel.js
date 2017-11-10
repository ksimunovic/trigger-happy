import React from 'react';
import HookListener from './HookListener';
const Entities = require('html-entities').AllHtmlEntities;

import NodeConnectorList from './NodeConnectorList';
import {setExpression,showSelectPluginActions,fetchActions,addNode,editNode} from '../actions';
import _ from 'lodash';
import { connect } from 'react-redux';
require('isomorphic-fetch');
class CreateNewPanel extends React.Component {
	constructor(props) {
		super(props);
		this.state = { selectedPlugin: null, showAll: false };
	}
	selectPlugin(plugin) {
		this.setState({'selectedPlugin':plugin});
		this.props.loadActions(plugin);
	}

	renderActionList(actions) {
		let selectAction = (p)=>{
			this.setState({'selectedPlugin':null,search:''});
			let id = null;
			if (p.nodeType == 'trigger') {
				id = 1;
			}
			this.props.selectAction(p,id);

		};
		return (<div className="action-list">
		{_.sortBy(actions,i=>i.name).map(function(p) {
			return (
				<div className="action-item" onClick={()=>selectAction(p)}>
					{p.nodeType == 'condition' &&
					<div className="action-tag">{p.nodeType}</div>
					}
					<strong>{p.name}</strong>
					<div>{p.description}</div>
				</div>
			);
		})}
		</div>)
	}
	renderBrowseByPlugin() {
		if (this.state.search || this.props.plugins.length == 0)
		return null;
		let selectPlugin = this.selectPlugin.bind(this);
		return (<div><h5>Browse by plugin</h5>
		<div className="create-new-list">
		{this.props.plugins.map(function(p) {
		return (
			<div className="create-new-item" onClick={()=>selectPlugin(p.name)}>
				<img src={p.icon} className="create-new-item__image" />
				<p >{Entities.decode(p.label)}</p>

			</div>
			);
		})}
		</div></div>);
	}

	checkIfCanShow(action) {
		if (action.nodeType == 'condition' && this.props.preferredNodeTypes !== false)
		{
			return !action.conditionType || this.props.preferredNodeTypes !== false && this.props.preferredNodeTypes.indexOf(action.conditionType) !== false;
		} else if ((action.nodeType == 'action' ) && (action.actionType || this.props.preferredNodeTypes) && !this.state.showAll) {
			let nodeTypes = this.props.preferredNodeTypes;


			return action.actionType && this.props.preferredNodeTypes !== false && this.props.preferredNodeTypes.indexOf(action.actionType) !== false;
		}
		return true;
	}
	catParts() {
		if (this.props.categories) {
			var vals = Object.values(this.props.categories);

			return _.sortBy(vals,i=>i.name).filter(c=>c.actions.length > 0).map(c=>{

				let actions = c.actions.filter(i=>this.props.allowTypes.indexOf(i.nodeType) >= 0 && (!this.state.search || i.name.toLowerCase().indexOf(this.state.search.toLowerCase()) >= 0 || i.description.toLowerCase().indexOf(this.state.search.toLowerCase()) >= 0 ) && this.checkIfCanShow(i));
				if (actions.length == 0)
					return null;
				return (<div><h5>Category: {c.name}</h5>{this.renderActionList(actions)}</div>);
			});
		}
		return [];
	}
	listenForHooks() {
		this.setState({listen:true});
	}
	setSearch(value) {
		this.setState({search:value});
	}
	customHook() {
		return <div className="action-item" onClick={()=>this.listenForHooks()}>Custom Action</div>;
	}
	renderFilterPanel() {
		return <div className="node-filter-panel" ><input type="text" className="node-search-bar" value={this.state.search} placeholder="Search available actions..." onChange={(e)=>this.setSearch(e.target.value)} /></div>;

	}
	renderParts() {
		if (this.state && this.state.selectedPlugin) {
			return this.renderActionList(this.props.actions);
		}
		let customHook = this.customHook();
		let cats = this.catParts();
		return [this.renderFilterPanel(),this.renderBrowseByPlugin(),  ...cats ];
	}
	showAllNodes() {
		this.setState({showAll:true});
	}
	render() {
		let selectPlugin = this.selectPlugin;
		if (this.state.listen) {
			return (<HookListener />);

		}
		return (<div>

			<div className="node-settings-plugin">Add New</div>
			<h4 className="node-settings-title">{this.props.title}</h4>
			{this.renderParts()}
			{this.props.preferredNodeTypes && <a href="javascript:void(0)" onClick={()=>this.showAllNodes()}>Show All Actions</a>}
		</div>);
	}
}
function  getDefinitionsByCategory(definitions) {
	var ret = {};

	for (var d in definitions) {
		var def = definitions[d];
		if (def.cat) {
			if (!ret[def.cat]) {
				ret[def.cat] = { name: def.cat, actions: [] };
			}
			ret[def.cat].actions.push(def);
		}
	}

	return ret;
}
const mapStateToProps = state => {
	 let isTrigger =(state.ui.addTrigger || Object.keys(state.nodes).filter(r=>state.nodes[r].nodeType == "trigger").length == 0);
	 let preferredNodeTypes = Object.keys(state.nodes).filter(r=>state.definitions[state.nodes[r].type].triggerType || state.definitions[state.nodes[r].type].actionType);

	 if (preferredNodeTypes.length > 0)
	 	preferredNodeTypes = preferredNodeTypes.map(r=>state.definitions[state.nodes[r].type].triggerType || state.definitions[state.nodes[r].type].actionType);
	else {
		preferredNodeTypes = false;
	}

     return {
		 title: isTrigger ? 'Select a trigger' : 'Add an action',
		 preferredNodeTypes:  preferredNodeTypes,

		 allowTypes: isTrigger ? ['trigger'] : ['action','condition'],
		 plugins: state.plugins && state.plugins.plugins || [],
		  actions: state.actions && state.actions.actions || [],
 		  categories: state.definitions && getDefinitionsByCategory(state.definitions) || []
    };
};

const mapDispatchToProps = dispatch => {
  return {
	  selectAction: (action,id) => { return dispatch(addNode(action,id)); },
	  loadActions: (plugin) => { return dispatch(fetchActions(plugin)); }
  }
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(CreateNewPanel)
