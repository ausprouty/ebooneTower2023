<template>
  <div>
    <div>
      <p>Code:</p>
      <v-select
        :options="libraryBookCodes"
        label="Code"
        :v-model="libraryBookCode"
        @input="updateLibraryBookCode"
      />
    </div>
    <div>
      <p>
        <a class="black" @click="createLibraryBookCode()">Create new Code</a>
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
      <button class="button" @click="addNewLibraryBookCode()">Save Code</button>
    </div>
  </div>
</template>
<script>
import LogService from '@/services/LogService.js'
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
    }
  },
  computed: {
    libraryBookCode() {
      return this.$store.state.bookmark.library.books[this.index].code
    },
    
  },

  methods: {
    updateLibraryBookCode(newValue) {
      // Perform any necessary operations before updating, if needed
      // For example, you might want to validate the new value here
      // Then update the value
      this.libraryBookCode = newValue
    },
    createLibraryBookCode() {
      this.newLibraryBookCodeIsHidden = false
    },
    setLibraryBookCode(index, value) {
      this.$store.commit('setLibraryBookCode', { index, value })
    },

    addNewLibraryBookCode() {},
    updateLibraryBookCodes() {},
  },
}
</script>
