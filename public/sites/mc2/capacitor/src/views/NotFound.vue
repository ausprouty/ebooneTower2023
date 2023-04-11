<template>
  <h1>Oops, it looks like the page you're looking for doesn't exist.</h1>
</template>
<script>
export default{
  beforeCreate() {
    var returnTo = 'eng-index'
    if (localStorage.getItem("lastpage")){
        localStorage.removeItem("lastpage")
    }
    this.$router.push({
        name:returnTo,
    })
  },
}
 
</script>
