import React from 'react';

import NodeField from './NodeField';
import Collapsible from 'react-collapsible';

export default class NodeFieldList extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			fieldTypes: {},
			visible: {}
		};
	}
	toggle( cat ) {
		let visible = this.state.visible;
		visible[cat] = ! visible[cat];
		this.setState({
			visible: visible
		});
	}
	onMouseUp( i ) {
		if ( this.props.onCompleteConnector ) {
			this.props.onCompleteConnector( i );
		}
	}
	onMouseDown( i ) {
		if ( this.props.onStartConnector ) {
			this.props.onStartConnector( i );
		}
	}
	render() {
		let i = 0;
		let type = this.props.type || 'input';
		let categories = this.props.items
			.filter( item => item.advanced )
			.map( item => item.advanced )
			.filter( ( item, pos, arr ) => arr.indexOf( item ) == pos );
		return (
			<div className={ 'node-connector-list-wrapper node-connector-list-wrapper--' + type }>
				<ul className={'node-connector-list node-connector-list--' + type}>
					{this.props.items.filter( item => ! item.advanced ).map( item => {
						if ( 'flow' == item.type ) {
							return null;
						}
						let itemType = item.type;
						if ( 0 === item.type.indexOf( '$' ) ) {
							let itemTypeRef = item.type.substring( 1 );
							if (
								this.props.fieldTypes &&
								this.props.fieldTypes[this.props.node.nid + '.' + itemTypeRef]
							) {
								itemType = this.props.fieldTypes[
									this.props.node.nid + '.' + itemTypeRef
								];
							}
						}
						return (
							<NodeField
								nodeIndex={this.props.nodeIndex}
								setExpression={this.props.setExpression}
								fetchNode={this.props.fetchNode}
								nodeid={this.props.node.nid}
								nodeindex={this.props.nodeIndex}
								isConnected={this.props.isConnected}
								setPinRef={this.props.setPinRef}
								onInputValueUpdated={this.props.onInputValueUpdated}
								onMouseUp={i => this.onMouseUp( i )}
								onMouseDown={i => this.onMouseDown( i )}
								key={i}
								index={i++}
								item={item}
								itemType={itemType}
							/>
						);
					})}
				</ul>
				{categories.map( cat => {
					return (
							<div>
									<h3 className='node-field-collapse-panel' onClick={() => this.toggle( cat )}>
										{cat}
										{this.state.visible[cat] ? (
												<i className='node-field-collapse-icon fa fa-chevron-up' />
										) : (
												<i className='node-field-collapse-icon fa fa-chevron-down' />
										)}
					</h3>
						{this.state.visible[cat] && (
								<ul className={'node-connector-list node-connector-list--' + type}>
									{this.props.items
										.filter( item => item.advanced == cat )
										.map( item => {
											if ( 'flow' == item.type ) {
												return null;
											}
											let itemType = item.type;
											if ( 0 === item.type.indexOf( '$' ) ) {
												let itemTypeRef = item.type.substring( 1 );
												if (
													this.props.fieldTypes &&
													this.props.fieldTypes[
														this.props.node.nid + '.' + itemTypeRef
													]
												) {
													itemType = this.props.fieldTypes[
														this.props.node.nid + '.' + itemTypeRef
													];
												}
											}
											return (
												<NodeField
													nodeIndex={this.props.nodeIndex}
													setExpression={this.props.setExpression}
													fetchNode={this.props.fetchNode}
													nodeid={this.props.node.nid}
													nodeindex={this.props.nodeIndex}
													isConnected={this.props.isConnected}
													setPinRef={this.props.setPinRef}
													onInputValueUpdated={this.props.onInputValueUpdated}
													onMouseUp={i => this.onMouseUp( i )}
													onMouseDown={i => this.onMouseDown( i )}
													key={i}
													index={i++}
													item={item}
													itemType={itemType}
												/>
											);
										})}
								</ul>
							)}
						</div>
					);
				})}
			</div>
		);
	}
}
