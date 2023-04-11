<template>
 </template>
 <script>
 export default{
    beforeCreate() {
        
    var returnTo = 'eng-index'
    if (localStorage.getItem("lastpage")){
        returnTo = localStorage.getItem("lastpage")
        localStorage.removeItem("lastpage")
    }
    this.$router.push({
        name:returnTo,
    })
  },
}
 
 </script>
 
  