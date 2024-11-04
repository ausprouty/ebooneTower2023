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
    ckEditorStyleSelected: {
      get() {
        return this.$store.getters.getBookProperty(this.index, 'styles_set')
      },
      set(value) {
        this.$store.commit('setBookProperty', {
          index: this.index,
          property: 'styles_set',
          value,
        })
      },
    },
    bookStyle: {
      get() {
        return this.$store.getters.getBookProperty(this.index, 'style')
      },
      set(value) {
        this.$store.commit('setBookProperty', {
          index: this.index,
          property: 'style',
          value,
        }) // Update book style in store
      },
    },
  },
  created() {
    this.bookStyle = this.$store.getters.getBookProperty(
      this.index,
      'styles_set'
    )
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
