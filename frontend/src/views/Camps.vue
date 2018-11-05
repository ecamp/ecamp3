<template>
  <section class="container">
    <h3>Camps</h3>
    <router-link
      v-for="campName in campNameList"
      :key="campName"
      :to="{ name: 'camp/basic', params: { groupname: 'Pfadi Bewegung Schweiz', campname: campName } }">{{ campName }}</router-link>
  </section>
</template>

<script>
export default {
  name: 'Camps',
  data () {
    return {
      campNameList: [ 'Camp1', 'Camp2' ]
    }
  }
}
</script>

<style scoped>

</style>
