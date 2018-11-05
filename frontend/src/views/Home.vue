<template>
  <ul>
    <li><router-link :to="{ name: 'login' }">Login</router-link></li>
    <li><router-link :to="{ name: 'camps' }">Camp List</router-link></li>
    <li><router-link :to="{ name: 'camp/basic' }">Camp basic info</router-link></li>
    <li><router-link :to="{ name: 'camp/periods' }">Camp periods</router-link></li>
  </ul>
</template>

<script>
export default {
  name: 'Home'
}
</script>
