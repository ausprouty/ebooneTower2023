<template>
  <div class="Xapp-link" @click="showPage(book)">
    <div class="Xapp-card -Xshadow" :class="{ notpublished: !book.publish }">
      <div v-if="!bookmark || !bookmark.language || !bookmark.language.titles">
        <img
          :src="(book && book.image && book.image.image) || defaultImage"
          class="book"
        />
        <div
          class="book"
          :class="
            (bookmark && bookmark.language && bookmark.language.rldir) || 'ltr'
          "
        >
          <span
            class="title"
            :class="
              (bookmark && bookmark.language && bookmark.language.rldir) ||
              'ltr'
            "
          >
            {{ book.title }}
          </span>
        </div>
      </div>

      <div v-else>
        <img
          :src="(book && book.image && book.image.image) || defaultImage"
          class="book-large"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import LogService from '@/services/LogService.js'

export default {
  props: {
    book: {
      type: Object,
      required: true,
      default: () => ({ title: '', image: { image: '' }, publish: false }),
    },
  },
  data() {
    return {
      image_dir: '',
      defaultImage: '/sites/default/images/book.png', // Fallback image
    }
  },
  computed: {
    ...mapState(['bookmark', 'standard']),
  },
  created() {
    if (this.bookmark && this.bookmark.language) {
      LogService.consoleLogMessage('BOOK PREVIEW - Using bookmark directory')
      this.image_dir = this.bookmark.language.image_dir
    } else {
      LogService.consoleLogMessage('BOOK PREVIEW - Using default directory')
      this.image_dir = process.env.VUE_APP_SITE_IMAGE_DIR
    }
  },
  methods: {
    showPage(book) {
      localStorage.setItem('lastPage', 'library/country/language')
      const code = book.code || book.name

      const params = {
        country_code: this.$route.params.country_code,
        language_iso: this.$route.params.language_iso,
        library_code: this.$route.params.library_code,
      }

      if (book.format === 'series') {
        this.$router.push({
          name: 'previewSeries',
          params: { ...params, folder_name: code },
        })
      } else if (book.format === 'library') {
        this.$router.push({
          name: 'previewLibrary2',
          params: { ...params, library_code: book.code },
        })
      } else if (book.format === 'page') {
        this.$router.push({
          name: 'previewPage',
          params: { ...params, folder_name: 'pages', filename: code },
        })
      }
    },
  },
}
</script>

<style scoped>
img.book {
  width: 25%;
}

div.book {
  vertical-align: top;
  width: 70%;
  font-size: 24px;
  float: right;
}

div.book.rtl {
  width: 30%;
  text-align: left;
}

.book {
  text-align: left;
}

.rtl {
  text-align: right;
  direction: rtl;
}
</style>
