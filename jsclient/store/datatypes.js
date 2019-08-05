import update from 'immutability-helper';

const nodes = ( state = {}, action ) => {

	switch ( action.type ) {

		/* Adds a data type to the store */
		case 'LOADED_DATA_TYPE':
			let toMerge = {};
			if ( null != action.datatype ) {
				toMerge[ action.dataTypeId ] = action.datatype;
				let newState = update( state, { $merge: toMerge });
				return newState;
			}

		default:
			return state;
	}

};

export default nodes;
