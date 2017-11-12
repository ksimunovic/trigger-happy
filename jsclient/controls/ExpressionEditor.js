import React from 'react';
import QuickSearch from './QuickSearch';
import OperationsPanel from './OperationsPanel';
import {
	buildExpression,
	parseExpression,
	stringifyExpression,
	getTypeFromExpression
} from '../lib/util';
import CodeMirror from 'codemirror';
import {
	connect
} from 'react-redux';
import {
	loadDataType
} from '../actions';
import onClickOutside from 'react-onclickoutside';
const _ = require( 'lodash' );

class ExpressionEditor extends React.Component {
	constructor( props ) {
		super( props );
		this.nodeTags = {};
		this.charMap = {};
		this.nodeTagTypes = {};
		this.state = {
			value: props.value
		};
	}
	replaceExpressionWithSpecialChars( text, type = null ) {
		if ( 'string' !== typeof text ) {
			if ( text.id ) {
				return text.id;
			}
			return '';
		}
		return text.replace( /\{\{_N([0-9]*)\.([^\}]*)\}\}/gi, ( m, nid, pin ) => {

			return this.replaceExpressionNodeTag({
				icon: this.props.getNodeIcon( nid ),
				nodeId: nid,
				pinId: pin,
				nodeLabel: this.props.getNodeLabel( nid ),
				pinLabel: pin,
				fieldType: type
			}, true );
		});
	}
	handleClickOutside() {
		this.setState({
			quickSearch: false,
			showOperations: false
		});
	}
	insertField( node, field, subprop, subType ) {
		let expression = '{{_N' + node.nid + '.' + field.name;
		if ( subprop ) {
			if ( ! _.isArray( subprop ) ) {
				subprop = [ subprop ];
			}

			expression += '.' + _.join( subprop, '.' );

		}
		expression += '}}';
		this.codeMirror.replaceSelection( this.replaceExpressionWithSpecialChars( expression, subType ) );
		this.setState({
			quickSearch: false
		});
	}

	clearValue() {
		this.codeMirror.setValue( '' );
	}

	toggleQuickSearch( e ) {
		e.stopPropagation();


		this.setState({
			quickSearch: ! this.state.quickSearch
		});
	}
	componentWillReceiveProps( props ) {
		let expr = props.value;
		this.setState({
			value: expr
		});
		let cm = this.codeMirror;

		if ( this.codeMirror ) {
			let startCursor = cm.getCursor();
			cm.setValue( this.replaceExpressionWithSpecialChars( expr ) );
			let cursorLine = startCursor.line;
			let cursorCh = startCursor.ch;
			cm.setCursor({
				line: cursorLine,
				ch: cursorCh
			}, null, {
				scroll: false
			});
		}
	}
	getExpression() {
		let val = this.codeMirror.getValue();
		return this.replaceSpecialCharsWithExpression( val, true );
	}
	onCodeMirrorChange( e ) {
		let newValue = this.getExpression();
		if ( newValue != this.props.value ) {
			if ( this.props.onChange ) {
				this.props.onChange( newValue );
			}
		}
	}
	createTagNode( char ) {
		let node = document.createElement( 'span' );
		let label = 'Custom Element';
		let tagId = this.charMap[char];
		let nodeTag = this.nodeTags[tagId];
		let parts = nodeTag.pinLabel.split( '.' );
		let showChevron = true;
		if ( ! nodeTag.nodeId ) {
			showChevron = false;
		}

		React.render( (
			<span data-tag='' className='node-expression-data-tag'>
				{nodeTag.icon && <img src={nodeTag.icon} className="expression-icon" />}
				{nodeTag.nodeId && <span  className="node-expression-data-tag__node">#{nodeTag.nodeId}</span>}
				{parts.map( p=> {
					let text = p;
					let seperator = showChevron && <i className="fa fa-chevron-right"></i> || null;
					showChevron = true;
					let result = text;
					if ( -1 == result.indexOf( '(' ) ) {
						result = result.replace( /([A-Z])/g, ' $1' );

						result = result.replace( /_([a-z])/g, function( r, m ) {
							return ' ' + m.toUpperCase();
						});
					}
					let finalResult = result.charAt( 0 ).toUpperCase() + result.slice( 1 );

					return (
						<span>
							{seperator}
							<span className='node-expression-data-tag__prop'>{finalResult}</span>
						</span>
					);
				})}

				{this.props.schemas[nodeTag.fieldType] && this.props.schemas[nodeTag.fieldType].methods && (
					<span className="settings-button" onClick={( e )=>this.toggleOperations( e, tagId )}><i className="fa fa-cogs"></i></span>
				)}
			</span>
		), node );
		return node;
	}
	componentDidMount() {
		for ( let i in this.props.availableFields ) {
			let field = this.props.availableFields[i];
			for ( let x in field.fields ) {
				this.props.loadDataType( field.fields[x].type );
			}
		}
		let specialCharsRegexp = /[\ue000-\uefff]/g;
		let options = {
			inputStyle: 'contenteditable',
			specialChars: specialCharsRegexp,
			specialCharPlaceholder: this.createTagNode.bind( this ),
			autoScrollCursorOnSet: false,
			extraKeys: {
				Tab: false
			}
		};
		this.codeMirror = CodeMirror.fromTextArea( this.textbox, options );
		let editor = this.codeMirror;
		this.codeMirror.on( 'change', this.onCodeMirrorChange.bind( this ) );
		this.codeMirror.on( 'cursorActivity', function() {
			let options = {
				hint: function() {
					return {
						from: editor.getDoc().getCursor(),
						to: editor.getDoc().getCursor(),
						list: [ 'foo', 'bar' ]
					};
				}
			};
		});
	}
	replaceSpecialCharsWithExpression( val, isString ) {
		return String( val ).replace( /[\ue000-\uefff]/g, function( tag ) {
			if ( ! this.charMap[tag]) {
				return tag;
			} else {
				let char = this.charMap[tag];
				let ntag = this.nodeTags[char];
				return '{{_N' + ntag.nodeId + '.' + ntag.pinId + '}}';
			}
		}.bind( this ) );
	}
	replaceExpressionNodeTag({
		nodeId,
		pinId,
		nodeLabel,
		pinLabel,
		icon,
		fieldType
	}, isString ) {
		let tags = this.nodeTags;
		let tagMaps = this.tagMaps || {};
		let tag = tagMaps[nodeId + '__' + pinId];
		let charMap = this.charMap;
		let tagId = 'UNKNOWN';
		const currentLength = Object.keys( tags ).length;
		if ( ! tag ) {
			tagId = 0xe000 + currentLength;
			if ( ! fieldType && tags[tagId]) {
				fieldType = tags[tagId].fieldType;
			}
			tags[tagId] = {
				id: 0xe000 + currentLength,
				nodeId: nodeId,
				pinId: pinId,
				nodeLabel: nodeLabel,
				pinLabel: pinLabel,
				icon: icon,
				fieldType: fieldType
			};
			charMap[String.fromCharCode( tagId )] = tagId;
			tagMaps[nodeId + '__' + pinId] = tagId;
			this.tagMaps = tagMaps;
		} else {
			tagId = tag;
			if ( ! fieldType && tags[tagId]) {
				fieldType = tags[tagId].fieldType;
			}
			tags[tagId] = {
				id: tag,
				nodeId: nodeId,
				pinId: pinId,
				nodeLabel: nodeLabel,
				pinLabel: pinLabel,
				icon: icon,
				fieldType: fieldType,
				operationValues: tags[tagId].operationValues
			};
			charMap[String.fromCharCode( tagId )] = tagId;
		}
		this.nodeTags = tags;
		this.charMap = charMap;
		return String.fromCharCode( tagId );
	}
	getValue() {
		return this.replaceExpressionWithSpecialChars( this.props.value );
	}
	toggleOperations( e, tagId ) {
		e.stopPropagation();
		this.setState({
			operationTagId: tagId,
			operationX: e.target.offsetLeft,
			operationY: e.target.offsetTop,
			operationFieldType: this.nodeTags[tagId].fieldType,
			showOperations: ! this.state.showOperations
		});
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
	updateExpressionWithOperation( tagId, operationName, operationValues ) {

		let nodeTag = this.nodeTags[tagId];
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

		nodeTag.operationValues = operationValues;
		let expr = parseExpression( '{{_N' + nodeTag.nodeId + '.' + nodeTag.pinId + '}}' );
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
		let matches = dataa.match( /\{\{_N([0-9]*).([^\}]*)\}\}/i );
		let tagMapId = matches[1] + '__' + nodeTag.pinId;
		nodeTag.pinId = matches[2];
		nodeTag.pinLabel = matches[2];
		this.nodeTags[tagId] = nodeTag;


		let tagMaps = this.tagMaps;
		tagMaps[matches[1] + '__' + nodeTag.pinId] = tagId;
		this.tagMaps = tagMaps;
		let newValue = this.getExpression();

		if ( newValue != this.props.value ) {
			if ( this.props.onChange ) {
				this.props.onChange( newValue );
			}
		}
		if ( matches ) {
			this.setState({
				text: this.props.getNodeLabel( matches[1]) + ' - ' + matches[2],
				id: dataa
			});
		}
		this.forceUpdate();
	}
	render() {

		let operations = false;


		if ( this.props.schemas[this.state.operationFieldType] && this.props.schemas[this.state.operationFieldType].methods ) {
			operations = true;
		}

		let allowExpressions = true;
		return (
			<div style={{position: 'relative'}}>
				<div className={'node-control-container ' + ( this.props.errorMessage && 'node-error' || '' )}>	<div ref={( el ) => this.element = el}  className="node-editable-setting">
					<textarea id={'expr_' + this.props.nodeId + '__' + this.props.name} name={'expr_' + this.props.nodeId + '__' + this.props.name} type="text" ref={el=>this.textbox = el} value={this.getValue()}></textarea>
				</div>
				{this.props.value && (
					<a href="javascript:void(0)" className="insert-button" onClick={()=>this.clearValue()}>
						<i className="fa fa-trash"></i>
					</a>
				)}
				{allowExpressions && (
					<a href="javascript:void(0)" className="insert-button" onClick={( e )=>this.toggleQuickSearch( e )}>
						<i className="fa fa-crosshairs"></i>
					</a>
				)}
				{! allowExpressions && (
					<a href="javascript:void(0)" className="insert-button" onClick={( e )=>this.toggleQuickSearch( e )}>
						<i className="fa fa-caret-down"></i>
					</a>
				)}
				</div>
				<QuickSearch className="node-quick-search node-quick-search--editor"
					insertField={this.insertField.bind( this )}
					resetControlTypeClicked={this.props.resetControlTypeClicked}
					show={this.state.quickSearch}
					availableFields={this.props.availableFields.filter( ( f )=>! f.visibleTo )}
					schemas={this.props.schemas}
					getNodeIcon={this.props.getNodeIcon}
					canUseField={( field, node )=>{
						return ! field.visibleTo || 0 <= field.visibleTo.indexOf( this.props.name ) && node.nid == this.props.nodeId;
					}}
				/>
				{this.state.showOperations && (
					<OperationsPanel
						updateSelectedOperation={( op )=>this.updateSelectedOperation( op )}
						selectedOperation={this.state.selectedOperation}
						operationX={this.state.operationX}
						fieldType={this.state.operationFieldType}
						operationY={this.state.operationY}
						operationValues={this.nodeTags[this.state.operationTagId] && this.nodeTags[this.state.operationTagId].operationValues && this.nodeTags[this.state.operationTagId].operationValues}
						setOperationValue={( f, v )=>this.setOperationValue( f, v )}
					/>
				)}

			</div> );
	}
}

const mapStateToProps = ( state, ownProps ) => {
	let schemas = {};
	for ( let type in  state.datatypes ) {
		let fields =  state.datatypes[type];
		schemas[type] = state.datatypes[type] && state.datatypes[type].schema || null;
	}
	return {
		values: state.datatypes && state.datatypes[ownProps.type] && state.datatypes[ownProps.type].choices,
		getNodeLabel: ( nid ) => 0 == nid ? 'Global' : ( state.nodes[nid].name ),
		getNodeIcon: ( nid ) => {
			if ( ! state.nodes[nid] || ! state.definitions[state.nodes[nid].type].plugin || ! state.definitions[state.nodes[nid].type].plugin.icon ) {
				return '';
			}
			return state.definitions[state.nodes[nid].type].plugin.icon;
		},
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
)( onClickOutside( ExpressionEditor, {
	excludeScrollbar: true
}) );
