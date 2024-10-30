<template>
  <div>
    <br />
    <label for="hideCheckbox">
      <h3>Hide on library page?&nbsp;&nbsp;</h3>
    </label>
    <input
      type="checkbox"
      id="hideCheckbox"
      v-model="bookPermissionHide"
      @change="updatePermissionHide(bookPermissionHide)"
    />
    <br />

    <BaseInput
      label="Password (to add hidden item to library page):"
      v-model="bookPermissionPassword"
      type="text"
      placeholder="password"
      class="field"
      @change="updatePermissionPassword(bookPermissionPassword)"
    />

    <label for="prototypeCheckbox">
      <h3>Prototype?&nbsp;&nbsp;</h3>
    </label>
    <input
      type="checkbox"
      id="prototypeCheckbox"
      v-model="bookPermissionPrototype"
      @change="updatePermissionPrototype(bookPermissionPrototype)"
    />
    <br />

    <label for="publishCheckbox">
      <h3>Publish?&nbsp;&nbsp;&nbsp;</h3>
    </label>
    <input
      type="checkbox"
      id="publishCheckbox"
      v-model="bookPermissionPublish"
      @change="updatePermissionPublish(bookPermissionPublish)"
    />
    <br />
    <br />
  </div>
</template>
<script>
import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'

export default {
  props: {
    index: {
      type: Number,
      required: true,
    },
  },
  mixins: [libraryUpdateMixin],
  computed: {
    bookPermissionHide: {
      get() {
        return this.getBookProperty('hide')
      },
      set(value) {
        this.ensureBookProperty('hide', value)
        this.updatePermissionHide(value)
      },
    },
    bookPermissionPassword: {
      get() {
        return this.getBookProperty('password')
      },
      set(value) {
        this.ensureBookProperty('password', value)
        this.updatePermissionPassword(value)
      },
    },
    bookPermissionPrototype: {
      get() {
        return this.getBookProperty('prototype')
      },
      set(value) {
        this.ensureBookProperty('prototype', value)
        this.updatePermissionPrototype(value)
      },
    },
    bookPermissionPublish: {
      get() {
        return this.getBookProperty('publish')
      },
      set(value) {
        this.ensureBookProperty('publish', value)
        this.updatePermissionPublish(value)
      },
    },
  },
  methods: {
    getBookProperty(property) {
      const books = this.$store.state.bookmark.library.books
      if (books && books[this.index]) {
        if (!(property in books[this.index])) {
          console.warn(
            `Initializing '${property}' for book at index ${this.index}`
          )
          this.ensureBookProperty(property, null) // Initialize to null
        }
        return books[this.index][property]
      }
      console.warn(`Book not found at index ${this.index}`)
      return ''
    },
    ensureBookProperty(property, value = null) {
      const books = this.$store.state.bookmark.library.books
      if (books && books[this.index] && !(property in books[this.index])) {
        console.log(
          `Setting '${property}' to ${value} for book at index ${this.index}`
        )
        this.$set(books[this.index], property, value)
      }
    },
    updateBookPermission(permissionType, value) {
      this.$store.commit(`setBookPermission${permissionType}`, {
        index: this.index,
        value,
      })
    },
    updatePermissionHide(value) {
      this.updateBookPermission('Hide', value)
    },
    updatePermissionPassword(value) {
      this.updateBookPermission('Password', value)
    },
    updatePermissionPrototype(value) {
      this.updateBookPermission('Prototype', value)
    },
    updatePermissionPublish(value) {
      this.updateBookPermission('Publish', value)
    },
  },
}
</script>
