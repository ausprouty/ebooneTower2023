<template>
  <div>
    <div>
      <p>Code:</p>
      <v-select
        :options="libraryBookCodes"
        label="Code"
        v-model="libraryBookCode"
        @input="updateLibraryBookCode"
      />
    </div>
    <div>
      <p>
        <a class="black" @click="openCodeBlock()">Create new Code</a>
      </p>
    </div>
    <div
      v-bind:id="index"
      v-bind:class="{ hidden: newLibraryBookCodeIsHidden }"
    >
      <BaseInput
        label="New Code:"
        v-model="newLibraryBookCode"
        type="text"
        placeholder="code"
        class="field"
      />
      <button class="button" @click="updateLibraryBookCode()">Save Code</button>
    </div>
  </div>
</template>
<script>
import LogService from '@/services/LogService.js'
import { mapGetters, mapMutations } from 'vuex'
import vSelect from 'vue-select'

export default {
  props: {
    index: Number,
  },
  components: {
    'v-select': vSelect,
  },
  data() {
    return {
      newLibraryBookCodeIsHidden: true,
      newLibraryBookCode: null,
    }
  },
  computed: {
    ...mapGetters(['getLibraryBookCodes']),
    libraryBookCode: {
      get() {
        return this.$store.state.bookmark.library.books[this.index].code
      },
      set(value) {
        this.setLibraryBookCode({ index: this.index, code: value })
      },
    },
    libraryBookCodes() {
      return this.getLibraryBookCodes
    },
  },

  methods: {
    ...mapMutations(['setLibraryBookCode', 'addNewLibraryBookCode']),
    updateLibraryBookCode(value) {
      this.setLibraryBookCode({ index: this.index, code: value })
      this.libraryBookCode = value
      this.newLibraryBookCodeIsHidden = false
    },
    openCodeBlock() {
      this.newLibraryBookCodeIsHidden = false
    },
  },
}
</script>
