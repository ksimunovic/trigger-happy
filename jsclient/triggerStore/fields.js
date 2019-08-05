import update from 'immutability-helper';

const fields = ( state = {}, action ) => {

	switch ( action.type ) {

		/* Add a new node to the store */
		case 'ADD_FIELD':

			let newState = state;
			newState = update( newState, { '$push': [ { dir: action.fieldType } ] });

			return newState;
		default:
			return state;
	}
};

export default fields;
