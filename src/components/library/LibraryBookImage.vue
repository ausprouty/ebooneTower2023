<template>
  <div>
    This is the start of the Image area
    <div v-if="imagesUsed">
      <div>
        <h3>Book Image:</h3>
        <div v-if="bookImagePermission">
          <img v-bind:src="bookImage" class="book" />
          <br />
        </div>
        <v-select :options="imageOptions" label="title" v-model="bookImage">
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
            v-on:change="handleImageUpload(imageFile)"
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
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
export default {
  mixins: [libraryUploadMixin],
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
    }
  },
  computed: {
    ...mapGetters(['getLibraryBookImage']),
    bookImage: {
      get() {
        const image = this.getLibraryBookImage(this.index)
        return image.image
      },
      set(value) {
        this.setLibraryBookImage({ index: this.index, code: value })
      },
    },
  },
  methods:{
    ...mapMutations(['setLibraryBookImage']),
    getBookImage(id) {
      const image = this.getLibraryBookImage(this.index);
      return image ? image.image : null;
    },
  }
}
</script>
