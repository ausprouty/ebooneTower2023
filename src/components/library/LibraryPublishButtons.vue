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
    ]),
    addNewBook() {
      const newBook = {
        code: '',
        format: '',
        id: '',
        image: {
          image: '',
          title: '',
        },
        pages: '',
        prototype: '',
        publish: '',
        style: '',
        style_set: '',
        template: '',
        title: '',
      }
      this.addBook(newBook)
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
      try {
        // update library file
        console.log ('saveForm')
        var library = this.$store.state.bookmark.library
        this.content.text = JSON.stringify(library)
        var route = this.$route.params
        route.filename = route.library_code
        delete route.folder_name
        this.content.route = JSON.stringify(route)
        this.content.filetype = 'json'
        console.log (this.content)
        var response = await AuthorService.createContentData(this.content)
        if (response.data.error != true && action != 'stay') {
          this.$router.push({
            name: 'previewLibrary',
            params: {
              country_code: this.$route.params.country_code,
              language_iso: this.$route.params.language_iso,
              library_code: this.$route.params.library_code,
            },
          })
        }
        if (response.data.error == true) {
          this.error = true
          this.loaded = false
          this.error_message = response.data.message
        }
      } catch (error) {
        LogService.consoleLogError(
          'LIBRARY EDIT There was an error in Saving Form ',
          error
        )
        this.loaded = false
        this.error_message = error
        this.error = true
      }
    },
  },
}
</script>
<style scoped>
.margin-left {
  margin-left: 10px;
}
</style>
