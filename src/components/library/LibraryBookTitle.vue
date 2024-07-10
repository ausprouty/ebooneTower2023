<template>
  <div>
    <hr />
    <hr />
    <p>Book #: {{ index }}</p>
    <BaseInput
      :value="libraryBookTitle"
      label="Title:"
      type="text"
      placeholder="Title"
      class="field"
      @change="setBookTitle(index, $event.target.value)"
    />
  </div>
</template>
<script>
export default {
  props: {
    index: Number,
  },
  methods: {
    setBookTitle(index, value) {
      this.$store.commit('setBookTitle', { index, value })
    },
  },
  computed: {
    libraryBookTitle() {
      const books = this.$store.state.bookmark.library.books
      if (books && books[this.index] && books[this.index].title) {
        return books[this.index].title
      }
      return ''
    },
  },
}
</script>
