<template>
  <div>
    <NavBar called_by="language" />
    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">There was an error... {{ this.error }}</div>
    <div class="content" v-if="loaded">
      <a v-bind:href="'/languages/' + this.bookmark.country.code">
        <img
          v-bind:src="
            process.env.VUE_APP_SITE_CONTENT_URL +
            this.image_dir +
            '/journey.jpg'
          "
          class="app-img-header"
        />
      </a>
      <div>
        <a href="/sites/default/HD/images/standard/standard.zip"
          >Download Standard Images to Edit</a
        >
      </div>
      <Book v-for="book in library" :key="book.title" :book="book" />
      <div class="version">
        <p class="version">Version 2.05</p>
      </div>
    </div>
  </div>
</template>

<script>
import Book from '@/components/Book.vue'
import { mapState } from 'vuex'
import NavBar from '@/components/NavBarHamburger.vue'
import LogService from '@/services/LogService.js'
import { libraryMixin } from '@/mixins/LibraryMixin.js'

export default {
  mixins: [libraryMixin],
  props: ['country_code', 'language_iso', 'library_code'],
  computed: mapState(['bookmark', 'cssURL', 'standard']),
  components: {
    Book,
    NavBar,
  },
  data() {
    return {
      bookmark: '',
      cssURL: '',
      error: null,
      image_dir: null,
      images: null,
      library: [
        {
          book: '',
          folder: '',
          format: 'series',
          id: '',
          image: process.env.VUE_APP_SITE_IMAGE,
          index: '',
          instructions: '',
          pages: 1,
          style: process.env.VUE_APP_SITE_STYLE,
          title: '',
        },
      ],
      loaded: null,
      loading: false,
      standard: '',
    }
  },
  beforeCreate() {
    this.$route.params.version = 'current'
  },
  async created() {
    try {
      this
      await this.getLibrary(this.$route.params)
      this.loaded = true
      this.loading = false
    } catch (error) {
      LogService.consoleLogError(
        'There was an error in LibraryEdit.vue:',
        error
      )
    }
  },
}
</script>
