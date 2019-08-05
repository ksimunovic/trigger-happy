import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import OperationsPanel from './OperationsPanel';
import React from 'react';
import { buildExpression, parseExpression, stringifyExpression } from '../lib/util';
import { connect } from 'react-redux';
import QuickSearch from './QuickSearch';
import { loadDataType } from '../actions';


class RichEditor extends React.Component {
	constructor( props ) {
		super( props );
		this.nodeTags = {};
		this.charMap = {};
		this.state = {
			value: props.value,
			operationFieldType: ''
		};
	}
	createTagNode( nodeId, path, type ) {

		let icon = this.props.getNodeIcon( nodeId );

		let parts = path.split( '.' );
		let expr = '<span spellcheck=\'false\' data-type=\'' + type + '\' class=\'node-expression-data-tag\' data-triggerhappy-tag=\'_N' + nodeId + '.' + path + '\' contenteditable=\'false\' data-id=\'' + nodeId + '\'>';
		if ( icon ) {
			expr += '<img src="' + this.props.getNodeIcon( nodeId ) + '" class="expression-icon" />';
		}
		expr += '<span  class="node-expression-data-tag__node">#' + nodeId + '</span>';

		for ( let i in parts ) {
			let p = parts[i];
			let text = p;
			let result = text.replace( /([A-Z])/g, ' $1' );
			result = result.replace( /_([a-z])/g, function( r, m ) {
				return ' ' + m.toUpperCase();
			});
			let finalResult = result.charAt( 0 ).toUpperCase() + result.slice( 1 );
			expr += '<i class="fa fa-chevron-right"></i>';
			expr += '<span class="node-expression-data-tag__prop">' + finalResult + '</span>';
		}
		expr += '</span>';

		this.nodeTags[nodeId + '.' + path] = {
			html: expr,
			nodeId: nodeId,
			path: path,
			type: type
		};
		return expr;
	}
	componentWillUnmount() {
		if ( this.editor && this.editor.dom ) {
			this.editor.remove();
		}
	}
	updateExpressionWithOperation( expression, operationName, operationValues ) {


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

		let expr = parseExpression( expression );

		if ( 'CallExpression' == expr.type ) {
			expr = expr.callee.object;
		}
		if ( operationName ) {
			expr = {
				type: 'CallExpression',
				callee: {
					type: 'MemberExpression',
					object: expr,
					property: {
						type: 'Identifier',
						name: operationName
					}
				},
				arguments: args
			};
		}
		let dataa = stringifyExpression( expr );

		return dataa;


	}
	componentDidMount() {
		let id = ( this.textbox.getAttribute( 'id' ) );
		let self = this;

		window.setTimeout(function() {
		wp.editor.initialize( id, {
			wpautop: false,

			tinymce: {
				plugins: wp.editor.getDefaultSettings().tinymce.plugins + ',wpflowexpression',

				setup: function( editor ) {
					self.editor = editor;
					self.editor.mountOperationsPanel = function( selectedExpressionElement, mountElement ) {
						let currentExpression = '{{' + self.editor.dom.getAttrib( selectedExpressionElement, 'data-triggerhappy-tag' ) + '}}';
						let updateSelectedOperation = function( op ) {
							self.editor.dom.setAttrib( selectedExpressionElement, 'data-triggerhappy-operation-values', '' );
							self.editor.dom.setAttrib( selectedExpressionElement, 'data-triggerhappy-operation', op );
							let newExpr = self.updateExpressionWithOperation( self.editor.dom.getAttrib( selectedExpressionElement, 'data-triggerhappy-tag' ), op, {});
							self.editor.dom.setAttrib( selectedExpressionElement, 'data-triggerhappy-tag', newExpr.replace( '{{', '' ).replace( '}}', '' ) );
							let newHtml = self.replaceSpecialCharsWithTags( newExpr, true );
							selectedExpressionElement.innerHTML = self.editor.dom.$( newHtml ).html();
							renderOp();
						};
						let setOperationValue = function( f, v ) {
							let values = self.editor.dom.getAttrib( selectedExpressionElement, 'data-triggerhappy-operation-values' );
							if ( values ) {
								values = JSON.parse( values );
							} else {
								values = {};
							}
							values[f] = v;
							let op = self.editor.dom.getAttrib( selectedExpressionElement, 'data-triggerhappy-operation' );
							self.editor.dom.setAttrib( selectedExpressionElement, 'data-triggerhappy-operation-values', JSON.stringify( values ) );
							let newExpr = self.updateExpressionWithOperation( self.editor.dom.getAttrib( selectedExpressionElement, 'data-triggerhappy-tag' ), op, values );
							self.editor.dom.setAttrib( selectedExpressionElement, 'data-triggerhappy-tag', newExpr.replace( '{{', '' ).replace( '}}', '' ) );
							let newHtml = self.replaceSpecialCharsWithTags( newExpr, true );
							selectedExpressionElement.innerHTML = self.editor.dom.$( newHtml ).html();
							renderOp();
						};
						let renderOp = function() {

							let selectedType = self.editor.dom.getAttrib( selectedExpressionElement, 'data-type' );
							let fieldTypeMethods = self.props.schemas[selectedType] && self.props.schemas[selectedType].methods;
							let opType = self.editor.dom.getAttrib( selectedExpressionElement, 'data-triggerhappy-operation' ) || '';
							let opValues = self.editor.dom.getAttrib( selectedExpressionElement, 'data-triggerhappy-operation-values' ) || '{}';
							opValues = JSON.parse( opValues );

							if ( fieldTypeMethods ) {
								ReactDOM.render(
									<Provider store={window.TH.nodeStore}>
										<OperationsPanel
											updateSelectedOperation={( op )=>updateSelectedOperation( op )}
											selectedOperation={opType}
											operationX={0}
											fieldType={selectedType}
											operationY={0}
											operationValues={opValues}
											setOperationValue={( f, v )=>setOperationValue( f, v )}
										/>
									</Provider>,
									mountElement
								);
							}
						};
						renderOp();
					};
					editor.settings.toolbar1 += ',insertExpr';
					editor.settings.content_css += ',' + TH.expression_css_url; // eslint-disable-line camelcase

					editor.addButton( 'insertExpr', {
						text: '',
						icon: 'crosshairs',
						onclick: function() {
							self.setState({
								quickSearch: ! self.state.quickSearch,
								quickSearchText: ''
							});
						}
					});

					editor.on( 'beforesetcontent', function( e ) {
						e.content = self.replaceSpecialCharsWithTags( e.content, true );
					});
					editor.on( 'postProcess', function( e ) {
						e.content = self.unreplaceTags( e.content );
					});
					editor.on( 'change', function( event, v ) {
						self.props.onChange( editor.getContent() );
					});
					editor.on( 'keyup', function( event, v ) {
						self.props.onChange( editor.getContent() );
					});


				}
			},
			quicktags: true
		});
	},10);
		for ( let i in this.props.availableFields ) {
			for ( let f in this.props.availableFields[i].fields ) {
				this.props.loadDataType( this.props.availableFields[i].fields[f].type );
			}
		}
	}


	unreplaceTags( val, isString ) {

		val = val.replace( /<span[^<]*data-triggerhappy-tag=['"](.*?)['"].*<\/span>.*<\/span>/gi, function( d, m ) {
			return '{{' + m + '}}';
		});
		return val;
	}

	replaceSpecialCharsWithTags( val, isString ) {

		return String( val ).replace( /\{\{_N(.*?)\.(.*?)\}\}/g, function( tag, m1, m2 ) {

			return this.createTagNode( m1, m2 );

		}.bind( this ) );
	}

	updateSelectedOperation( v ) {
		this.setState({
			selectedOperation: v
		});
		let tagId = this.state.operationTagId;
		if ( '' == v ) {
			v = null;
		}
		this.updateExpressionWithOperation( tagId, v, {});
	}
	setOperationValue( key, value ) {
		let tagId = this.state.operationTagId;
		let nodeTag = this.nodeTags[tagId];


		let operationValues = nodeTag.operationValues || {};
		if ( key ) {
			operationValues[key] = value;
		}
		this.updateExpressionWithOperation( tagId, this.state.selectedOperation, operationValues );
	}

	textChange( e, v ) {

		this.props.onChange( e.target.value );
	}
	insertField( node, field, subprop, type ) {

		let fieldText = field.name;
		if ( subprop ) {
			if ( ! _.isArray( subprop ) ) {
				subprop = [ subprop ];
			}

			fieldText += '.' + _.join( subprop, '.' );

		}

		let expr = this.createTagNode( node.nid, fieldText, type );

		this.editor.execCommand( 'mceInsertContent', false, expr );
		this.setState({
			quickSearch: false
		});
	}
	render() {

		return (
			<div ref={( el ) => {
 this.element = el;
}}  className={'node-editable-setting ' + ( this.props.errorMessage && 'node-error' || '' )}>
				<textarea id={'expr_' + this.props.id } className="wp-editor-area" name={'expr_' + this.props.id} type="text" ref={el=>this.textbox = el} onChange={( e )=>this.textChange( e )} value={this.props.value}></textarea>
				<QuickSearch className="node-quick-search node-quick-search--tinymce"
				insertField={this.insertField.bind( this )}
				resetControlTypeClicked={this.props.resetControlTypeClicked}
				show={this.state.quickSearch}
				availableFields={this.props.availableFields}
				schemas={this.props.schemas}
				getNodeIcon={this.props.getNodeIcon}
				canUseField={( field, node )=>{

					return ! field.visibleTo || 0 <= field.visibleTo.indexOf( this.props.name ) && node.nid == this.props.nodeId;
				}}
				/>
			</div>

		);
	}

}

const mapStateToProps = ( state, ownProps ) => {
	let dataTypeChoices = false;
	let schemas = {};
	for ( let i in ownProps.availableFields ) {
		let fields = ownProps.availableFields[i];

		for ( let f in fields.fields ) {
			let type = fields.fields[f].type;
			schemas[type] = state.datatypes[type] && state.datatypes[type].schema || null;
		}
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
	schemas.string = {
		methods: {
			'toUpperCase': {
				description: 'To Upper Case',
				'type': 'string',
				'fields': {

				}
			},
			'toLowerCase': {
				description: 'To Lower Case',
				'type': 'string',
				'fields': {

				}
			}
		}
	};
	return {
		getNodeIcon: ( nid ) => {
			if ( ! state.nodes[nid] || ! state.definitions[state.nodes[nid].type].plugin || ! state.definitions[state.nodes[nid].type].plugin.icon ) {
				return '';
			}
			return state.definitions[state.nodes[nid].type].plugin.icon;
		},
		getNodeLabel: ( nid ) => 0 == nid ? 'Global' : ( state.nodes[nid].name ),
		schemas
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
)( RichEditor );
