<template>
  <div>
    <div>
      <v-select
        :options="bookcodes"
        label="Code"
        :class="{ error: book.code.$error }"
        @mousedown="book.code.$touch()"
        v-model="book.code.$model"
      />
    </div>
    <div>
      <p>
        <a class="black" @click="createBook(book.code.$model)"
          >Create new Code</a
        >
      </p>
    </div>
    <div v-bind:id="book.title.$model" v-bind:class="{ hidden: isHidden }">
      <BaseInput
        label="New Code:"
        v-model="book.code.$model"
        type="text"
        placeholder="code"
        class="field"
      />
      <button class="button" @click="addNewBookTitle(book.title.$model)">
        Save Code
      </button>
    </div>
  </div>
</template>
<script>
import LogService from '@/services/LogService.js'
export default {
  methods: {
    addNewBookTitle(title) {
      LogService.consoleLogMessage('I came to addNewBookTitle')
      LogService.consoleLogMessage(title)
      this.bookcodes = []
      var change = this.$v.books.$model
      LogService.consoleLogMessage('change')
      LogService.consoleLogMessage(change)
      var arrayLength = change.length
      for (var i = 0; i < arrayLength; i++) {
        this.bookcodes.push(this.$v.books.$model[i].code)
      }
      LogService.consoleLogMessage(this.bookcodes)
      LogService.consoleLogMessage('about to hide')
      this.isHidden = true
      LogService.consoleLogMessage('hidden')
      //make bookcodes list
      this.updateBookCodes()
    },
    createBook(title) {
      LogService.consoleLogMessage(title)
      this.isHidden = false
    },
    updateBookCodes() {
      var arrayLength = this.bookmark.library.books.length
      if (typeof arrayLength !== 'undefined') {
        for (var i = 0; i < arrayLength; i++) {
          if (!this.bookcodes.includes(this.bookmark.library.books[i].code)) {
            this.bookcodes.push(this.bookmark.library.books[i].code)
          }
        }
        if (this.bookcodes.length > 0) {
          this.bookcodes.sort()
        }
      }
    },
  },
}
</script>
