import Vue from 'vue'
import 'vue-awesome/icons/caret-up'
import 'vue-awesome/icons/caret-down'
import Icon from 'vue-awesome/components/Icon'
import mixins from './mixins'

Vue.component('fa-icon', Icon)
// Vue.use(ElementUI)

/* eslint-disable no-new */
new Vue({
  el: '#vue-app',
  mixins: [
    mixins
  ]
})
