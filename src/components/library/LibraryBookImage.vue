<template>
  <div>
    This is the start of the Image area
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
            v-bind:id="imageFile"
            ref="image"
            v-on:change="handleImageUpload($event.target.files[0])"
          />
        </label>
      </div>
    </div>
    This is the end of the image area
  </div>
</template>
<script>
import vSelect from 'vue-select'
import { mapGetters, mapMutations, mapState } from 'vuex'
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
      imageFile: null,
      selectedBookImage: null, // Initially no image selected
    }
  },
  created() {
    if (this.$store.state.bookmark.library.books[this.index].image) {
      this.selectedBookImage =
        this.$store.state.bookmark.library.books[this.index].image
    }
  },
  computed: {
    imageOptions() {
      return this.$store.state.imagesForBooks
    },
  },
  watch: {
    selectedBookImage(newVal, oldVal) {
      this.onSelectedBookImageChange(newVal)
    },
  },
  methods: {
    ...mapMutations(['setImageForBook']),
    displayBookImage() {
      if (this.selectedBookImage) {
        return this.selectedBookImage.image
      }
      if (this.$store.state.bookmark.library.books[this.index].image) {
        return this.$store.state.bookmark.library.books[this.index].image.image
      } else {
        return null
      }
    },
    onSelectedBookImageChange(newVal) {
      this.$store.commit('setImageForBook', {
        index: this.index,
        image: newVal,
      })
    },
    onSelectImage(fileName){
      this.handleImageUpload(fileName)
    }

  },
}
</script>
