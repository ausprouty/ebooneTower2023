<template>
  <div>
    <div>
      <p>Code:</p>
      <v-select
        :options="combinedLibraryBookCodes"
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
        v-model="typedLibraryBookCode"
        type="text"
        placeholder="code"
        class="field"
      />
      <button class="button" @click="setNewLibraryBookCode()">Save Code</button>
    </div>
  </div>
</template>
<script>
import LogService from '@/services/LogService.js'
import { mapGetters, mapMutations, mapState } from 'vuex'
import vSelect from 'vue-select'

export default {
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
      newLibraryBookCodeIsHidden: true,
      typedLibraryBookCode: null,
    }
  },
  computed: {
    ...mapGetters(['getLibraryBookCodes']),
    ...mapState(['newLibraryBookCode']),
    libraryBookCode: {
      get() {
        const books = this.$store.state.bookmark.library.books
        const book = books && books[this.index]
        if (this.typedLibraryBookCode && this.newLibraryBookCodeIsHidden) {
          return this.typedLibraryBookCode
        } else {
          return book ? book.code : undefined
        }
      },
      set(value) {
        this.setLibraryBookCode({ index: this.index, code: value })
      },
    },
    combinedLibraryBookCodes() {
      let codes = [...this.getLibraryBookCodes]
      if (this.typedLibraryBookCode && this.newLibraryBookCodeIsHidden) {
        codes.push(this.typedLibraryBookCode)
      }
      return codes.sort()
    },
  },
  methods: {
    ...mapMutations([
      'setLibraryBookCode',
      'addNewLibraryBookCode',
      'setNewLibraryBookCode',
    ]),
    updateLibraryBookCode(value) {
      this.libraryBookCode = value
    },
    setNewLibraryBookCode() {
      this.addNewLibraryBookCode(this.newLibraryBookCode)
      this.newLibraryBookCodeIsHidden = true
    },
    openCodeBlock() {
      this.newLibraryBookCodeIsHidden = false
    },
  },
}
</script>
<style scoped>
.hidden {
  display: none;
}
</style>
