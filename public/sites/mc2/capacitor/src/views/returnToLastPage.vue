<template>
 </template>
 <script>
 export default{
    beforeCreate() {
    var returnTo = '/index'
    if (localStorage.getItem("lastpage")){
        returnTo = localStorage.getItem("lastpage")
        localStorage.removeItem("lastpage")
    }
    this.$router.push({
        path:returnTo,
    })
  },
}
 
 </script>
 
  