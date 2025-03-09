import App from './App'

// #ifndef VUE3
import Vue from 'vue'
import './uni.promisify.adaptor'
import Footer from '@/components/footer/footer.vue';
import Axios from '@/util/axios.js';
import Apis from '@/util/api.js';
Vue.config.productionTip = false
App.mpType = 'app'
import uView from '@/uni_modules/uview-ui'
Vue.use(uView)
Vue.component('Footer', Footer)
Vue.prototype.$http = Axios;
Vue.prototype.$api = Apis;
const app = new Vue({
  ...App
})
app.$mount()
// #endif

// #ifdef VUE3
import { createSSRApp } from 'vue'
export function createApp() {
  const app = createSSRApp(App)
  return {
    app
  }
}
// #endif