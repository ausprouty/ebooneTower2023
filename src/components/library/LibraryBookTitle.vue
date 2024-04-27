<template>
  <div>
    <h2>Name and Code</h2>
    <p>Book Number: {{ this.book.index }}</p>
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
import { mapState } from 'vuex'
export default {
  props: {
    book: Object,
  },
  computed: {
    ...mapState({
      library: (state) => state.bookmark.library,
    }),
    libraryBookTitle() {
      return this.library.book[this.book.id].title
    },
  },
  created() {
    console.log(this.book.id)
  },
}
</script>
