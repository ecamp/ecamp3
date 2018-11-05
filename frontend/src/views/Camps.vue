<template>
  <section class="container">
    <h3>Camps</h3>
    <ul>
      <li
        v-for="campId in campIdList"
        :key="campId">
        <router-link
          :to="{ name: 'camp', params: { campId: campId } }">{{ campId }}</router-link>
      </li>
    </ul>
  </section>
</template>

<script>
export default {
  name: 'Camps',
  data () {
    return {
      campIdList: [ '1fb176e0', '77dd2792' ]
    }
  }
}
</script>

<style scoped>

</style>
