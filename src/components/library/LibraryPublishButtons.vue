<template>
  <div>
    <div>
      <button class="button" @click="prototypeAll">
        Select ALL to prototype
      </button>
      <button class="button margin-left" @click="publishAll">
        Select ALL to publish
      </button>

      <button class="button" @click="prototypeNone">
        Do NOT prototype ANY
      </button>
      <button class="button margin-left" @click="publishNone">
        Do NOT publish ANY
      </button>
    </div>

    <button class="button" @click="addNewBook">New Book</button>
    <div>
      <button class="button red" @click="saveForm">Save Changes</button>
    </div>
  </div>
</template>
<script>
import { mapMutations } from 'vuex'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
export default {
  methods: {
    ...mapMutations([
      'setAllBooksPrototypeToFalse',
      'setAllBooksPrototypeToTrue',
      'setAllBooksPublishToFalse',
      'setAllBooksPublishToTrue',
      'addBook',
    ]),
    addNewBook() {
      const newBook = {
        code: 'unknown',
        format: 'series',
        hide: false,
        id: '998',
        image: {
          image: '',
          title: '',
        },
        password: '',
        prototype: 'N',
        publish: 'N',
        style: '',
        styles_set: '',
        title: 'New Book', // Provide a default title for clarity
        template: '',
      }

      console.log('addNewBook', newBook)
      this.addBook(newBook) // Call the mutation to add the book to the store
      console.log('I am emitting bookAdded')
      this.$emit('bookAdded')
    },

    prototypeAll() {
      this.setAllBooksPrototypeToTrue()
    },
    prototypeNone() {
      this.setAllBooksPrototypeToFalse()
    },
    publishAll() {
      this.setAllBooksPublishToTrue()
    },
    publishNone() {
      this.setAllBooksPublishToFalse()
    },
    async saveForm(action = null) {
      if (this.saving) return // Prevent re-trigger
      this.saving = true // Disable button

      try {
        console.log('saveForm')

        // Prepare content data
        const library = this.$store.state.bookmark.library
        const { country_code, language_iso, library_code } = this.$route.params
        const route = { ...this.$route.params, filename: library_code }
        delete route.folder_name

        this.content = {
          text: JSON.stringify(library),
          route: JSON.stringify(route),
          filetype: 'json',
        }

        console.log(this.content)

        // Send content to AuthorService
        const response = await AuthorService.createContentData(this.content)
        console.log(response)

        if (response.data.error) {
          this.handleError(response.data.message)
        } else if (action !== 'stay') {
          this.navigateToPreview({ country_code, language_iso, library_code })
        }
      } catch (error) {
        this.handleException(error)
      } finally {
        this.saving = false // Re-enable button
      }
    },
    navigateToPreview(params) {
      this.$router.push({ name: 'previewLibrary', params })
    },

    handleError(message) {
      this.error = true
      this.loaded = false
      this.error_message = message
    },

    handleException(error) {
      LogService.consoleLogError(
        'LIBRARY EDIT There was an error in Saving Form',
        error
      )
      this.error_message = error
      this.error = true
      this.loaded = false
    },
  },
}
</script>
<style scoped>
.margin-left {
  margin-left: 10px;
}
</style>
