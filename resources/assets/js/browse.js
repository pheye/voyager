import Vue from 'vue'
import queryString from 'query-string'
import 'vue-awesome/icons/caret-up'
import 'vue-awesome/icons/caret-down'
import Icon from 'vue-awesome/components/Icon'
// import ElementUI from 'element-ui'
// import 'element-ui/lib/theme-chalk/index.css'

Vue.component('fa-icon', Icon)
// Vue.use(ElementUI)

/* eslint-disable no-new */
new Vue({
  el: '#vue-app',
  data: function () {
    const parsed = queryString.parse(window.location.search)
    return {
      field: parsed.order,
      searchKey: '',
      searchValue: ''
    }
  },
  methods: {
    sort: function (field) {
      const parsed = queryString.parse(window.location.search)
      if (this.field === field) {
        parsed.order = `-${field}`
      } else {
        parsed.order = field
      }
      window.location.search = queryString.stringify(parsed)
      console.log('field', field, parsed)
    },
    search () {
      console.log('values', this.searchKey, this.searchValue)
      let parsed = {
        searchKey: this.searchKey,
        searchValue: this.searchValue
      }
      window.location.search = queryString.stringify(parsed)
    }
  }
})
