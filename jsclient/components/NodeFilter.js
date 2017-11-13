import React from 'react';

import {
	setExpression
} from '../actions';
import {
	ExpressionEditor,
	SelectBox,
	ExpressionSelectBox,
	RichEditor,
	AutoSuggest
} from '../controls';
import {
	loadDataType
} from '../actions';
import {
	connect
} from 'react-redux';
class NodeFilter extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			value: {},
			fieldType: 'string'
		};
		if ( props.filter ) {

			this.state = Object.assign({}, this.state, props.filter );
			if ( props.filter.left ) {
				let fieldType = this.getFieldTypeFromExpression( props.filter.left.expr );
				this.state = Object.assign({}, this.state, {
					fieldType: fieldType
				});

			}
		}
	}
	componentWillReceiveProps( props ) {
		if ( props.filter ) {
			this.setState( props.filter );
			if ( props.filter.left ) {
				let fieldType = this.getFieldTypeFromExpression( props.filter.left.expr );

				this.setState({
					fieldType: fieldType
				});
			}
		}
	}
	getFieldTypeFromExpression( expr ) {
		let results = expr.match( /\{\{_N([0-9]*)\.([^\}]*)*\}\}/i );
		if ( ! results ) {
			return '';
		}
		let nodeId = results[1];
		let path = results[2].split( '.' );
		let nextPart = path.shift();
		let nodeType = '' == nodeId ? null : this.props.getNodeFieldType( nodeId, nextPart );

		while ( 0 < path.length ) {
			nextPart = path.shift();
			let schema = this.props.getPropsForType( nodeType );
			if ( null == schema ) {
				return nodeType;
			}
			nodeType = schema[nextPart] && schema[nextPart].type;

		}
		return nodeType;

	}
	selectLeftValue( n, v, e, t, text ) {

		let type = t || v.type;

		this.setState({
			fieldType: type
		});
		this.props.loadDataType( type );
		let newState = {
			left: {
				expr: e,
				display: text
			}
		};

		if ( 'boolean' == type ) {
			newState.right = {
				expr: true,
				display: 'Yes'
			};
			newState.op = 'equals';
		}
		this.setState( newState );
		this.updateFilter( newState );
	}
	selectRightValue( n, v, e, t, text ) {

		this.setState({
			right: {
				expr: e,
				display: text
			}
		});
		this.updateFilter({
			right: {
				expr: e,
				display: text
			}
		});
	}
	selectOperator( v ) {
		this.setState({
			op: v
		});
		this.updateFilter({
			op: v
		});
	}
	updateFilter( filterState ) {

		let state = {
			right: this.state.right,
			left: this.state.left,
			op: this.state.op
		};

		this.props.onChange( Object.assign( state, filterState ) );
	}
	getLeftValueForSelect() {
		if ( this.state.left ) {

			return {
				id: this.state.left.expr,
				text: this.state.left.display
			};
		}
		return {};
	}

	getRightValueForSelect() {
		if ( this.state.right ) {

			return {
				id: this.state.right.expr,
				text: this.state.right.display
			};
		}
		return {};
	}

	getRightValueId() {
		return this.state.right && this.state.right.expr || '';
	}

	getOpValue() {
		if ( this.state.op ) {

			return {
				id: this.state.op
			};
		}
		return {};
	}
	render() {

		let choices = [ {
				id: 'equals',
				text: '='
			},

			{
				id: 'notequals',
				text: '<>'
			},
			{
				id: 'greaterThan',
				text: '<'
			},
			{
				id: 'lessThan',
				text: '>'
			},
			{
				id: 'notnull',
				text: 'Not Null'
			},
			{
				id: 'contains',
				text: 'Contains'
			}, {
				id: 'startsWith',
				text: 'Starts With'
			}, {
				id: 'endsWith',
				text: 'Ends With'
			}
		];


		let simpleDataType = this.props.dataTypes[this.state.fieldType];
		let isBool = 'boolean' == this.state.fieldType;
		return (
			<div className="node-filter-row">
				<div className="node-filter-item">
					<ExpressionSelectBox
						controlTypeOverrideText={this.state.controlTypeOverrideText}
						customValueControlTypeClicked={()=>this.useControlType( 'string' )}
						resetControlTypeClicked={()=>this.useControlType( null )}
						availableFields={this.props.availableFields}

						fetchNode={this.props.fetchNode}
						nodeId={null}
						onSelect={( n, v, e, t, text )=>this.selectLeftValue( n, v, e, t, text )}
						value={this.getLeftValueForSelect()}
						ref={( control )=>this.control = control}
					/>
				</div>
				<div className="node-filter-item node-filter-item--small">
					<SelectBox notNull={true} showID={false} value={this.getOpValue()}  onChange={( v )=>this.selectOperator( v )} allowCustomValue={false} dataTypeChoices={choices} />
				</div>
				<div className="node-filter-item node-filter-item--large">
					{ simpleDataType ?
						( simpleDataType.ajax  ?
							<AutoSuggest notNull={true} value={this.getRightValueForSelect()} showID={true} onChange={({id, text})=>this.selectRightValue( null, null, id, null, text )} allowSearch={true}  allowCustomValue={false} type={this.state.fieldType} />  :
							<SelectBox allowSearch={! isBool} value={this.getRightValueForSelect()} onChange={( v )=>this.selectRightValue( null, null, v, null, null )} showID={false} allowCustomValue={false} notNull={isBool} type={this.state.fieldType} />
						) : (
							<input type="text" value={this.getRightValueId()} onChange={( e )=>this.selectRightValue( null, null, e.target.value, null, e.target.value )} />
						)
					}
				</div>
			</div>
		);
	}
}

const mapStateToProps = ( state, ownProps ) => {

	return {
		getPropsForType: ( type ) => {
			if ( null == type ) {
				return state.globals;
			}
			let dt = state.datatypes[type];
			if ( dt ) {
				return dt.schema && dt.schema.properties || null;
			}
			return null;
		},
		getNodeFieldType: ( nid, field ) => {
			let allFields = [];
			if ( state.nodes[nid]) {
				allFields = state.nodes[nid].fields;

				for ( let f in allFields ) {
					if ( allFields[f].name == field ) {
						return allFields[f].type;
					}
				}
			}
			return '';
		},
		dataTypes: state.datatypes,
		getNodeLabel: ( nid ) => ( state.nodes[nid].name )
	};
};

const mapDispatchToProps = dispatch => {
	return {

		loadDataType: dataTypeId => dispatch( loadDataType( dataTypeId ) )
	};
};

export default connect(
	mapStateToProps,
	mapDispatchToProps, null, {
		withRef: true
	}
)( NodeFilter );
