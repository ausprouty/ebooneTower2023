<template>
  <div>
    <div v-if="images">
      <div>
        <h3>Book Image:</h3>
        <div v-if="book.image.$model">
          <img v-bind:src="book.image.$model.image" class="book" />
          <br />
        </div>
        <v-select :options="images" label="title" v-model="book.image.$model">
          <template slot="option" slot-scope="option">
            <img class="select" :src="option.image" />

            <br />
            {{ option.title }}
          </template>
        </v-select>
      </div>
      <div v-if="image_permission">
        <label>
          Add new Image&nbsp;&nbsp;&nbsp;&nbsp;
          <input
            type="file"
            v-bind:id="book.code.$model"
            ref="image"
            v-on:change="handleImageUpload(book.code.$model)"
          />
        </label>
      </div>
    </div>
  </div>
</template>
<script>
</script>
