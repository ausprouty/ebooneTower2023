<template>
  <div>
    <h2>{{ language.language_name }}</h2>

    <CapacitorLanguage v-bind:language="language" />

    <div v-for="(book, id) in books" :key="id" :book="book">
      <div>
        <h3>{{ book.title }} ({{ book.library_code }})</h3>
      </div>
      <div><CapacitorActions v-bind:book="book" /></div>
    </div>
  </div>
</template>

<script>
import CapacitorService from '@/services/CapacitorService.js'
import CapacitorLanguage from '@/components/CapacitorLanguage.vue'
import CapacitorActions from '@/components/CapacitorActions.vue'
export default {
  props: {
    language: Object,
  },
  components: {
    CapacitorActions,
    CapacitorLanguage,
  },
  data() {
    return {
      books: [],
    }
  },
  methods: {},
  async created() {
    console.log (this.language)
    this.books = []
    var params = {}
    params.language = this.language
    this.books = await CapacitorService.getBooks(params)
    console.log(this.books)
  },
}
</script>
