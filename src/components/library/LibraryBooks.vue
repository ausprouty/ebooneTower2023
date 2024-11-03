<template>
  <div :key="renderKey">
    <div
      v-for="(book, id) in this.$store.state.bookmark.library.books"
      :key="id"
      :book="book"
      :class="{
        'light-background': isOdd(id),
        'dark-background': !isOdd(id),
      }"
    >
      <LibraryBookTitle :index="id" />
      <LibraryBookCode :index="id" />
      <LibraryBookImage :index="id" />
      <LibraryBookFormat :index="id" />
      <LibraryBookStyle :index="id" />
      <LibraryBookTemplate :index="id" />
      <LibraryBookPermission :index="id" />

      <div
        class="app-card -shadow"
        v-bind:class="{ notpublished: !book.publish }"
      >
        <div
          class="float-right"
          style="cursor: pointer"
          @click="deleteBookForm(id)"
        >
          X Delete
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import LibraryBookCode from '@/components/library/LibraryBookCode'
import LibraryBookFormat from '@/components/library/LibraryBookFormat'
import LibraryBookImage from '@/components/library/LibraryBookImage'
import LibraryBookPermission from '@/components/library/LibraryBookPermission'
import LibraryBookStyle from '@/components/library/LibraryBookStyle'
import LibraryBookTemplate from '@/components/library/LibraryBookTemplate'
import LibraryBookTitle from '@/components/library/LibraryBookTitle'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { mapState, mapMutations } from 'vuex'
import '@/assets/css/vueSelect.css'
export default {
  mixins: [libraryGetMixin],
  components: {
    LibraryBookCode,
    LibraryBookFormat,
    LibraryBookImage,
    LibraryBookPermission,
    LibraryBookStyle,
    LibraryBookTemplate,
    LibraryBookTitle,
  },
  data() {
    return {
      renderKey: 0, // Initialize renderKey
    }
  },
  computed: {
    ...mapState({
      libraryBooks: (state) => state.bookmark.library.books,
    }),
  },

  created() {
    console.log('about to store in state')
    this.storeBookImagesInState()
    this.storeBookStyleSheetsInState()
    this.storeBookTemplatesInState()
    this.storeCkEditorStyleSetsInState()
    console.log('finished store in state')
  },
  methods: {
    ...mapMutations([
      'removeBook',
      'setBookImages',
      'setBookStyleSheets',
      'setBookTemplates',
      'setCkEditorStyleSets',
    ]),
    refreshBooks() {
      console.log('But State is:', this.$store.state.bookmark.library.books)
      console.log('Refreshine LibraryBooks:', this.libraryBooks)
      this.renderKey += 1
      //this.renderKey += 1 // Change the renderKey value to force re-render
    },
    isOdd(id) {
      return id % 2 === 0 // Even index (since arrays are 0-based, this means odd items in 1-based logic)
    },
    async storeBookImagesInState() {
      var directory = this.$store.state.bookmark.language.image_dir
      console.log(directory)
      var images = await this.getImages('content', directory)
      this.setBookImages(images)
    },
    async storeBookStyleSheetsInState() {
      var params = []
      params['country_code'] = this.$route.params.country_code
      params['language_iso'] = this.$route.params.language_iso
      var styles = await this.getStyles(params)
      this.setBookStyleSheets(styles)
    },
    async storeBookTemplatesInState() {
      var params = []
      params['country_code'] = this.$route.params.country_code
      params['language_iso'] = this.$route.params.language_iso
      var templates = await this.getTemplates(params)
      this.setBookTemplates(templates)
    },
    async storeCkEditorStyleSetsInState() {
      var params = []
      var styles = await this.getCkEditorStyleSets(params)
      console.log('styles in storeCkEditorStyleSetsInState')
      console.log(styles)
      this.setCkEditorStyleSets(styles)
    },
    async deleteBookForm(id) {
      try {
        // Remove the book from the store
        this.removeBook(id)

        // Update library file
        var libraryObject = this.$store.state.bookmark.library
        var content = {
          text: JSON.stringify(libraryObject),
          route: JSON.stringify({
            ...this.$route.params,
            filename: this.$route.params.library_code,
          }),
          filetype: 'json',
        }

        await AuthorService.createContentData(content)

        // Force a re-render by updating renderKey
        this.renderKey += 1
      } catch (error) {
        console.log('Error in deleteBookForm', error)
        LogService.consoleLogError(
          'LIBRARY EDIT There was an error in Saving Form ',
          error
        )
      }
    },
  },
}
</script>
<style scoped>
.light-background {
  background-color: palegoldenrod;
}
.dark-background {
  background-color: #d0eaff;
}
</style>
