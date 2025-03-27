import LogService from '@/services/LogService.js'
export const publishMixin = {
  methods: {
    mayCreatePDFCountries() {
      if (process.env.VUE_APP_MAKE_PDF == 'TRUE') {
        return this.mayPublishCountries()
      } else {
        return false
      }
    },
    mayCreatePDFLanguages() {
      if (process.env.VUE_APP_MAKE_PDF == 'TRUE') {
        return this.mayPublishLanguages()
      } else {
        return false
      }
    },
    mayCreatePDFLibrary() {
      if (process.env.VUE_APP_MAKE_PDF == 'TRUE') {
        return this.mayPublishLibrary()
      } else {
        return false
      }
    },
    mayCreatePDFSeries() {
      if (process.env.VUE_APP_MAKE_PDF == 'TRUE') {
        return this.mayPublishSeries()
      } else {
        return false
      }
    },
    mayCreatePDFPage() {
      if (process.env.VUE_APP_MAKE_PDF == 'TRUE') {
        return this.mayPublishPage()
      } else {
        return false
      }
    },
    mayCreateSDCardCountries() {
      if (process.env.VUE_APP_MAKE_SDCARD == 'TRUE') {
        return this.mayPublishCountries()
      } else {
        return false
      }
    },
    mayCreateSDCardLanguages() {
      if (process.env.VUE_APP_MAKE_SDCARD == 'TRUE') {
        return this.mayPublishLanguages()
      } else {
        return false
      }
    },
    mayCreateSDCardLibrary() {
      if (process.env.VUE_APP_MAKE_SDCARD == 'TRUE') {
        return this.mayPublishLibrary()
      } else {
        return false
      }
    },
    mayCreateSDCardSeries() {
      if (process.env.VUE_APP_MAKE_SDCARD == 'TRUE') {
        return this.mayPublishSeries()
      } else {
        return false
      }
    },
    mayCreateSDCardPage() {
      if (process.env.VUE_APP_MAKE_SDCARD == 'TRUE') {
        return this.mayPublishPage()
      } else {
        return false
      }
    },

    mayPrototypeCountries() {
      if (!this.authorize('prototype', this.$route.params)) {
        return false
      }
    },
    mayPrototypeLanguages() {
      if (!this.authorize('prototype', this.$route.params)) {
        return false
      }
      if (typeof this.bookmark.country.prototype !== 'undefined') {
        return this.bookmark.country.prototype
      }
      return false
    },
    mayPrototypeLibrary() {
      console.log('in mayPrototypeLibrary')
      if (!this.authorize('prototype', this.$route.params)) {
        console.log('failed first test')
        return false
      }
      console.log(this.bookmark)
      if (this.bookmark.country.prototype && this.bookmark.language.prototype) {
        console.log('past second test')
        return true
      } else {
        console.log(this.bookmark)
        console.log('failed second test')
        return false
      }
    },
    mayPrototypeSeries() {
      if (!this.authorize('prototype', this.$route.params)) {
        return false
      }
      if (
        this.bookmark.country.prototype &&
        this.bookmark.language.prototype &&
        this.bookmark.book.prototype
      ) {
        LogService.consoleLogMessage('source','mayPrototypeSeries returned true')
        return true
      } else {
        return false
      }
    },
    mayPrototypePage() {
      if (!this.authorize('prototype', this.$route.params)) {
        return false
      }
      if (
        this.bookmark.country.prototype &&
        this.bookmark.language.prototype &&
        this.bookmark.book.prototype &&
        this.bookmark.page.prototype
      ) {
        return true
      } else {
        return false
      }
    },
    mayPublishCountries() {
      if (!this.authorize('publish', this.$route.params)) {
        return false
      }
    },
    mayPublishLanguages() {
      if (!this.authorize('publish', this.$route.params)) {
        return false
      }
      return this.bookmark.country.publish
    },
    mayPublishLibrary() {
      console.log('in mayPublishLibrary')
      if (!this.authorize('publish', this.$route.params)) {
        console.log('failed first test')
        return false
      }
      if (this.bookmark.country.publish && this.bookmark.language.publish) {
        console.log('past second test')
        return true
      } else {
        console.log('failed second test')
        return false
      }
    },
    mayPublishSeries() {
      LogService.consoleLogMessage('source','mayPublishSeries called')
      if (!this.authorize('publish', this.$route.params)) {
        LogService.consoleLogMessage('source','mayPublishSeries returned false')
        return false
      }
      if (
        this.bookmark.country.publish &&
        this.bookmark.language.publish &&
        this.bookmark.book.publish
      ) {
        LogService.consoleLogMessage('source','mayPublishSeries returned true')
        return true
      } else {
        return false
      }
    },
    mayPublishPage() {
      if (!this.authorize('publish', this.$route.params)) {
        return false
      }
      if (
        this.bookmark.country.publish &&
        this.bookmark.language.publish &&
        this.bookmark.book.publish &&
        this.bookmark.page.publish
      ) {
        return true
      } else {
        return false
      }
    },
  },
}
