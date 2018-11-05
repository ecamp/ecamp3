<template>
  <section class="container">
    <ul>
      <li><router-link :to="{ name: 'home' }">Home</router-link></li>
      <li><router-link :to="{ name: 'login' }">Login</router-link></li>
      <li><router-link :to="{ name: 'camps', params: { groupname: 'Pfadi Bewegung Schweiz' } }">Camp List</router-link></li>
      <li><router-link :to="{ name: 'camp/basic', params: { groupname: 'Pfadi Bewegung Schweiz', campname: 'Camp2' } }">Camp basic info</router-link></li>
      <li><router-link :to="{ name: 'camp/periods', params: { groupname: 'Pfadi Bewegung Schweiz', campname: 'Camp2' } }">Camp periods</router-link></li>
    </ul>
  </section>
</template>

<script>
export default {
  name: 'Home'
}
</script>
