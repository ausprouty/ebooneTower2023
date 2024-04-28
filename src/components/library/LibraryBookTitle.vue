<template>
  <div>
    <h2>Name and Code</h2>
    <p>#: {{ book.id }}</p>
    <BaseInput
      v-model="this.libraryBookTitle"
      label="Title:"
      type="text"
      placeholder="Title"
      class="field"
    />
  </div>
</template>
<script>
import { mapState, state } from 'vuex'
export default {
  props: {
    book: Object,
    index: Number,
  },
  computed: {
    libraryBookTitleX: {
      get() {
        var title = this.$store.state.library.books[this.index].title
        console.log(this.$store.state.library)
        return title
      },
      set(newValue) {
        this.$set(this.state.library.books[this.index], 'title', newValue)
      },
    },
  },
  created() {
    console.log ('next id')
    console.log(this.book)
    console.log(this.index)
    console.log ('was index')
  },
}
</script>
