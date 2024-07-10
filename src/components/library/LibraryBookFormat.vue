<template>
  <div>
    <h3>Format and Styling:</h3>
    <v-select
      label="Format:"
      :options="formats"
      v-model="bookFormat"
      class="field"
      @change="updateFormat(bookFormat)"
    />
  </div>
</template>
<script>
import vSelect from 'vue-select'
export default {
  props: {
    index: {
      type: Number,
      required: true,
    },
  },
  components: {
    'v-select': vSelect,
  },
  data() {
    return {
      formats: ['page', 'series', 'library'],
    }
  },
  computed: {
    bookFormat() {
      const books = this.$store.state.bookmark.library.books
      if (books && books[this.index] && books[this.index].format) {
        return books[this.index].format
      }
      return ''
    },
  },  
  methods: {
    updateFormat(bookFormat) {
      this.$store.commit('setBookFormat', {
        index: this.index,
        formatType: bookFormat,
      })
    },
  },
}
</script>
