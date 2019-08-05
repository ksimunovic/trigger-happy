import React from 'react';
export default class QuickSearch extends React.Component {

	constructor( props ) {
		super( props );
		this.state = {};
	}

	getTypeIcon( node ) {

		let pluginIcon = this.props.getNodeIcon( node.nid );


		if ( pluginIcon ) {
			return ( <div className={'node-icon node-icon--' + node.cat}>
				<img src="pluginIcon" />
				</div> );
		}
		if ( ! pluginIcon && 'trigger' == node.nodeType ) {

			return ( <div className={'node-icon node-icon--' + node.nodeType}>
				<i className="fa fa-bolt"></i>
				</div> );
		}
		if ( ! pluginIcon && 'action' == node.nodeType ) {
			return ( <div className={'node-icon node-icon--' + node.nodeType}>
					<i className="fa fa-tasks"></i>
					</div> );
		}

	}
	canUseField( f, n ) {
		if ( this.props.canUseField ) {
			return this.props.canUseField( f, n );
		}
		return true;
	}
	render() {

		let fields = this.props.availableFields.filter( ( n, i ) => {
			let innerFields = ( n.fields || []).filter( f => ! this.state.quickSearchText || 0 <= ( f.label || f.name ).indexOf( this.state.quickSearchText ) || this.filterSchema( f.type ) );
			innerFields = innerFields.filter( f => this.canUseField( f, n ) );
			return 0 < innerFields.length;
		});
		let getLabel = ( n ) => {
			if ( n.nid ) {
				return n.nid + '. ' + ( n.label || n.name2 );
			}
			return 'Global';
		};
		return ( <div className={this.props.className} style={{display: this.props.show ? 'block' : 'none'}}>

			<div className="node-quick-search__search">
				<input type="text" className="node-quick-search__search-input" placeholder="search..." value={this.state.quickSearchText} onChange={( e )=>this.setState({quickSearchText: e.target.value })}/>
			</div>

		<div className="quick-search-container">
		{ 0 == fields.length && ( <div><div className="node-quick-search__group">No fields available</div></div> ) }
		{fields.map( ( n, i )=> {
			return ( <div><div className="node-quick-search__group">
			{this.getTypeIcon( n )}
			{getLabel( n )}
			</div>{(
				<ul>{( n.fields || []).filter( f=>! this.state.quickSearchText || 0 <= ( f.label || f.name ).indexOf( this.state.quickSearchText ) || this.filterSchema( f.type ) ).filter( f=>this.canUseField( f, n ) ).map( f=> {
					let schemaList = this.renderSchema( n, f, f.type );

					return (
				<li className="node-quick-search__item">
				<a href="javascript:void(0);" onClick={( e )=>this.props.insertField( n, f,null, f.type )}>
				<strong>{f.label || f.name}</strong>
				{schemaList && <i onClick={( e )=>this.toggleList( n, f, e )} className={ this.canShowList( n, f ) ? 'fa fa-chevron-up' : 'fa fa-chevron-down'}></i>}
				</a>
				{this.renderSchemaDesc( f.type )}
				{this.canShowList( n, f ) && schemaList}
				</li>
			);
			})}</ul> )}</div> );
		})}
		</div>
		{this.props.controlTypeOverrideText &&
		<div><hr/><ul><li>
		<a href="javascript:void(0);" onClick={()=>this.props.resetControlTypeClicked()}> {this.props.controlTypeOverrideText} </a>
		</li></ul></div>
		}
		</div> );
	}
	canShowList( n, f, type ) {
		return this.state && this.state.showSchema && this.state.showSchema[n.nid + '-' + f.name];
	}
	toggleList( n, f, e ) {

		e.stopPropagation();
		let showSchemaList = this.state.showSchema || {};
		showSchemaList[n.nid + '-' + f.name] = ! showSchemaList[n.nid + '-' + f.name];
		this.setState({
			showSchema: showSchemaList
		});
	}
	filterSchema( type ) {
		if ( type && this.props.schemas && this.props.schemas[type]) {
			return 0 < Object.keys( this.props.schemas[type].properties ).filter( f => ! this.state.quickSearchText || 0 <= f.indexOf( this.state.quickSearchText ) || 0 <= this.props.schemas[type].properties[f].description.indexOf( this.state.quickSearchText ) ).length;
		}
		return false;
	}
	renderSchemaDesc( type ) {
		if ( type && this.props.schemas && this.props.schemas[type]) {
			return <em className="quick-search__desc">{this.props.schemas[type].description}</em>;
		}
		return null;
	}

	renderSchema( n, f, type, prefix = '' ) {

		if ( type && this.props.schemas && this.props.schemas[type]) {

			return ( <ul className="quick-search-sub">
			{this.props.schemas[type].properties && Object.keys( this.props.schemas[type].properties ).filter( f=>! this.state.quickSearchText || 0 <= f.indexOf( this.state.quickSearchText ) || 0 <= this.props.schemas[type].properties[f].description.indexOf( this.state.quickSearchText ) ).map( ( k )=>(
					<li className="node-quick-search__item" data-type={this.props.schemas[type].properties[k].type}>
					<a href="javascript:void(0);" onClick={()=>this.props.insertField( n, f, prefix + k, this.props.schemas[type].properties[k].type )} data-type={this.props.schemas[type].properties[k].type}>
					<strong className="quick-search__name">{k}</strong><em className="quick-search__desc">{this.props.schemas[type].properties[k].description}</em>
					</a>
						{this.renderSchema( n, f, this.props.schemas[type].properties[k].type, k + '.' )}
					</li>
			) )}

			</ul> );
		}
		return null;
	}
}
