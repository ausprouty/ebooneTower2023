<template>
  <div>
    <div
      v-for="(book, id) in libraryBooks"
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
import LogService from '@/services/LogService.js'
import LibraryBookCode from '@/components/library/LibraryBookCode'
import LibraryBookFormat from '@/components/library/LibraryBookFormat'
import LibraryBookImage from '@/components/library/LibraryBookImage'
import LibraryBookPermission from '@/components/library/LibraryBookPermission'
import LibraryBookStyle from '@/components/library/LibraryBookStyle'
import LibraryBookTemplate from '@/components/library/LibraryBookTemplate'
import LibraryBookTitle from '@/components/library/LibraryBookTitle'
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { mapGetters, mapMutations } from 'vuex'
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
      'setBookImages',
      'setBookStyleSheets',
      'setBookTemplates',
      'setCkEditorStyleSets',
    ]),
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
      console.log(styles)
      this.setCkEditorStyleSets(styles)
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
  background-color: palegoldenrod;
}
.dark-background {
  background-color: #d0eaff;
}
</style>
