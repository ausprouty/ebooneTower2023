<template>
  <div>
    <h2>Name and Code</h2>
    <p>#: {{ book.id }}</p>
    <BaseInput
      v-model="libraryBookTitle"
      label="Title:"
      type="text"
      placeholder="Title"
      class="field"
    />
  </div>
</template>
<script>
import Vue from 'vue'
export default {
  props: {
    book: Object,
    index: Number,
  },
  computed: {
    libraryBookTitle: {
      get() {
        var title = this.$store.state.bookmark.library.books[this.index].title
        return title
      },
      set(newValue) {
        console.log(this.index)
        console.log(newValue)
        if (!this.$store.state.bookmark.library.books[this.index]) {
          Vue.set(this.$store.state.bookmark.library.books, this.index, {})
        }
        this.$set(
          this.$store.state.bookmark.library.books[this.index],
          'title',
          newValue
        )
        this.$store.state.bookmark.library.books[this.index].title = newValue
      },
    },
  },
}
</script>
