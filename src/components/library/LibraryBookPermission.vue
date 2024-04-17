<template>
  <div>
    <br />
    <label for="checkbox">
      <h3>Hide on library page?&nbsp;&nbsp;</h3>
    </label>
    <input type="checkbox" id="checkbox" v-model="book.hide.$model" />
    <br />

    <BaseInput
      label="Password (to add hidden item to library page):"
      v-model="book.password.$model"
      type="text"
      placeholder="password"
      class="field"
    />
    <label for="checkbox">
      <h3>Prototype?&nbsp;&nbsp;</h3>
    </label>
    <input type="checkbox" id="checkbox" v-model="book.prototype.$model" />
    <br />

    <label for="checkbox">
      <h3>Publish?&nbsp;&nbsp;&nbsp;</h3>
    </label>
    <input type="checkbox" id="checkbox" v-model="book.publish.$model" />
    <br />
    <br />
  </div>
</template>
<script>
</script>
