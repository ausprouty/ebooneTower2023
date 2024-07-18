<template>
  <div>
    <br />
    <label for="checkbox">
      <h3>Hide on library page?&nbsp;&nbsp;</h3>
    </label>
    <input
      type="checkbox"
      id="checkbox"
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

    <label for="checkbox">
      <h3>Prototype?&nbsp;&nbsp;</h3>
    </label>
    <input
      type="checkbox"
      id="checkbox"
      v-model="bookPermissionPrototype"
      @change="updatePermissionPrototype(bookPermissionPrototype)"
    />
    <br />

    <label for="checkbox">
      <h3>Publish?&nbsp;&nbsp;&nbsp;</h3>
    </label>
    <input
      type="checkbox"
      id="checkbox"
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
        this.updatePermissionHide(value)
      },
    },
    bookPermissionPassword: {
      get() {
        return this.getBookProperty('password')
      },
      set(value) {
        this.updatePermissionPassword(value)
      },
    },
    bookPermissionPrototype: {
      get() {
        return this.getBookProperty('prototype')
      },
      set(value) {
        this.updatePermissionPrototype(value)
      },
    },
    bookPermissionPublish: {
      get() {
        return this.getBookProperty('publish')
      },
      set(value) {
        this.updatePermissionPublish(value)
      },
    },
  },
  methods: {
    getBookProperty(property) {
      console.log(property)
      const books = this.$store.state.bookmark.library.books
      if (books && books[this.index] && books[this.index][property]) {
        console.log(property + ' ' + books[this.index][property])
        return books[this.index][property]
      }
      console.log(property + ' not found')
      return ''
    },
    updateBookPermission(permissionType, value) {
      this.$store.commit(`setBookPermission${permissionType}`, {
        index: this.index,
        value: value,
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
