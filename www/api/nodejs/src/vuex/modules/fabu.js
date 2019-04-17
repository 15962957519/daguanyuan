// import { } from '../mutation-types'
let state = {
    localIds: '',
}
const mutations = {
    ['SET_IFRAME_URL'](state, urlObj){
        state.localIds = urlObj.localIds
    }
}

export default {
    state,
    mutations
}