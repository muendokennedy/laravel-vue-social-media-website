import axios from 'axios'

const instance = axios.create()

instance.interceptors.request.use(function(config){
    // Do something before the request is made
    // TODO
    return config
})

export default instance
