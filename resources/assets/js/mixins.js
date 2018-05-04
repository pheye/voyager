import queryString from 'query-string'

const mixins = {
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
}
export default mixins
