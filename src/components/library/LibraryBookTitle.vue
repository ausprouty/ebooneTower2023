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
      set(value) {
        console.log(this.index)
        console.log(value)
        if (!this.$store.state.bookmark.library.books[this.index]) {
          Vue.set(this.$store.state.bookmark.library.books, this.index, {})
        }
        //this.$store.state.bookmark.library.books[this.index].title = value
        //this.setBookTitle(this.index, value)
        var index = this.index
        this.$store.commit('setBookTitle', { index, value })
      },
    },
  },
}
</script>
