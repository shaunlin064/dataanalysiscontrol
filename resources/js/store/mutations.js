// const mutations = {
//     changeLoadingStatus(state, payload) {
//         state.loading = payload;
//     },
// }
//
// export default mutations;


import * as types from './types';

export default {
    [types.MUTATE_LOADING_STATUS] : (state, payload) => {
        state.loading = payload;
    }
}
