<template>
  <section class="container">
    <ul>
      <li><router-link :to="{ name: 'home' }">Home</router-link></li>
      <li><router-link :to="{ name: 'login' }">Login</router-link></li>
      <li><router-link :to="{ name: 'camps', params: { groupName: 'Pfadi Bewegung Schweiz' } }">Camp List</router-link></li>
      <li><router-link :to="{ name: 'camp', params: { groupName: 'Pfadi Bewegung Schweiz', campId: '1fb176e0' } }">Camp basic info</router-link></li>
      <li><router-link :to="{ name: 'camp/periods', params: { groupName: 'Pfadi Bewegung Schweiz', campId: '1fb176e0' } }">Camp periods</router-link></li>
    </ul>
  </section>
</template>

<script>
export default {
  name: 'Home'
}
</script>
