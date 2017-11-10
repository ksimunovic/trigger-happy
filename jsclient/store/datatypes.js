import update from 'immutability-helper';
const nodes = (state = {}, action) => {

  switch (action.type) {

      case 'LOADED_DATA_TYPE':

        let toMerge = {};
        if (action.datatype != null) {
            toMerge[action.dataTypeId] = action.datatype;
            let newState = update(state, { $merge: toMerge });
            return newState;
        }



    default:
      return state
  }
}

export default nodes;
