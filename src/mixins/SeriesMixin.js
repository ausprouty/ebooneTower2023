import AuthorService from '@/services/AuthorService.js'
import ContentService from '@/services/ContentService.js'
import LogService from '@/services/LogService.js'

export const seriesMixin = {
  methods: {
    async getSeries(params) {
      try {
        console.log('params in SeriesMixin ')
        console.log(params)
        console.log(
          'VUE_APP_SITE_IMAGE_DIR:',
          process.env.VUE_APP_SITE_IMAGE_DIR
        )
        console.log('VUE_APP_SITE_STYLE:', process.env.VUE_APP_SITE_STYLE)

        this.error = this.loaded = null
        this.loading = true
        await AuthorService.bookmark(params)
      } catch (error) {
        LogService.consoleLogError(
          'There was an error withcheckBookmarks in CountriesMixin:',
          error
        )
        this.$router.push({
          name: 'login',
        })
      }
      try {
        var response = await ContentService.getSeries(params)

        if (typeof response == 'undefined') {
          console.log('No Series Data obtained')
          this.chapters = []
          this.new = true
          this.description = null
          this.loaded = true
          this.loading = false
          return
        }
        if (typeof response.text != 'undefined') {
          console.log('Series Data obtained')
          console.log(response)
          // latest data
          if (typeof response.recnum != 'undefined') {
            this.recnum = response.recnum
            this.publish_date = response.publish_date
            this.prototype_date = response.prototype_date
          }
          console.log(response.text)
          // this.seriesDetails = JSON.parse(response.text)
          this.seriesDetails = response.text
          console.log('Series Details')
          console.log(this.seriesDetails)
          this.chapters = this.seriesDetails.chapters
          this.description = this.seriesDetails.description
          this.download_now = this.seriesDetails.download_now
          this.download_ready = this.seriesDetails.download_ready
        } else {
          this.description = response.description
          this.chapters = response.chapters
        }
        //TODO: evaluate if we want to always be able to add with tab delimited file
        //this.new = false
        //if (!this.chapters) {
        this.new = true
        // }

        this.image_dir = process.env.VUE_APP_SITE_IMAGE_DIR
        if (typeof this.bookmark.language.image_dir != 'undefined') {
          console.log('USING BOOKMARK')
          this.image_dir = this.bookmark.language.image_dir
        }
        //console.log('look at vue app site dir')
        //console.log(process.env.VUE_APP_SITE_DIR)
        if (typeof this.bookmark.book.image.image != 'undefined') {
          this.book_image = this.bookmark.book.image.image
        }

        this.style = process.env.VUE_APP_SITE_STYLE
        console.log(this.style)
        if (typeof this.bookmark.book.style != 'undefined') {
          console.log('USING BOOKMARK')
          this.style = this.bookmark.book.style
        }
        this.rldir = this.bookmark.language.rldir
        console.log('this.image_dir')
        console.log(this.image_dir)
        this.loaded = true
        console.log('loaded ' + this.loaded)
        this.loading = false
        console.log('loading ' + this.loading)
        console.log('finished with SeriesMizin')
      } catch (error) {
        console.log('error in getSeries')
        LogService.consoleLogError('There was an error in SeriesMixin:', error) // Logs out the error
        this.$router.push({
          name: 'login',
        })
      }
    },

    newSeries() {
      return
    },
  },
}
