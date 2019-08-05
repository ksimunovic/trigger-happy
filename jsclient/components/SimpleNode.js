import React, {
	PropTypes
} from 'react';
import onClickOutside from 'react-onclickoutside';

import {
	selectTrigger,
	navigate,
	deleteNode,
	loadDataType
} from '../actions';

import {
	connect
} from 'react-redux';
import NodeFieldList from './NodeFieldList';

class SimpleNode extends React.Component {
	constructor( props ) {
		super( props );

		this.state = {
			selected: false,
			collapsed: props.collapsed || false,
			canCollapse: ! props.collapsed
		};


	}


	handleClick( e ) {
		this.setState({
			selected: true
		});
		if ( this.props.onNodeSelect ) {
			this.props.onNodeSelect( this.props.nid );
		}
	}
	getInputValue( inputIndex ) {
		let inputIndexParts = inputIndex.toString().replace( /\$/gi, '' ).split( '|' );

		inputIndex = parseInt( inputIndexParts[0]);
		if ( this.props.inputs.length > inputIndex ) {
			return this.props.inputs[inputIndex].value || inputIndexParts[1] || '';
		}
		return inputIndexParts[1] || '';
	}
	getCaption() {
		return this.props.node.title || this.props.node.name.replace( /\$[0-9]\|.*\$/, ( v ) => this.getInputValue( v ) ).trim();
	}
	handleClickOutside() {
		let {
			selected
		} = this.state;
		if ( this.props.onNodeDeselect && selected ) {
			this.props.onNodeDeselect( this.props.nid );
		}
		this.setState({
			selected: false
		});
	}
	updateInputValue( index, value ) {
		this.props.onInputValueUpdated( this.props.nid, index, value );
		this.forceUpdate();
	}

	toggleCollapsed( e ) {
		e.stopPropagation();
		this.setState({
			collapsed: ! this.state.collapsed
		});
		this.forceUpdate();
		if ( this.props.onCollapsed ) {
			this.props.onCollapsed( ! this.state.collapsed );
		}
	}
	deleteNode() {
		if ( this.props.deleteNode ) {
			this.props.deleteNode( this.props.nid );
		}
	}

	isCurrentStep( step ) {
		return this.props.panelType == step.page && Object.keys( this.props.panelOptions ).reduce( ( p, k ) => p && step.options[k] && this.props.panelOptions[k] == step.options[k], true );
	}
	render() {

		let {
			selected
		} = this.state;

		let nodeClass = 'node' + ( selected ? ' selected' : '' ) + ' node--' + this.props.node.cat + ' node--type_' + this.props.node.nodeType;
		if ( ! this.state.canCollapse ) {
			nodeClass += ' node--type_flow';
		}
		let pluginPrefix = this.props.node.plugin ? this.props.node.plugin : '';
		let nodeContent = '';


		nodeContent = (
			<div className="node-content">
				<NodeFieldList fetchNode={this.props.fetchNode} node={this.props.node} type="output" isConnected={( pinIndex )=>this.props.isConnected( this.props.nid, pinIndex, 'output' )} setPinRef={( i, r )=>this.props.setPinRef( r, this.props.nid, i, 'output' )} items={this.props.outputs || []} onStartConnector={( index )=>this.onStartConnector( index )} />
			</div>
		);
		nodeContent = '';

		let icon = this.state.canCollapse ? ( <i className={'collapse-icon fa  fa-chevron-circle-' + ( this.state.collapsed ? 'down' : 'up' )} onClick={this.toggleCollapsed.bind( this )} ></i> ) : '';
		if ( ! this.state.canCollapse ) {
			icon = ( <i className="collapse-icon fa fa-sign-out" /> );
		}
		let i = 0;
		let pluginIcon = this.props.getNodeIcon( this.props.nid );
		return (
			<div onDoubleClick={( e ) =>this.handleClick( e ) }className="node-wrapper">
				<section className={nodeClass} style={{zIndex: 10}} data-nid={this.props.nid}>
				{pluginIcon && (
					<div className={'node-icon node-icon--' + this.props.node.cat}>
						<img src="pluginIcon" />
					</div>
				)}
		{! pluginIcon && 'trigger' == this.props.node.nodeType && (
			<div className={'node-icon node-icon--' + this.props.node.nodeType}>
				<i className="fa fa-bolt"></i>
			</div>
		)}
		{! pluginIcon && 'action' == this.props.node.nodeType && (
			<div className={'node-icon node-icon--' + this.props.node.nodeType}>
				<i className="fa fa-tasks"></i>
			</div>
		)}

		{! pluginIcon && 'condition' == this.props.node.nodeType && (
			<div className={'node-icon node-icon--' + this.props.node.nodeType}>
				<i className="fa fa-question"></i>
			</div>
		)}
		<div className="node-details">
			<header className="node-header">
				<span className="node-tag">{this.props.node.nodeType}</span>
				<div className="node-header__top">
					<span className="node-title" >
					{this.props.nid}. {this.getCaption()}
					</span>
				</div>

				<div className="node-plugin">
					<div className="node-menu">
						{ this.props.node.description ? (
							<span className="node-description" >
							{this.props.node.description}
							</span>
						) : ''}
						{ this.props.steps && this.props.steps.filter( s=>! s.isReturn ).map( step=>{

							return ( <a key={'step--' + this.props.node.nid + '--' + i++} onClick={()=>this.props.navigate( step.page, step.options )} href="javascript:void(0)" className={'node-menu__item ' + ( this.isCurrentStep( step ) ? 'node-menu__item--selected' : '' )}>
								<i className={'fa ' + step.icon}></i> {step.text}
							</a> );
						})}
						<hr />
						{ 'trigger' !== this.props.node.nodeType ?
						<a key={'step--' + this.props.node.nid + '--delete'} onClick={()=>this.props.deleteNode( this.props.node.nid )} href="javascript:void(0)" className={'node-menu__item node-menu__item--delete '}>
							<i className="fa fa-trash"></i> Delete this {this.props.node.nodeType}
						</a>  :
						<a key={'step--' + this.props.node.nid + '--delete'} onClick={()=>this.props.selectTrigger( this.props.node.nid )} href="javascript:void(0)" className={'node-menu__item node-menu__item--delete ' + ( this.props.triggerSelected ? 'node-menu__item--selected' : '' )}>
							<i className="fa fa-refresh"></i> Change Trigger
						</a> }
					</div>
				</div>

			</header>
			{nodeContent}
			</div>
		</section>
		</div> );
	}
}


const mapStateToProps = ( state, ownProps ) => {
	return {
		triggerSelected: state.ui.addTrigger,
		panelType: state.ui.panelType,
		panelOptions: state.ui.panelOptions,
		steps: state.steps && state.steps[ownProps.nodeId],
		getNodeIcon: ( nid ) => {
			if ( ! state.definitions[state.nodes[nid].type].plugin || ! state.definitions[state.nodes[nid].type].plugin.icon ) {
				return '';
			}
			return state.definitions[state.nodes[nid].type].plugin.icon;
		}
	};
};

const mapDispatchToProps = dispatch => {
	return {
		navigate: ( step, options ) => dispatch( navigate( step, options ) ),
		deleteNode: ( nid ) => dispatch( deleteNode( nid ) ),
		selectTrigger: () => dispatch( selectTrigger() )
	};
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)( SimpleNode );
