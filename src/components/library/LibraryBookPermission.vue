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
export default {
  props: {
    index: {
      type: Number,
      required: true,
    },
  },
  methods: {
    updatePermissionHide(bookPermissionHide) {
      this.$store.commit('setBookPermissionHide', {
        index: this.index,
        value: bookPermissionHide,
      })
    },
    updatePermissionPassword(bookPermissionPassword) {
      this.$store.commit('setBookPermissionPassword', {
        index: this.index,
        value: bookPermissionPassword,
      })
    },
    updatePermissionPrototype(bookPermissionPrototype) {
      this.$store.commit('setBookPermissionPrototype', {
        index: this.index,
        value: bookPermissionPrototype,
      })
    },
    updatePermissionPublish(bookPermissionPublish) {
      this.$store.commit('setBookPermissionPublish', {
        index: this.index,
        value: bookPermissionPublish,
      })
    },
  },
}
</script>
