<template>
  <div>
    <h2>Name and Code</h2>
    <p>Book Number: {{ this.libraryBookNumber }}</p>
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
import { mapGetters, mapState } from 'vuex'
export default {
  props: {
    book: Object,
  },
  computed: {
    ...mapGetters(['getLibraryBook']),
    libraryBookNumber: {
      get() {
        return this.getLibraryBook(this.book.id)
      },
    },
    libraryBookTitle: {
      get() {
        return this.getLibraryBookTitle(this.book.id)
      },
      set(value) {
        this.setLibraryBookTitle(this.book.id, value)
      },
    },
  },
}
</script>
