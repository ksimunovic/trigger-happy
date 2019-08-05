const globals = ( state = [], action ) => {

	switch ( action.type ) {

		/* Set the available global variables */
		case 'SET_GLOBALS':

			return action.globals.reduce( function( acc, cur, i ) {
				acc[ cur.id ] = cur;
				return acc;
			}, {});

		default:
			return state;

	}
};

export default globals;
