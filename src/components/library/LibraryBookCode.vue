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
</script>
