import React from 'react';
import QuickSearch from './QuickSearch';
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

class OperationsPanel extends React.Component {
	render() {

		let fieldTypeMethods = this.props.schemas[this.props.fieldType] && this.props.schemas[this.props.fieldType].methods;

		let style = {};
		if ( 0 < this.props.operationX || 0 < this.props.operationY ) {
			this.style = {
				left: this.props.operationX,
				top: this.props.operationY + 30
			};
		}
		return ( <div className="triggerhappy-operations" style={style} onClick={( e )=>e.stopPropagation()}>
		<label>Operation Type</label>
		<select onChange={e=>this.props.updateSelectedOperation( e.target.value )}>
		<option value="">None</option>
		{Object.keys( fieldTypeMethods ).map( m=>this.props.selectedOperation == m ? <option selected="selected" value={m}>{fieldTypeMethods[m].description}</option> : <option value={m}>{fieldTypeMethods[m].description}</option> )}
		</select>
		{this.props.selectedOperation && null != this.props.selectedOperation && fieldTypeMethods[this.props.selectedOperation] && (
			<div>
			{Object.keys( fieldTypeMethods[this.props.selectedOperation].fields ).length ? <hr /> : null}
			{Object.keys( fieldTypeMethods[this.props.selectedOperation].fields ).map( f=>( this.renderOperationField( f, fieldTypeMethods[this.props.selectedOperation].fields[f].description ) ) )}
			</div>

		)}

		</div> );
	}

	renderOperationField( f, desc ) {
		return (
			<div>
				<label>{f}</label>
				<input type="text" onChange={( e )=>this.props.setOperationValue( f, e.target.value )} value={this.props.operationValues[f] || ''} />
				<div className="desc">{desc}</div>
			</div>
		);

	}
}
let mapStateToProps = function( state, ownProps ) {
	let schemas = {};
	for ( let type in state.datatypes ) {

		schemas[type] = state.datatypes[type] && state.datatypes[type].schema || null;
	}


	return {

		schemas
	};
};
let mapDispatchToProps = function() {
	return {};
};
export default connect(
	mapStateToProps,
	mapDispatchToProps, null, {
		withRef: true
	}
)( OperationsPanel );
