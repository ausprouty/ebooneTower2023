<template>
  <div>
    <div>
      <p>Code:</p>
      <v-select
        :options="combinedLibraryBookCodes"
        label="Code"
        v-model="selectedLibraryBookCode"
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
        ref="newCodeInput"
        label="New Code:"
        v-model="typedLibraryBookCode"
        type="text"
        placeholder="Enter new code"
        class="field"
      />
      <button class="button" @click="saveTypedLibraryBookCode()">
        Save Code
      </button>
    </div>
  </div>
</template>

<script>
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
      typedLibraryBookCode: '', // Store the new typed code
      localBookCodes: [], // Maintain local codes within the component
      selectedLibraryBookCode: '', // Internal model for v-select
    }
  },
  computed: {
    ...mapGetters(['getLibraryBookCodes']),
    ...mapState(['newLibraryBookCode']),

    // Combine Vuex codes with local codes dynamically
    combinedLibraryBookCodes() {
      const codes = [...this.getLibraryBookCodes, ...this.localBookCodes]
      return codes.sort() // Return a sorted list of codes
    },
  },
  watch: {
    // Keep Vuex and the component in sync whenever the selected code changes
    selectedLibraryBookCode(newValue) {
      this.setLibraryBookCode({ index: this.index, code: newValue })
    },
  },
  created() {
    // Initialize the selected code from Vuex when the component is created
    const books = this.$store.state.bookmark.library.books
    const book = books && books[this.index]
    if (book && book.code) {
      this.selectedLibraryBookCode = book.code
    } else {
      console.warn(`No code found for book at index ${this.index}`)
    }
  },
  methods: {
    ...mapMutations(['setLibraryBookCode']), // Store the selected code in Vuex

    // Save the new code locally and select it in the dropdown
    saveTypedLibraryBookCode() {
      const newCode = this.typedLibraryBookCode
      if (newCode && !this.localBookCodes.includes(newCode)) {
        this.localBookCodes.push(newCode) // Add new code to local state

        // Ensure Vue updates the dropdown after mutation
        this.$nextTick(() => {
          this.selectedLibraryBookCode = newCode // Set new code as selected
        })

        this.newLibraryBookCodeIsHidden = true // Hide the input field
        this.typedLibraryBookCode = '' // Clear the input field
      }
    },

    // Open the input field and set focus on it
    openCodeBlock() {
      this.newLibraryBookCodeIsHidden = false

      // Focus the input field after the DOM updates
      this.$nextTick(() => {
        this.$refs.newCodeInput.focus() // Call the focus() method on BaseInput
      })
    },
  },
}
</script>

<style scoped>
.hidden {
  display: none;
}
</style>
