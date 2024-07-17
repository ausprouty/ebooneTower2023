import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'

import { mapMutations } from 'vuex'
export const libraryUpdateMixin = {
  methods: {
    ...mapMutations([
      'addBook',
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
  },
  prototypeAll() {
    this.setAllBooksPrototypeToTrue()
  },
  prototypeNone() {
    this.setAllBooksPrototypeToFalse()
  },
  publishAll() {
    this.setAllBooksPublishToFalse()
  },
  publishNone() {
    this.setAllBooksPublishToFalse()
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
  async saveForm(action = null) {
    try {
      // update library file
      var library = this.$store.state.bookmark.library
      this.content.text = JSON.stringify(library)
      var route = this.$route.params
      route.filename = route.library_code
      delete route.folder_name
      this.content.route = JSON.stringify(route)
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
}
