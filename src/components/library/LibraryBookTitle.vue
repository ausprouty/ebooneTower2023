<template>
  <div>
    <h2>Name and Code</h2>
    <p>#: {{ book.id }}</p>
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
    book: Object,
    index: Number,
  },
  methods: {
    setBookTitle(index, value) {
      //this.$store.state.bookmark.library.books[index].title = value
      this.$store.commit('setBookTitle', { index, value })
    },
  },
  computed: {
    libraryBookTitle() {
      return this.$store.state.bookmark.library.books[this.index].title
    },
  },
}
</script>
