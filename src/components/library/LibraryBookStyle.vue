<template>
  <div>
    <hr />
    <BaseSelect
      label="Book and Chapters Style Sheet:"
      :options="bookStyleSheets"
      v-model="bookStyle"
      class="field"
    />
    <label>
      Add new stylesheet&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="file" ref="style" v-on:change="checkStyleUpload" />
    </label>
    <hr />
    <div>
      <BaseSelect
        label="ckEditor Style Shown:"
        :options="ckEditorStyleSets"
        v-model="ckEditorStyleSelected"
        class="field"
      />
    </div>
  </div>
</template>
<script>
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
import { mapMutations } from 'vuex'
export default {
  mixins: [libraryGetMixin, libraryUploadMixin],
  props: {
    index: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      styles: [],
    }
  },
  computed: {
    bookStyleSheets() {
      return this.$store.state.bookStyleSheets
    },
    ckEditorStyleSets() {
      return this.$store.state.ckEditorStyleSets
    },
    bookStyle() {
      return this.getBookProperty('style')
    },
    ckEditorStyleSelected() {
      return this.getBookProperty('styles_set')
    },
  },
  created() {
    this.bookStyle = this.getBookProperty('style')
  },
  methods: {
    ...mapMutations(['updateBookStyleSheets']),
    checkStyleUpload(event) {
      const file = event.target.files[0]
      if (file) {
        if (file.type === 'text/css') {
          // Proceed with file upload
          const response = this.handleStyleUpload(file)
          if (response.data) {
            this.$store.commit('updateBookStyleSheets', response.data)
          }
        } else {
          alert('Please upload a valid .css file.')
        }
      }
    },
    getBookProperty(property) {
      const books = this.$store.state.bookmark.library.books
      if (books && books[this.index] && books[this.index][property]) {
        return books[this.index][property]
      }
      return ''
    },
  },
}
</script>
