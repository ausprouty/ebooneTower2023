import AuthorService from '@/services/AuthorService.js'
import ContentService from '@/services/ContentService.js'
import LogService from '@/services/LogService.js'
//import { mapState } from 'vuex'
export const libraryMixin = {
  //  computed: mapState(['bookmark']),
  data() {
    return {
      library: [
        {
          id: '',
          code: '',
          title: '',
          folder: '',
          index: '',
          style: process.env.VUE_APP_SITE_STYLE,
          image: 'issues.jpg',
          format: 'series',
        },
      ],
      image_dir: 'sites/default/images',
      loading: false,
      loaded: '',
      error: '',
      error_message: '',
      rldir: 'ltr',
      prototype: false,
      prototype_date: null,
      books: [],
      write: false,
      publish: false,
      publish_date: '',
      recnum: '',
      content: {
        recnum: '',
        version: '',
        edit_date: '',
        edit_uid: '',
        publish_uid: '',
        publish_date: '',
        language_iso: '',
        country_code: '',
        folder_name: '',
        filetype: '',
        title: '',
        filename: '',
        text: '',
      },
    }
  },
  // computed: mapState(['user', 'bookmark']),
  methods: {
    async getLibrary() {
      // removed params because we are always using route params
      this.error = this.loaded = null
      this.loading = true
      this.recnum = null
      this.publish_date = null
      //console.log(this.$route.params)
      await AuthorService.bookmark(this.$route.params)

      var response = await ContentService.getLibrary(this.$route.params)
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
      this.image_dir = process.env.VUE_APP_SITE_IMAGE_DIR
      if (typeof this.bookmark.language !== 'undefined') {
        if (typeof this.bookmark.language.image_dir !== 'undefined') {
          this.image_dir = this.bookmark.language.image_dir
          this.rldir = this.bookmark.language.rldir
        }
      }
      this.books = this.bookmark.library.books
      this.loaded = true
      this.loading = false
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
      LogService.consoleLogMessage('from getImages for ' + directory)
      LogService.consoleLogMessage(image_options)
      return image_options
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
    newLibrary() {
      this.books = [
        {
          id: 1,
          code: 'life',
          title: 'Life Principles',
          image: 'life.jpg',
          format: 'series',
          style: process.env.VUE_APP_SITE_STYLE,
        },
      ]
    },
  },
}
