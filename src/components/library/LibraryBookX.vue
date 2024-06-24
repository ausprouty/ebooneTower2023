<template>
  <div>
    <div
      v-for="(book, id) in $v.books.$each.$iter"
      :key="id"
      :book="book"
      :class="{
        'light-background': isOdd(index),
        'dark-background': !isOdd(index),
      }"
    >
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
    </div>
  </div>
</template>
<script>
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
export default {
  methods: {
    isOdd(index) {
      console.log (index)
      return index % 2 === 0 // Even index (since arrays are 0-based, this means odd items in 1-based logic)
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
<style scoped>
.light-background {
  background-color: #f5f5f5;
}
.dark-background {
  background-color: #d0eaff;
}
</style>
