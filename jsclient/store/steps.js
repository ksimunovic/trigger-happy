import update from 'immutability-helper';
const ui = (state = {}, action) => {
  switch (action.type) {
      case 'ADD_STEP':
        let nodeId = action.nodeId;
        let stepDef = {
        };
        stepDef[nodeId] = state[nodeId] || [];
        stepDef[nodeId].push(action.step);
        let newState = update(state, { $merge: stepDef }) ;
        return newState;
    case 'CLEAR_STEPS':
          let clearstepDef = {
          };
          clearstepDef[action.nodeId] =  [];

          return update(state, { $merge: clearstepDef }) ;

    default:
      return state
  }
}

export default ui;
