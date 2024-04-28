<template>
  <div>
    <div v-for="(book, id) in libraryBooks" :key="id" :book="book">
      <LibraryBookTitle :book="book" :index="id" />
      <!-- <LibraryBookCode :book="book" :index="id" />
      <LibraryBookImage :book="book" :index="id" />
      <LibraryBookFormat :book="book" :index="id" />
      <LibraryBookStyle :book="book" :index="id" />
      <LibraryBookTemplate :book="book" :index="id" />
      <LibraryBookPermission :book="book" :index="id" />
      -->
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
import LibraryBookCode from '@/components/library/LibraryBookCode'
import LibraryBookFormat from '@/components/library/LibraryBookFormat'
import LibraryBookImage from '@/components/library/LibraryBookImage'
import LibraryBookPermission from '@/components/library/LibraryBookPermission'
import LibraryBookStyle from '@/components/library/LibraryBookStyle'
import LibraryBookTemplate from '@/components/library/LibraryBookTemplate'
import LibraryBookTitle from '@/components/library/LibraryBookTitle'

import { mapGetters, mapMutations } from 'vuex'
import '@/assets/css/vueSelect.css'
export default {
  components: {
    LibraryBookCode,
    LibraryBookFormat,
    LibraryBookImage,
    LibraryBookPermission,
    LibraryBookStyle,
    LibraryBookTemplate,
    LibraryBookTitle,
  },
  computed: {
    ...mapGetters(['getLibraryBooks']),
    libraryBooks: {
      get() {
        var temp = this.getLibraryBooks
        console.log(temp)
        return temp
      },
    },
  },
  methods: {
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
