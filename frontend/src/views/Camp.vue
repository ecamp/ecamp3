<!--
Displays several tabs with details on a single camp.
-->

<template>
  <b-card class="m-3">
    <div
      slot="header"
      class="row">
      <div class="col-sm-12">
        <div
          class="nav nav-justified btn-group btn-block"
          role="group">
          <router-link
            :to="{ name: 'camp' }"
            class="nav-item btn btn-sm btn-outline-secondary">
            Info
          </router-link>
          <router-link
            :to="{ name: 'camp/collaborators' }"
            class="nav-item btn btn-sm btn-outline-secondary">
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
    <router-view :camp-uri="this.$route.params.campUri" />
  </b-card>
</template>

<script>
export default {
  name: 'CampDetails'
}
</script>

<style scoped>

</style>
