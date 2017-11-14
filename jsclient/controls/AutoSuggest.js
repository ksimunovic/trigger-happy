import React from 'react';
import {
	connect
} from 'react-redux';
import {
	loadDataType
} from '../actions';
import _ from 'lodash';
class AutoSuggest extends React.Component {
	constructor( props ) {
		super( props );
		this.state = {
			text: props.value && props.value.text,
			id: props.value && props.value.id
		};
	}
	componentWillReceiveProps( props ) {
		if ( props.value ) {
			this.setState({
				text: props.value.text,
				id: props.value.id
			});
		}
	}
	toggleSearch( value ) {

		this.setState({
			quickSearchText: value
		});
		this.handleSearchDebounced();
	}
	componentWillMount() {
		this.handleSearchDebounced = _.debounce( () => {
			this.handleSearch.apply( this, [ this.state.quickSearchText ]);
		}, 500 );
	}
	handleSearch( query ) {
		this.setState({
			result: query
		});
		fetch( TH.rest_api_url + 'wpflow/v1/types/' + this.props.type + '/values/' + query + '?_wpnonce=' + document.getElementById( 'triggerhappy-x-nonce' ).value, {
			credentials: 'same-origin'
		}).then( response => {
			if ( 200 != response.status ) {
				return [];
			}
			return response.json();
		}).then( data => this.setState({
			quickSearch: true,
			choices: data
		}) );
	}

	componentDidMount() {

		this.props.loadDataType( this.props.type );
	}
	insertField( data ) {

		this.setState({
			text: data.text,
			id: data.id
		});
		this.props.onChange && this.props.onChange( data );
	}

	clearValue() {
		this.setState({
			text: null,
			id: null
		});
		this.props.onChange && this.props.onChange( null );
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
	render() {
		let allowExpressions = false;
		let choices = this.state.choices || this.props.dataTypeChoices;
		return (
			<div>
				<div className="node-control-container">
					<div ref={( el ) => this.element = el} className="node-editable-setting">
						<input placeholder="Type to search..." type="text" value={this.state.text} className="node-editable-text" onBlur={( v )=>this.ensureSelected()} onChange={( v )=>this.toggleSearch( v.target.value )} />
					</div>
					{this.props.value && ! this.props.notNull && (
						<a href="javascript:void(0)" className="insert-button" onClick={()=>this.clearValue()}>
							<i className="fa fa-trash"></i>
						</a>
					)}
					{allowExpressions && <a href="javascript:void(0)" className="insert-button" onClick={()=>this.toggleQuickSearch()}>
						<i className="fa fa-crosshairs"></i>
					</a>}

				</div>
				<div className="node-quick-search" style={{display: this.state.quickSearch ? 'block' : 'none'}}>
					{this.props.dataTypeChoicesLoading && ( <ul><li>Loading Choices</li></ul> )}
					{choices && (
						<div className="quick-search-container">
							<ul>
								{choices.filter( n=>null == this.state.quickSearchFilter || 0 <= n.text.indexOf( this.state.quickSearchFilter ) ).map( ( n )=>(
									<li className="node-quick-search__item">
										<a href="javascript:void(0);" onClick={()=>this.selectChoice( n )}>{this.props.showID && '#' + n.id + ' '}{n.text}</a>
									</li>
								) )}
							</ul>
						</div>
					)}
					{this.props.allowCustomValue && (
						<div>
							<hr/>
							<ul>
								<li>
									<a href="javascript:void(0);" onClick={()=>this.props.customValueControlTypeClicked()}> Use custom value </a>
								</li>
							</ul>
						</div>
					)}
				</div>
			</div>
		);
	}
}

const mapStateToProps = ( state, ownProps ) => {
	let dataTypeChoices = false;
	if ( ! ownProps.dataTypeChoices && state.datatypes[ownProps.type] && state.datatypes[ownProps.type].choices ) {
		dataTypeChoices = state.datatypes[ownProps.type].choices;
	}
	return {
		getNodeLabel: ( nid ) => ( state.nodes[nid].name ),
		dataTypeChoices: ownProps.dataTypeChoices || dataTypeChoices
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
)( AutoSuggest );
