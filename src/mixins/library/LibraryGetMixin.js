import Vue from 'vue'
import Vuex from 'vuex'
import { mapState } from 'vuex'
import LogService from '@/services/LogService.js'
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
      await AuthorService.bookmark(this.$route.params)
    },
    async getCkEditStyleSets(param) {
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
      // get images for library formatted for dropdown
      var image_options = []
      var img = []
      if (where == 'content') {
        img = await AuthorService.getImagesInContentDirectory(directory)
      } else {
        img = await AuthorService.getImagesForSite(directory)
      }
      if (typeof img !== 'undefined') {
        if (img.length > 0) {
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
      }
      return image_options
    },
    async getLibraryBookmark(){
      await AuthorService.bookmark(this.$route.params)
    },
    async getLibraryIndex() {
      this.error = this.loaded = null
      this.loading = true
      this.recnum = null
      this.publish_date = null
      //await this.UnsetBookmarks()
      await AuthorService.bookmark(this.$route.params)
      var response = await ContentService.getLibraryIndex(this.$route.params)
      if (response) {
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
      var templates = await AuthorService.getTemplates(param)
      return templates
    },
  },
}
