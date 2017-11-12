import React from 'react';
import {
	buildExpression
} from '../lib/util';
import {
	ExpressionEditor,
	SelectBox,
	ExpressionSelectBox,
	RichEditor
} from '../controls';
import {
	loadDataType,
	setFieldType
} from '../actions';
import {
	connect
} from 'react-redux';


const valueTypes = [
	'number',
	'string',
	'boolean',
	'date',
	'datetime'
];
class NodeInputListItem extends React.Component {
		constructor( props ) {
			super( props );
			let value = this.props.value;
			if ( false == Array.isArray( value ) ) {
				value = [ value ];
			}
			this.state = {
				hover: false,
				edit: !! this.props.item.value,
				value: this.props.value
			};
		}
		onMouseUp( e ) {
			e.stopPropagation();
			e.preventDefault();
			this.props.onMouseUp( this.props.index );
		}
		onMouseDown( e ) {
			e.stopPropagation();
			e.preventDefault();
			this.props.onMouseDown( this.props.index );
		}

		onMouseOver() {
			this.setState({
				hover: true
			});
		}
		onMouseOut() {
			this.setState({
				hover: false
			});
		}
		noop( e ) {
			e.stopPropagation();
			e.preventDefault();
		}
		isConnected() {
			return false;
		}
		getTypeIcon() {
			if ( this.state.edit ) {
				return 'fa-pencil-square';
			}
			if ( this.props.item.type ) {
				switch ( this.props.item.type ) {
					case 'flow':
						return 'fa-sign-in';
						return this.isConnected() ? 'fa-square ' : 'fa-square-o ';
					default:
				}
			}
			if ( this.isConnected() ) {
				return 'fa-circle';
			}
			return 'fa-circle-o';
		}
		edit() {
			this.setState({
				'edit': true
			});
		}
		updateValue( index, e ) {
			let value = e.target.value;
			let valState = this.state.value;
			valState[index] = e.target.value;
			this.setState({
				value: valState
			});
		}
		addValueOption() {
			if ( Array.isArray( this.state.value ) ) {
				let newValues = this.state.value;
				newValues.push( '' );
				this.setState({
					value: newValues
				});
			} else {
				let newValues = [ this.state.value ];
				newValues.push( '' );
				this.setState({
					value: newValues
				});
			}
		}
		renderInputs() {
			let values = [ '' ];
			if ( Array.isArray( this.state.value ) ) {
				values = this.state.value;
			} else {
				values = [ this.state.value ];
			}
			let inputs = [];
			for ( let v in values ) {
				let value = values[v];
				inputs.push( <div><input value={value} onChange={this.updateValue.bind( this, v )} type='text' style={{fontSize: 10, width: '100%'}} />	<i onClick={this.addValueOption.bind( this )} className="fa fa-plus flow-add-array-element-icon" /></div> );
			}
			return inputs;
		}
		getControl( type ) {
			if ( this.state.controlTypeOverride ) {
				type = this.state.controlTypeOverride;

			}

			if ( 0 == type.indexOf( '@' ) ) {
				this.lookupOnly = true;
				type = type.substring( 1 );
				return ExpressionSelectBox;
			} else {
				this.lookupOnly = false;
			}

			if ( this.props.item.choices ) {
				return SelectBox;
			}
			switch ( type ) {
				case 'string':
					return ExpressionEditor;
				case 'html':
					return RichEditor;
				case 'number':
					return ExpressionEditor;
				case 'array':
					return ExpressionEditor;
			}
			return SelectBox;
		}
		getType() {
			let derivedType = this.props.itemType;
			if ( 0 == this.props.itemType.indexOf( '$' ) ) {
				derivedType = this.props.fieldTypes[this.props.nodeid + '.' + this.props.itemType.replace( '$', '' )] || this.props.itemType;
			}
			return derivedType;
		}
		useControlType( type ) {
			this.setState({
				controlTypeOverride: type,
				controlTypeOverrideText: null == type ? null : 'Use lookup value'
			});
		}
		render() {
			let {
				name
			} = this.props.item;
			if ( 'flow' == this.props.item.type ) {
				name = '';
			}
			let {
				hover
			} = this.state;
			let type = this.props.itemType;
			let Control = this.getControl( type );

			let allowExpressions = Control == ExpressionEditor;
			let errorMessage = this.props.getErrorMessageFor( this.props.nodeid, this.props.item.name );
			return (
				<li>
					<a onClick={( e )=>this.edit( e )} onMouseUp={( e )=>this.onMouseUp( e )} onMouseDown={( e )=>this.onMouseDown( e )} href="javascript:void(0)" className={'node-connector node-connector--' + ( this.props.item.type || 'basic' )}>
						<span>
							<strong>{this.props.item.label || this.props.item.name}</strong>
						</span>
					</a>
					<p className="node-connector-description">
						{this.props.item.description}
					</p>
					<Control
						errorMessage={errorMessage}
						onSetFieldType={( type )=> {
							this.props.setFieldType( this.props.nodeid, this.props.item.name, type );
						}}
						controlTypeOverrideText={this.state.controlTypeOverrideText}
						customValueControlTypeClicked={()=>this.useControlType( 'string' )}
						resetControlTypeClicked={()=>this.useControlType( null )}
						availableFields={this.props.availableFields}
						lookupOnly={this.lookupOnly}
						fetchNode={this.props.fetchNode}
						nodeId={this.props.nodeid}
						name={this.props.item.name}
						key={this.props.nodeid + '---' + this.props.item.name}
						id={this.props.nodeid + '---' + this.props.item.name}
						onChange={( v )=>this.setValue( v )}
						dataTypeChoices={this.getItemChoices()}
						type={this.props.itemType}
						value={this.props.value}
						ref={( control )=>this.control = control}
					/>
					{errorMessage && ( <div className="node-error-text">{errorMessage}</div> )}
				</li>
			);
		}
		getItemChoices() {
			let dataTypeChoices = this.props.dataTypes[this.props.itemType];
			if ( dataTypeChoices && dataTypeChoices.choices ) {
				dataTypeChoices = dataTypeChoices.choices;
			} else {
				dataTypeChoices = [];
			}
			let initialChoices = this.props.item.choices || [];
			let additionalFields = [];
			let af = this.props.availableFields;
			for ( let i in af ) {
				let node = af[i];
				for ( let x in node.fields ) {
					let field = node.fields[x];
					if ( field.type == this.props.itemType ) {
						additionalFields.push({
							id: '{{_N' + node.nid + '.' + field.name + '}}',
							text: '#' + node.nid + ' - ' + field.name
						});
					}
				}
			}
			return [ ...initialChoices, ...dataTypeChoices, ...additionalFields ];

		}
		setValue( v ) {
			this.props.setExpression( this.props.nodeid, this.props.item.name, v );
		}
		insertField( node, field ) {
			this.setState({
				quickSearch: false
			});
			if ( this.control ) {
				this.control.getWrappedInstance().insertField( '{{_N' + node.nid + '.' + field.name + '}}' );
			}


		}
	}


		const mapStateToProps = ( state, ownProps ) => {
			let dataTypeChoices = false;
			let availableFields = Object.values( state.nodes ).filter( ( el, i ) => {
				let def = state.definitions[el.type];

				return i < ownProps.nodeIndex || ( def.fields && def.fields.filter( r => 'start' == r.dir ).length ) || true;
			}).map( ( n, i ) => {
				let def = state.definitions[n.type];
				let allFields = [];
				if ( def && def.fields ) {
					allFields = ( def.fields && def.fields.filter( f => 'flow' !== f.type && ( 'out' == f.dir || 'start' == f.dir ) ) );
				}
				return {
					nid: n.nid,
					name: n.type,
					name2: def.name,
					plugin: def.plugin,
					fields: allFields
				};
			});
			let globalFields = Object.values( state.globals );
			availableFields.push({
				nid: '',
				name: '',
				name2: '',
				plugin: '',
				fields: globalFields,
				type: 'start'
			});


			let node = state.nodes[ownProps.nodeid];

			return {
				dataTypes: state.datatypes,
				fieldTypes: state.fieldTypes,
				getErrorMessageFor: ( nodeid, fieldName ) => state.ui.errors && state.ui.errors[nodeid] && state.ui.errors[nodeid][fieldName],
				dataTypeChoicesLoading: state.ui.loadingDataTypes || false,
				value: ( state.nodes[ownProps.nodeid].expressions && state.nodes[ownProps.nodeid].expressions[ownProps.item.name] || '' ),
				availableFields: availableFields

			};
		};

		const mapDispatchToProps = dispatch => {
			return {
				setFieldType: ( nodeId, fieldName, fieldType ) => dispatch( setFieldType( nodeId, fieldName, fieldType ) )
			};
		};

		export default connect(
			mapStateToProps,
			mapDispatchToProps
		)( NodeInputListItem );
