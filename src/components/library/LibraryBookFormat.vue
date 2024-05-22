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
import { mapMutations, mapState } from 'vuex'
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
      bookFormat: this.$store.state.bookmark.library.books[this.index].format,
      formats: ['page', 'series', 'library'],
    }
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
