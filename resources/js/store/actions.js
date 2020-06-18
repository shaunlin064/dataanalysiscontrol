// const actions = {
//     changeLoadingStatus({commit}, payload) {
//         commit('changeLoadingStatus', payload)
//     },
// }
//
// export default actions;

import * as types from './types';

export default {
    [types.CHANGE_LOADING_STATUS] : ({commit}, payload) => {
        commit(types.MUTATE_LOADING_STATUS, payload);
    }
}
