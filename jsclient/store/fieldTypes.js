import update from 'immutability-helper';
const nodes = ( state = {}, action ) => {

	switch ( action.type ) {

		/* Sets the field type of the specified Node Field */
		case 'SET_FIELD_TYPE':

			let nid = action.nodeId;
			let pin = action.fieldName;
			let expr = action.fieldType;
			let obj = {};
			obj[nid + '.' + pin] = expr;
			return Object.assign({}, state, obj );

		default:
			return state;
	}

};

export default nodes;
