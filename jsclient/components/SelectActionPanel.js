import React from 'react';
import NodeConnectorList from './NodeConnectorList';
import {setExpression,showSelectPluginActions} from '../actions';
import {fetchActions} from '../actions';
import { connect } from 'react-redux';
class SelectActionPanel extends React.Component {
	constructor(props) {
		super(props);

	}
	componentDidMount() {
		
		this.props.loadActions(this.props.selectedPlugin);
	}

	render() {
		return (<div>
			<div className="node-settings-plugin">Add New</div>
			<h4 className="node-settings-title">Select Action</h4>
			<div className="action-list">
			{this.props.actions.map(p=>
			(
				<div className="action-item" onClick={this.props.selectAction}>
					<strong>{p.name}</strong>
					<div>{p.description}</div>
				</div>
			))}
			</div>

		</div>);
	}
}
const mapStateToProps = state => {

     return {
		 selectedPlugin: state.ui.selectedPlugin,
		 actions: state.actions && state.actions.actions || []
    };
};

const mapDispatchToProps = (dispatch,props) => {

  return {
	  loadActions: (plugin) => { return dispatch(fetchActions(plugin)); },
	  selectPlugin:  (plugin)=> { return dispatch(showSelectPluginActions(plugin)); }
  }
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(SelectActionPanel)
