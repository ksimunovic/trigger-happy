import React from 'react';
import { addTriggerField, loadAllDataTypes } from '../actions';
import {
	connect
} from 'react-redux';
import NodeFilters from './NodeFilters';

class TriggerFields extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			filterGroups: props.filters || [
				[ '' ]
			]
		};
	}
	componentDidMount() {
		this.props.loadAllDataTypes();

	}
	render() {
		return (
			<div>
				<h4 className="trigger-detail-heading">Trigger Details:</h4>
				<div className="trigger-detail-field">
					<label className="trigger-detail-field__label">Hook Name</label>
					<input className="trigger-detail-field__input" type="text" />

				</div>
				<em className="trigger-detail-help">
					The action or trigger name (eg: wp_insert_post)
				</em>
				<div className="trigger-detail-field">
					<label className="trigger-detail-field__label">Hook Type</label>
					<select className="trigger-detail-field__input">
						<option>Filter</option>
						<option>Action</option>
					</select>
				</div>
				<em className="trigger-detail-help">
					The type of trigger (action or filter)
				</em>
				<div className="trigger-detail-field">
					<label className="trigger-detail-field__label">Description</label>
					<textarea className="trigger-detail-field__input"></textarea>
				</div>
				<em className="trigger-detail-help">
					Description of the trigger. This will be displayed in the Flow editor.
				</em>
				<h4 className="trigger-detail-heading">Input Fields:</h4>
				<p>Add fields that are passed into this hook. For example, the <a href="https://codex.wordpress.org/Plugin_API/Action_Reference/wp_insert_post">wp_insert_post</a> hook has three parameters: Post ID, Post and Update </p>
				<div className="trigger-fields-container">
					{this.props.fields.map( ( group, i ) => (
						<div className="trigger-field-group">
							<div className="trigger-field-control">
								<label className="trigger-field-control__label">Field Name</label>
								<input className="trigger-field-control__input" type="text" />
							</div>
							<div className="trigger-field-control">
								<label className="trigger-field-control__label">Label</label>
								<input className="trigger-field-control__input" type="text" />
							</div>
							<div className="trigger-field-control">
								<label className="trigger-field-control__label">Field Type</label>
								<select className="trigger-field-control__input">
								{Object.keys( this.props.dataTypes ).map( ( dataType, i ) => (
									<option value={dataType}>{dataType}</option>
								) )}
								</select>
							</div>

						</div>
					) )}
					<a className="button" onClick={()=>this.addField( 'start' )}>Add New Input Field</a>
				</div>
				<h4 className="trigger-detail-heading">Filters:</h4>
				<div className="trigger-fields-filters">

				</div>
			</div>
		);
	}
	addField( dir ) {
		this.props.addField( dir );
	}
}
const mapStateToProps = ( state, ownProps ) => {
	return {
		fields: state.fields,
		dataTypes: state.datatypes
	};
};

const mapDispatchToProps = dispatch => {
	return {
		loadAllDataTypes: () => dispatch( loadAllDataTypes() ),
		addField: ( ) => {
			dispatch( addTriggerField( ) );
		}
	};
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)( TriggerFields );
