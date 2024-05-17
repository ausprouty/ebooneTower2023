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
      <button class="button" @click="setNewLibraryBookCode()">Save Code</button>
    </div>
  </div>
</template>
<script>
import LogService from '@/services/LogService.js'
import { mapGetters, mapMutations } from 'vuex'
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
      newLibraryBookCode: null,
    }
  },
  computed: {
    ...mapGetters(['getLibraryBookCodes']),
    libraryBookCode: {
      get() {
        const code = this.$store.state.bookmark.library.books[this.index].code
        console.log(`Getting libraryBookCode: ${code}`) // Debug log
        return code
      },
      set(value) {
        console.log(`Setting libraryBookCode: ${value} at index ${this.index}`) // Debug log
        this.setLibraryBookCode({ index: this.index, code: value })
      },
    },
    libraryBookCodes: {
      get() {
        var standard = this.getLibraryBookCodes
        console.log ('I am looking at standard')
        if (
          this.newLibraryBookCode != null &&
          this.newLibraryBookCodeIsHidden == true
        ) {
          console.log ('I updated standard')
          standard.push(this.newLibraryBookCode)
        }
        console.log (standard)
        return standard
      },
      set() {
        this.setLibraryBookCodes
      },
    },
  },

  methods: {
    ...mapMutations(['setLibraryBookCode', 'addNewLibraryBookCode']),
    updateLibraryBookCode(value) {
      this.libraryBookCode = value
    },
    setNewLibraryBookCode() {
      console.log('setting new library code', this.setLibraryBookCode)
      this.setLibraryBookCode({
        index: this.index,
        code: this.newLibraryBookCode,
      })
      console.log(this.getLibraryBookCodes)
      this.newLibraryBookCodeIsHidden = true
    },
    openCodeBlock() {
      this.newLibraryBookCodeIsHidden = false
    },
  },
}
</script>
