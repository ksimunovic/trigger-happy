import update from 'immutability-helper';

const nodes = ( state = {}, action ) => {

	switch ( action.type ) {

		/* Add a new node to the store */
		case 'ADD_NODE':

			let newNode = action.node;

			// Trigger Nodes should always be the first node
			if ( 'trigger' == newNode.nodeType ) {
				newNode.nid = 1;
			}

			let toMerge = {};
			toMerge[ newNode.nid ] = newNode;
			let newState = update( state, { $merge: toMerge });

			return newState;

		/* Set the title of a node */
		case 'SET_NODE_TITLE':

			let nodeId1 = action.nodeId;
			let title = action.nodeTitle;
			let toMergeTitle = { title: title };
			let newStateTitle = update( state, { [ nodeId1 ]: { $merge: toMergeTitle } });
			return newStateTitle;

		/* Set the filters on a node */
		case 'SET_NODE_FILTERS':

			let filterState = state;
			let filteredNodeId = action.nodeId;
			filterState = update( filterState, {
				[ filteredNodeId ]: {
					$merge: {
						filters: action.filters
					}
				}
			});
			return filterState;


		/* delete the specified node */
		case 'REMOVE_NODE':
			let toRemove = [ action.nodeId ];
			return update( state, { $unset: toRemove });

		/* Set the expression for a node field */
		case 'SET_NODE_EXPRESSION':

			let nid = action.nodeId;
			let pin = action.connectorId;
			let expr = action.expr;
			let obj = {};
			let newState2 = state;
			if ( ! newState2[ nid ].expressions ) {
				newState2 = update( state, {
					[ nid ]: {
						$merge: {
							expressions: {}
						}
					}
				});
			}
			newState2 = update( newState2, {
				[ nid ]: {
					expressions: {
						$merge: {
							[ pin ]: expr
						}
					}

				}
			});
			return newState2;


		default:
			return state;
	}
};

export default nodes;
