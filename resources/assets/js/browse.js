import Vue from 'vue'
import queryString from 'query-string'
import 'vue-awesome/icons'
import Icon from 'vue-awesome/components/Icon'

Vue.component('fa-icon', Icon)

/* eslint-disable no-new */
new Vue({
  el: '#vue-app',
  data: function () {
    const parsed = queryString.parse(window.location.search)
    return {
      field: parsed.order
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
    }
  }
})
