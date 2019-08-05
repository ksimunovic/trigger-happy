import React from 'react';
import {
	buildExpression,
	parseExpression,
	stringifyExpression
} from '../lib/util';

import {
	connect
} from 'react-redux';
import QuickSearch from './QuickSearch';
import {
	loadDataType,
	setFieldType
} from '../actions';

class ExpressionSelectBox extends React.Component {
	constructor( props ) {
		super( props );
		if ( 'string' == typeof props.value ) {
			let matches = props.value.match( /\{\{_N([0-9]*).([^\}]*)\}\}/i );
			if ( matches ) {
				this.state = ({
					text: props.getNodeLabel( matches[1]) + ' - ' + matches[2],
					id: props.value,
					expr: parseExpression( props.value )
				});
			} else {
				this.state = {
					id: props.value,
					expr: parseExpression( props.value )
				};
			}
		} else {
			this.state = ({
				text: props.value.text,
				id: props.value.id
			});
		}
	}
	componentWillReceiveProps( props ) {
		if ( props.value ) {
			if ( 'string' == typeof props.value ) {
				let matches = props.value.match( /\{\{_N([0-9]*).([^\}]*)\}\}/i );
				if ( matches ) {
					this.setState({
						text: props.getNodeLabel( matches[1]) + ' - ' + matches[2],
						id: props.value,
						expr: parseExpression( props.value )
					});
				}
			} else {
				this.setState({
					text: props.value.text,
					id: props.value.id
				});
			}
		}
		for ( let i in props.availableFields ) {
			for ( let f in props.availableFields[i].fields ) {
				if ( ! props.datatypes[props.availableFields[i].fields[f].type]) {
					props.loadDataType( props.availableFields[i].fields[f].type );
				}
			}
		}
	}
	componentDidMount() {
		for ( let i in this.props.availableFields ) {
			for ( let f in this.props.availableFields[i].fields ) {
				this.props.loadDataType( this.props.availableFields[i].fields[f].type );
			}
		}
	}
	insertField( node, field, subprop, subtype ) {
		this.setState({
			quickSearch: false
		});
		let expression = field.name;
		if ( 'object' == typeof subprop ) {
			for ( let i in subprop ) {
				if ( 2 < i ) {
					expression += '.' + subprop[i];
				}
			}
		} else if ( subprop ) {
			expression += '.' + subprop;
		}
		this.setState({
			fieldType: subtype || field.type
		});

		this.setState({
			text: expression,
			id: '{{_N' + node.nid + '.' + expression + '}}',
			expr: parseExpression( '{{_N' + node.nid + '.' + expression + '}}' )
		});
		if ( this.props.onSetFieldType ) {
			this.props.onSetFieldType( subtype || field.type );
		}
		let nid = node.nid;
		if ( 'Global' == nid ) {
			nid = '';
		}
		if ( this.props.onSelect ) {
			this.props.onSelect( node, field, '{{_N' + nid + '.' + expression + '}}', subtype || field.type, expression );
		}
		if ( this.props.onChange ) {
			this.props.onChange( '{{_N' + node.nid + '.' + expression + '}}' );
		}
	}

	clearValue() {
		this.setState({
			text: null,
			id: null
		});
		this.props.onChange( null );
	}
	selectChoice( node, field ) {
		this.setState({
			quickSearch: false
		});

		this.insertField( node );


	}
	toggleQuickSearch() {
		this.loadOptions();
		this.setState({
			quickSearch: ! this.state.quickSearch,
			quickSearchFilter: null
		});
	}
	toggleDropdown() {
		this.loadOptions();
		this.setState({
			dropdown: ! this.state.dropdown
		});
	}
	loadOptions() {}
	ensureSelected() {
		if ( ! this.props.dataTypeChoices ) {
			return;
		}
		let avail = this.props.dataTypeChoices.filter( n => null == this.state.quickSearchFilter || 0 <= n.text.indexOf( this.state.quickSearchFilter ) );
		if ( 0 < avail.length ) {
			this.selectChoice( avail[0]);
		} else {
			this.clearValue();
		}
		this.setState({
			quickSearch: false,
			quickSearchFilter: null
		});
	}
	updateValue( event ) {
		this.setState({
			text: event.target.value,
			quickSearch: true,
			quickSearchFilter: event.target.value
		});

	}
	toggleOperations() {
		this.setState({
			showOperations: ! this.state.showOperations
		});
	}
	updateSelectedOperation( v ) {
		this.setState({
			selectedOperation: v
		});
	}
	setOperationValue( key, value ) {
		let operationValues = this.state.operationValues || {};
		operationValues[key] = value;
		let fields = '';
		let args = [];
		for ( let i in operationValues ) {
			let prefix = ( '' == fields ? '' : ',' );
			fields += prefix + '\'' + operationValues[i] + '\'';
			args.push({
				raw: '\'' + operationValues[i] + '\'',
				type: 'Literal',
				value: operationValues[i]
			});
		}
		let expr = parseExpression( this.state.id );
		if ( 'CallExpression' == expr.type ) {
			expr = expr.callee.object;
		}
		expr = {
			type: 'CallExpression',
			callee: {
				type: 'MemberExpression',
				object: expr,
				property: {
					type: 'Identifier',
					name: this.state.selectedOperation
				}
			},
			arguments: args
		};
		let dataa = stringifyExpression( expr );
		let matches = dataa.match( /\{\{_N([0-9]*).([^\}]*)\}\}/i );
		this.setState({
			operationValues,
			text: this.props.getNodeLabel( matches[1]) + ' - ' + matches[2],
			id: dataa
		});
		if ( this.props.onChange ) {
			this.props.onChange( dataa );
		}
		if ( this.props.onSelect ) {
			this.props.onSelect( null, null, dataa, 'string', this.props.getNodeLabel( matches[1]) + ' - ' + matches[2]);
		}
	}
	render() {

		let allowExpressions = false;
		let operations = false;
		let fieldTypeMethods = this.props.schemas[this.state.fieldType] && this.props.schemas[this.state.fieldType].methods;
		if ( fieldTypeMethods ) {
			operations = true;
		}
		return (
			<div>
				<div className={'node-control-container node-control-expr-select ' + ( this.props.errorMessage && 'node-error' || '' )}>
					<div ref={( el ) => this.element = el }  className="node-editable-setting">
						<input type="text" readOnly={this.props.lookupOnly} value={this.state.text} className="node-editable-text" onBlur={( v )=>this.ensureSelected()} onChange={( v )=>this.updateValue( v )} />
					</div>
					{operations && <a href="javascript:void(0)" className="insert-button" onClick={()=>this.toggleOperations()}><i className="fa fa-cogs"></i></a>}
					{allowExpressions && (
						<a href="javascript:void(0)" className="insert-button" onClick={()=>this.toggleQuickSearch()}>
							<i className="fa fa-crosshairs"></i>
						</a>
					)}
					{! allowExpressions && (
						<a href="javascript:void(0)" className="insert-button" onClick={()=>this.toggleQuickSearch()}>
						<i className="fa fa-caret-down"></i>
						</a>
					)}
				</div>
				{this.state.showOperations && (
					<div className="triggerhappy-operations">
						<label>Operation Type</label>
						<select onChange={e=>this.updateSelectedOperation( e.target.value )}>
							<option value="">None</option>
							{Object.keys( fieldTypeMethods ).map( m => <option value={m}>{fieldTypeMethods[m].description}</option> )}
						</select>
						{this.state.selectedOperation && null != this.state.selectedOperation && (
							<div>
							<hr />
							{Object.keys( fieldTypeMethods[this.state.selectedOperation].fields ).map( f=>(
								<div>
									<label>{f}</label>
									<input type="text" onChange={( e )=>this.setOperationValue( f, e.target.value )} value={this.state.operationValues && this.state.operationValues[f] || ''} />
									<div className="desc">{fieldTypeMethods[this.state.selectedOperation].fields[f].description}</div>
									</div>
								)
							)}
							</div>
						)}
						<a className="button button-small">OK</a>
					</div>
				)}
				<QuickSearch
					allowTypes={this.props.type}
					allowExpressions={allowExpressions}
					className="node-quick-search"
					insertField={this.insertField.bind( this )}
					resetControlTypeClicked={this.props.resetControlTypeClicked}
					show={this.state.quickSearch}
					availableFields={this.props.availableFields}
					schemas={this.props.schemas}
					getNodeIcon={this.props.getNodeIcon}
				/>
			</div>
		);
	}
}

const mapStateToProps = ( state, ownProps ) => {
	let schemas = {};
	for ( let i in ownProps.availableFields ) {
		let fields = ownProps.availableFields[i];
		for ( let f in fields.fields ) {
			let type = fields.fields[f].type;
			schemas[type] = state.datatypes[type] && state.datatypes[type].schema || null;
		}
		schemas.array = {
			methods: {
				'getItem': {
					description: 'Get Item',
					'type': 'number',
					'fields': {
						'key': {
							'type': 'string',
							'description': 'The item key'
						}
					}
				}
			}
		};
	}
	return {
		getNodeIcon: ( nid ) => {
			if ( ! state.nodes[nid] || ! state.definitions[state.nodes[nid].type].plugin || ! state.definitions[state.nodes[nid].type].plugin.icon ) {
				return '';
			}
			return state.definitions[state.nodes[nid].type].plugin.icon;
		},
		getNodeLabel: ( nid ) => 0 == nid ? 'Global' : ( state.nodes[nid].name ),
		schemas,
		datatypes: state.datatypes
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
)( ExpressionSelectBox );
