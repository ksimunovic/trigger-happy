const nodes = ( state = [], action ) => {

	switch ( action.type ) {

		/* Adds the definiton collection to the store */
		case 'SET_DEFINITIONS':

			return action.definitions.reduce( function( acc, cur, i ) {
				acc[ cur.type ] = cur;
				return acc;
			}, {});

		default:
			return state;

	}

};

export default nodes;
