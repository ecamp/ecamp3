<!--
Displays several tabs with details on a single camp.
-->

<template>
  <section class="container">
    <div
      class="row"
      style="margin-bottom: 10px">
      <div class="col-sm-12">
        <div
          class="nav nav-justified btn-group btn-block"
          role="group">
          <router-link
            :to="{ name: 'camp' }"
            class="nav-item btn btn-sm btn-outline-secondary">Info
          </router-link>
          <router-link
            :to="{ name: 'camp' }"
            class="nav-item btn btn-sm btn-outline-secondary d-none d-sm-block disabled">
            Collaborators
          </router-link>
          <router-link
            :to="{ name: 'camp/periods' }"
            class="nav-item btn btn-sm btn-outline-secondary">
            Periods
          </router-link>
          <router-link
            :to="{ name: 'camp' }"
            class="nav-item btn btn-sm btn-outline-secondary d-none d-sm-block disabled">
            Picasso
          </router-link>
        </div>
      </div>
    </div>
    <router-view :camp-id="this.$route.params.campId" />
  </section>
</template>

<script>
export default {
  name: 'CampDetails'
}
</script>

<style scoped>

</style>
