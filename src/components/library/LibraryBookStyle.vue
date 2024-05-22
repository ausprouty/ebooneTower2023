<template>
  <div>
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
    <div>
      <BaseSelect
        label="ckEditor Style Shown:"
        :options="ckEditStyleSets"
        v-model="ckEditorStyleSelected"
        class="field"
      />
    </div>
  </div>
</template>
<script>
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
import { mapMutations, mapState } from 'vuex'
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
      ckEditStyleSets: ['default'],
      ckEditorStyleSelected: 'default',
      bookStyle: '',
    }
  },
  computed: {
    bookStyleSheets() {
      return this.$store.state.bookStyleSheets
    },
  },
  created() {
    const style = this.$store.state.bookmark.library.books[this.index].style
    if (style) {
      this.bookStyle = style
    }
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
  },
}
</script>
