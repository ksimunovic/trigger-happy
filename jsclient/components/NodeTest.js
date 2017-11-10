import React from 'react';
import NodeConnectorList from './NodeConnectorList';
import {setExpression} from '../actions';
import { CookiesProvider, withCookies, Cookies } from 'react-cookie';

import { connect } from 'react-redux';

const ListenState = {
    NOT_LISTENING: 0,
    LISTENING: 1,
    FINISHED: 2
}
class NodeTest extends React.Component {
	constructor(props) {
		super(props);
		this.state = { listeningState: ListenState.NOT_LISTENING};
	}
	startListening() {
		this.setState({ listeningState: ListenState.LISTENING });

		this.doListen(true);
	}
	doListen(force) {
	   const { cookies } = this.props;
	   let date = new Date();
	   date.setTime(date.getTime() + 2000); // 2 seconds
	   if (force == undefined  && this.state.listeningState !== ListenState.LISTENING) {
		   cookies.remove("listenHooks", { path: '/' });
		   return;
	   }
	   cookies.set("listenHooks",this.props.selectedNode.type, { path: "/", expires: date });
	   var self = this;
	   fetch('?testf=1').then(function(response) {

		   return response.json();

	   }).then(function(hooks) {
		   if (hooks.length == 0) {
			   self.timeout = setTimeout(()=>self.doListen(),500);
			   return;
		   }
		   self.setState({foundHooks: hooks, foundHooksCount: self.countHooks(hooks) });
		   self.timeout = setTimeout(()=>self.doListen(),500);
	   });
   }
   countHooks(h) {
	   return 1;
	}
	stopListening() {
		
		clearTimeout(this.timeout);
		const { cookies } = this.props;
		this.setState({ listeningState: ListenState.FINISHED });

	}
	getCaption() {
		return this.props.selectedNode.name.replace(/\$[0-9]\|.*\$/,(v)=>this.getInputValue(v)).trim()
	}
	getPlugin() {
		let pluginPrefix = this.props.selectedNode.plugin ? this.props.selectedNode.plugin : "";
		return pluginPrefix;
	}
	render() {
		let lstate = this.state.listeningState;
		let children = [];
		if (lstate == ListenState.NOT_LISTENING) {
			// Show "Start Listening" button
			children.push(<div>
			<a onClick={()=>this.startListening()} className="listen-button">Start Hook Detection</a>
			</div>);
		} else if (lstate == ListenState.LISTENING) {
			children.push(<div>

				<div><p>Hook detection in progress. Navigate to a page on your site to retrieve a list of available hooks for that page</p>
				<p><h4>Found {(this.state.foundHooksCount||0)} hooks</h4></p></div>
			<a onClick={()=>this.stopListening()} className="listen-button">Stop Listening</a>
			</div>);
			// Show Progress
		} else if (lstate == ListenState.FINISHED) {
			// Show list of hooks
			let hooks = this.state.foundHooks ? this.renderHooks(this.state.foundHooks) : <div>Searching...</div>;
			children.push(<div>
				<h5>Hooks found:</h5>
				{ hooks }
			</div>);
		}
		return (<div>
			<div className="node-settings-plugin">{this.getPlugin()}</div>
			<h4 className="node-settings-title">{this.getCaption()} - Test</h4>
			<p>Navigate to a page on your site that will execute the current trigger</p>
			{children}
		</div>);
	}
}
const mapStateToProps = (state, ownProps) => {
	let node = state.nodes[ownProps.node];
	 return {
		selectedNode: node,
        settings: (node.fields.in||[]).filter(n=>n.type != 'flow')
    };
};

const mapDispatchToProps = dispatch => {
  return {
	  setExpression: (nodeId, pinId, value) =>  { dispatch(setExpression(nodeId,pinId,value));},
      onNodeEdit: id => {

          dispatch(editNode(id))
      }
  }
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(withCookies(NodeTest))
