import Vue from 'vue'

/* eslint-disable no-new */
new Vue({
  el: '#vue-app',
  data: {
    message: 'Hello Vue!'
  },
  methods: {
    sort: function (field) {
      console.log('field', field)
    }
  }
})
