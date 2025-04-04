import Vue from 'vue'
import Vuex from 'vuex'
import { mapState } from 'vuex'
import ContentService from '@/services/ContentService.js'
import AuthorService from '@/services/AuthorService.js'
//import { timeout } from 'q'
Vue.use(Vuex)

export const libraryGetMixin = {
  computed: mapState(['user']),
  methods: {
    async getBookCodes() {
      await AuthorService.bookmark(this.$route.params)
    },
    async getBookmark() {
      console.log('getBookmark started')
      console.log(this.$route.params)
      await AuthorService.bookmark(this.$route.params)
      console.log('getBookmark finished')
    },
    async getCkEditorStyleSets(param) {
      console.log('getCkEditorStyleSets', param)
      var ckEditStyleSets = await AuthorService.getCkEditStyleSets(param)
      return ckEditStyleSets
    },
    async getFoldersContent(bookmark) {
      var param = {}
      param.route = JSON.stringify(this.$route.params)
      param.image_dir = bookmark.language.image_dir
      var folders = await AuthorService.getFoldersContent(param)
      return folders
    },
    async getImages(where, directory) {
      console.log('getting Images for ', where)
      console.log('in directory ', directory)
      // get images for library formatted for dropdown
      var image_options = []
      var img = []
      if (where == 'content') {
        img = await AuthorService.getImagesInContentDirectory(directory)
      } else {
        img = await AuthorService.getImagesForSite(directory)
      }
      console.log('getImages response')
      console.log(img)
      console.log('img is', img) // Check what you're getting
      if (Array.isArray(img) && img.length > 0) {
        img = img.sort()
        var length = img.length
        var i = 0
        var pos = 0
        for (i = 0; i < length; i++) {
          var formatted = {}
          pos = img[i].lastIndexOf('/') + 1
          formatted.title = img[i].substring(pos)
          formatted.image = img[i]
          image_options.push(formatted)
        }
      }
      return image_options
    },
    async getLibraryBookmark() {
      await AuthorService.bookmark(this.$route.params)
    },
    async getLibrary() {
      this.error = this.loaded = null
      this.loading = true
      this.recnum = null
      this.publish_date = null
      this.prototype_date = null
      var response = await ContentService.getLibrary(this.$route.params)
      if (response) {
        console.log('I am in getLibrary')
        console.log(response)
        if (response.recnum) {
          this.recnum = response.recnum
          this.publish_date = response.publish_date
          this.prototype_date = response.prototype_date
        }
      }
    },
    async getLibraryIndex() {
      this.error = this.loaded = null
      this.loading = true
      this.recnum = null
      this.publish_date = null
      //await this.UnsetBookmarks()
      await AuthorService.bookmark(this.$route.params)
      console.log(this.$route.params)
      var response = await ContentService.getLibraryIndex(this.$route.params)
      if (response) {
        console.log('I am in getLibraryIndex')
        console.log(response)
        if (response.recnum) {
          this.recnum = response.recnum
          this.publish_date = response.publish_date
          this.prototype_date = response.prototype_date
        }
        var text = response.text
        this.pageText = text.page
        this.style = text.style
        this.footerText = text.footer
      } else {
        this.pageText = ''
        this.style = ''
        this.footerText = ''
      }

      this.loaded = true
      this.loading = false
    },

    async getStyles(params) {
      var styles = await AuthorService.getStyles(params)
      return styles
    },
    async getTemplates(param) {
      console.log('LibraryMixinGetTemplates', param)
      var templates = await AuthorService.getTemplates(param)
      console.log('getTemplates response: ' + templates)
      return templates
    },
  },
}
