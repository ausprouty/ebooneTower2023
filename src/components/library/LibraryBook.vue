<template>
  <div>
    <div v-for="(book, id) in $v.books.$each.$iter" :key="id" :book="book">
      <div
        class="app-card -shadow"
        v-bind:class="{ notpublished: !book.publish.$model }"
      >
        <div
          class="float-right"
          style="cursor: pointer"
          @click="deleteBookForm(id)"
        >
          X
        </div>

          

  </div>
</template>
<script>
import LogService from '@/services/LogService.js'
export default {
  methods: {
    addNewBookTitle(title) {
      LogService.consoleLogMessage('I came to addNewBookTitle')
      LogService.consoleLogMessage(title)
      this.bookcodes = []
      var change = this.$v.books.$model
      LogService.consoleLogMessage('change')
      LogService.consoleLogMessage(change)
      var arrayLength = change.length
      for (var i = 0; i < arrayLength; i++) {
        this.bookcodes.push(this.$v.books.$model[i].code)
      }
      LogService.consoleLogMessage(this.bookcodes)
      LogService.consoleLogMessage('about to hide')
      this.isHidden = true
      LogService.consoleLogMessage('hidden')
    },
    async createFolder(folder) {
      LogService.consoleLogMessage(folder)
      var params = {}
      params.route = this.$route.params
      params.route.folder_name = folder.toLowerCase()
      params.route = JSON.stringify(params.route)
      AuthorService.createContentFolder(params)
      this.folders = await AuthorService.getFoldersContent(params)
    },

    async createTemplate(
      template,
      css,
      styles_set,
      title,
      book_code,
      book_format
    ) {
      await this.saveForm('stay')
      // creating a new template
      if (typeof template == 'undefined') {
        template = 'new'
      }
      if (typeof styles_set == 'undefined') {
        styles_set = 'default'
      }
      // use default style if not set
      if (typeof css == 'undefined') {
        css = this.style
      }
      LogService.consoleLogMessage(template)
      LogService.consoleLogMessage(css)
      this.$router.push({
        name: 'createTemplate',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
          title: title,
          template: template,
          cssFORMATTED: css,
          styles_set: styles_set,
          book_code: book_code,
          book_format: book_format,
        },
      })
    },
    deleteBookForm(id) {
      LogService.consoleLogMessage('Deleting id ' + id)
      this.books.splice(id, 1)
      LogService.consoleLogMessage(this.books)
    },
  },
}
</script>
