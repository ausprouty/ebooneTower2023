<template>
  <div>
    <NavBar called_by="library" />

    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">
      There was an error... {{ error_message }}
    </div>

    <div class="content" v-if="loaded">
      <div v-if="!authorized">
        <BaseNotAuthorized />
      </div>

      <div v-else>
        <h1>
          Library
          <a
            target="_blank"
            class="help"
            :href="prototype_url + 'HD/eng/help-1/library_sort.html'"
          >
            <img class="help-icon" src="/sites/default/images/icons/help.png" />
          </a>
        </h1>
        <div>
          <!-- Draggable component for reordering books -->
          <draggable v-model="books" @end="updateBooksOrder">
            <transition-group>
              <div v-for="book in books" :key="book.title" :book="book">
                <div class="shadow-card -shadow">
                  <img
                    src="/sites/default/images/icons/move2red.png"
                    class="sortable"
                  />
                  <span class="card-name">{{ book.title }}</span>
                </div>
              </div>
            </transition-group>
          </draggable>
        </div>
        <button class="button" @click="saveForm">Save</button>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBarAdmin.vue'
import ContentService from '@/services/ContentService.js'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
import draggable from 'vuedraggable'
import { mapState, mapMutations } from 'vuex'
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'

export default {
  mixins: [libraryGetMixin, libraryUpdateMixin, authorizeMixin],
  props: ['country_code', 'language_iso', 'library_code'],
  computed: {
    ...mapState(['bookmark', 'cssURL', 'standard']),
  },

  components: {
    NavBar,
    draggable,
  },
  data() {
    return {
      authorized: false,
      books:[],
      content: {},
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      loading: true, // Initialize loading state
      loaded: false, // Initialize loaded state
      error: false, // Initialize error state
      error_message: '', // Error message for display
    }
  },
  methods: {
    ...mapMutations(['setBooks']),
    updateBooksOrder() {
      this.setBooks(this.books) // Commit reordered books to Vuex
    },
    async saveForm() {
      try {
        const valid = this.$store.state.bookmark.library
        this.content.text = JSON.stringify(valid)
        const route = {
          ...this.$route.params,
          filename: this.$route.params.library_code,
        }
        this.content.route = JSON.stringify(route)
        this.content.filetype = 'json'
        console.log (this.content)
        const response = await AuthorService.createContentData(this.content)
        if (response.data.error !== true) {
          this.$router.push({
            name: 'previewLibrary',
            params: {
              country_code: this.$route.params.country_code,
              language_iso: this.$route.params.language_iso,
              library_code: this.$route.params.library_code,
            },
          })
        } else {
          this.error = true
          this.loaded = false
          this.error_message = response.data.message
        }
      } catch (error) {
        LogService.consoleLogError('LIBRARY SORT encountered an error:', error)
        this.error = true
        this.loaded = false
        this.error_message = 'An unexpected error occurred while saving.'
      }
    },
  },
  async created() {
    try {
      await this.getBookmark()
      this.books = this.$store.state.bookmark.library.books
      this.authorized = this.authorize('write', this.$route.params)
      this.loaded = true
      this.loading = false
    } catch (error) {
      LogService.consoleLogError('Error in LibrarySort.vue:', error)
      this.error = true
      this.loading = false
      this.error_message = 'Failed to load library data.'
    }
  },
}
</script>
