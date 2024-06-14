<template>
  <div>
    <div v-if="imagesUsed">
      <div>
        <h3>Book Image:</h3>
        <div v-if="bookImagePermission">
          <img v-bind:src="displayBookImage()" class="book" />
          <br />
        </div>
        <v-select
          :options="imageOptions"
          label="title"
          v-model="selectedBookImage"
        >
          <template slot="option" slot-scope="option">
            <img class="select" :src="option.image" />

            <br />
            {{ option.title }}
          </template>
        </v-select>
      </div>
      <div v-if="authorImagePermission">
        <label>
          Add new Image&nbsp;&nbsp;&nbsp;&nbsp;
          <input
            type="file"
            ref="image"
            v-on:change="onAddNewImage($event.target.files[0])"
          />
        </label>
      </div>
    </div>
  </div>
</template>
<script>
import vSelect from 'vue-select'
import { mapMutations, mapState } from 'vuex'
import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
export default {
  mixins: [libraryUpdateMixin, libraryUploadMixin],
  props: {
    index: {
      type: Number,
      required: true,
    },
  },
  components: {
    'v-select': vSelect,
  },
  data() {
    return {
      bookImagePermission: true,
      authorImagePermission: true,
      imagesUsed: true,
      selectedBookImage: null, // Initially no image selected
    }
  },
  created() {
    const bookImage = this.$store.state.bookmark.library.books[this.index].image
    if (bookImage) {
      this.selectedBookImage = bookImage
    }
  },
  computed: {
    imageOptions() {
      return this.$store.state.BookImages
    },
  },
  watch: {
    selectedBookImage(newVal) {
      this.onSelectedBookImageChange(newVal)
    },
  },
  methods: {
    ...mapMutations(['setBookImage']),
    displayBookImage() {
      if (this.selectedBookImage) {
        return this.selectedBookImage.image
      }
      const bookImage =
        this.$store.state.bookmark.library.books[this.index].image
      if (bookImage) {
        return bookImage.image
      }
      return null
    },
    onSelectedBookImageChange(newVal, oldVal) {
      this.$store.commit('setBookImage', {
        index: this.index,
        image: newVal,
      })
    },
    async onAddNewImage(fileName) {
      await this.handleImageUpload(fileName)
      console.log(fileName)
      const newFile = {
        title: fileName.name,
        image:
          this.$store.state.bookmark.language.image_dir + '/' + fileName.name,
      }
      this.selectedBookImage = newFile
    },
  },
}
</script>
