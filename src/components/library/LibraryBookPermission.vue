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
    bookPermissionHide() {
      return this.getBookProperty('permission_hide')
    },
    bookPermissionPassword() {
      return this.getBookProperty('permission_password')
    },
    bookPermissionPrototype() {
      return this.getBookProperty('permission_prototype')
    },
    bookPermissionPublish() {
      return this.getBookProperty('permission_publish')
    },
  },
  methods: {
    getBookProperty(property) {
      const books = this.$store.state.bookmark.library.books
      if (books && books[this.index] && books[this.index][property]) {
        return books[this.index][property]
      }
      return ''
    },
    updateBookPermission(permissionType, value) {
      this.$store.commit(`setBookPermission${permissionType}`, {
        index: this.index,
        value: value,
      })
    },
    updatePermissionHide(bookPermissionHide) {
      this.updateBookPermission('Hide', bookPermissionHide)
    },
    updatePermissionPassword(bookPermissionPassword) {
      this.updateBookPermission('Password', bookPermissionPassword)
    },
    updatePermissionPrototype(bookPermissionPrototype) {
      this.updateBookPermission('Prototype', bookPermissionPrototype)
    },
    updatePermissionPublish(bookPermissionPublish) {
      this.updateBookPermission('Publish', bookPermissionPublish)
    },
  },
}
</script>
