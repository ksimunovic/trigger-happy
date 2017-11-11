import update from 'immutability-helper';

const ui = ( state = [], action ) => {

	switch ( action.type ) {
		case 'EDIT_NODE':

			return Object.assign({}, state, {
				nextButtonText: 'Save and Continue',
				editType: action.editType,
				showPanel: '',
				selectedNodeId: action.node.nid
			});

		case 'TEST_NODE':
			return Object.assign({}, state, {
				nextButtonText: 'Fetch and Continue',
				showPanel: 'TestNode',
				selectedNodeId: action.node.nid
			});

		case 'SHOW_CREATE_NEW':
			return Object.assign({}, state, {
				addTrigger: false,
				selectedNodeId: null,
				nextButtonText: null,
				showPanel: 'CreateNew',
				'selectedPlugin': null
			});

		case 'ADD_TRIGGER':
			let prevStep = null;
			if ( state.panelType ) {
				prevStep = {
					page: state.panelType,
					options: state.panelOptions
				};
			}
			return Object.assign({}, state, {
				addTrigger: true,
				panelType: 'CreateNew',
				panelOptions: null,
				prevStep: prevStep
			});

		case 'SHOW_SELECT_ACTION':
			return Object.assign({}, state, {
				selectedNodeId: null,
				nextButtonText: null,
				showPanel: 'SelectAction',
				'selectedPlugin': action.plugin
			});

		case 'RESET_UI':
			return Object.assign({}, state, {
				showPanel: '',
				nextButtonText: null,
				'selectedPlugin': null
			});

		case 'LOADING_DATA_TYPE':
			return Object.assign({}, state, {
				loadingDataTypes: true
			});

		case 'LOADED_DATA_TYPE':
			return Object.assign({}, state, {
				loadingDataTypes: false
			});

		case 'NAVIGATE':
			return Object.assign({}, state, {
				addTrigger: false,
				panelType: action.page,
				panelOptions: action.options
			});

		case 'SET_ERROR_MESSAGE':
			let newState = state;
			if ( ! newState.errors ) {
					newState = update( newState, { $merge: { errors: {} } });
			}
			if ( ! newState.errors[ action.nodeId ]) {
				let errorMerge = {};
				errorMerge[ action.nodeId ] =  {} ;
				newState = update( newState, { errors: { $merge: errorMerge  } });
			}
			let toMerge = {};
			toMerge[ action.fieldName ] = action.message;
			newState = update( newState, { errors: { [ action.nodeId ]: { $merge: toMerge } } });
			return newState;

		default:
			return state;
  }
};

export default ui;
