<template>
  <div>
    <hr><hr>
    <h2>Book Name and Code</h2>
    <p>#: {{ index }}</p>
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
      return this.$store.state.bookmark.library.books[this.index].title
    },
  },
}
</script>
