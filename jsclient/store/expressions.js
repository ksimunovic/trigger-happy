import update from 'immutability-helper';
const nodes = ( state = {}, action ) => {

	switch ( action.type ) {

		/* Sets the expression for the specified node field */
		case 'SET_NODE_EXPRESSION':

			let nid = action.nodeId;
			let pin = action.connectorId;
			let expr = action.expr;
			let obj = {};
			obj[nid + '.' + pin] = expr;
			return Object.assign({}, state, obj );
		case 'REMOVE_NODE':
			let removedNode = action.nodeId
			let newState = {};
			for (let s in state) {
				if (s.indexOf(removedNode + ".") === 0)
					continue;
				newState[s] = state[s];
			}
			return newState;
		default:
			return state;

	}

};

export default nodes;
