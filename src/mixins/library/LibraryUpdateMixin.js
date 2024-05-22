import AuthorService from '@/services/AuthorService.js'
import ContentService from '@/services/ContentService.js'
import LogService from '@/services/LogService.js'
//import { mapState } from 'vuex'
export const libraryUpdateMixin = {
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
    addNewBookForm() {
      if (this.books.length == 0) {
        this.newLibrary()
      } else {
        this.books.push({
          id: '',
          code: '',
          title: '',
          style: '',
          styles_set: '',
          image: '',
          format: '',
          template: '',
          hide: '',
          password: '',
          prototype: '',
          publish: '',
        })
      }
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
    async saveForm(action = null) {
      try {
        // update library file
        var output = this.$store.state.bookmark.library
        this.content.text = JSON.stringify(output)
        this.$route.params.filename = this.$route.params.library_code
        delete this.$route.params.folder_name
        this.content.route = JSON.stringify(this.$route.params)
        this.content.filetype = 'json'
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
    async saveForm2() {
      try {
        var text = {}
        text.page = ContentService.validate(this.pageText)
        text.footer = ContentService.validate(this.footerText)
        text.style = this.style
        this.content.text = JSON.stringify(text)
        this.$route.params.filename = 'index'
        this.content.route = JSON.stringify(this.$route.params)

        this.content.filetype = 'html'
        //this.$store.dispatch('newBookmark', 'clear')
        var response = await AuthorService.createContentData(this.content)
        if (response.data.error != true) {
          this.$router.push({
            name: 'previewLibraryIndex',
            params: {
              country_code: this.$route.params.country_code,
              language_iso: this.$route.params.language_iso,
            },
          })
        } else {
          this.error = true
          this.loaded = false
          this.error_message = response.data.message
        }
      } catch (error) {
        LogService.consoleLogError(
          'Library Index Edit line 257 says There was an error ',
          error
        )
        this.error = true
        this.loaded = false
        this.error_message = error
      }
    },
    prototypeAll() {
      var arrayLength = this.books.length
      LogService.consoleLogMessage(' Item count:' + arrayLength)
      for (var i = 0; i < arrayLength; i++) {
        this.$v.books.$each.$iter[i].prototype.$model = true
      }
    },
    prototypeNone() {
      var arrayLength = this.books.length
      LogService.consoleLogMessage(' Item count:' + arrayLength)
      for (var i = 0; i < arrayLength; i++) {
        this.$v.books.$each.$iter[i].prototype.$model = false
      }
    },
    publishAll() {
      var arrayLength = this.books.length
      LogService.consoleLogMessage(' Item count:' + arrayLength)
      for (var i = 0; i < arrayLength; i++) {
        this.$v.books.$each.$iter[i].publish.$model = true
      }
    },
    publishNone() {
      var arrayLength = this.books.length
      LogService.consoleLogMessage(' Item count:' + arrayLength)
      for (var i = 0; i < arrayLength; i++) {
        this.$v.books.$each.$iter[i].publish.$model = false
      }
    },
    async revert() {
      var params = {}
      params.recnum = this.recnum
      params.route = JSON.stringify(this.$route.params)
      params.scope = 'library'
      var res = await AuthorService.revert(params)
      //console.log(res.content)
      this.seriesDetails = res.content.text
      this.recnum = res.content.recnum
    },
  },
}
