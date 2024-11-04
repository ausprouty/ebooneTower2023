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
          <input type="file" ref="image" v-on:change="onAddNewImage($event)" />
        </label>
      </div>
    </div>
  </div>
</template>
<script>
import vSelect from 'vue-select'
import { mapMutations } from 'vuex'
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
      console.log(this.$store.state.bookImages)
      return this.$store.state.bookImages
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
      // Retrieve the book at the specified index
      let book = this.$store.state.bookmark.library.books[this.index]
      if (book && book.image && book.image.image) {
        // Return the actual image URL
        return book.image.image
      }
      return null // Return null if no valid image found
    },
    onSelectedBookImageChange(newVal) {
      this.$store.commit('setBookImage', {
        index: this.index,
        image: newVal, // Pass the entire newVal object, which includes image and title
      })
    },
    async onAddNewImage(event) {
      // Prevent the default form behavior that might cause a refresh
      event.preventDefault()

      const fileName = event.target.files[0]
      if (!fileName) return

      await this.handleImageUpload(fileName)

      this.selectedBookImage = `${this.$store.state.bookmark.language.image_dir}/${fileName.name}`
    },
  },
}
</script>
