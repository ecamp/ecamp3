<!--
Displays several tabs with details on a single camp (specified by the url in the prop).
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
            to="/"
            class="nav-item btn btn-sm btn-outline-secondary"><i class="zmdi zmdi-hc-fw zmdi-labels" /> Info</router-link>

          <router-link
            to="/collaborators"
            class="nav-item btn btn-sm btn-outline-secondary d-none d-sm-block disabled">
            <i class="zmdi zmdi-hc-fw zmdi-accounts" /> Collaborators
          </router-link>

          <router-link
            to="/periods"
            class="nav-item btn btn-sm btn-outline-secondary">
            <i class="zmdi zmdi-hc-fw zmdi-calendar-alt" /> Periods
          </router-link>

          <router-link
            to="/picasso"
            class="nav-item btn btn-sm btn-outline-secondary d-none d-sm-block disabled">
            <i class="zmdi zmdi-hc-fw zmdi-grid" /> Picasso
          </router-link>
        </div>
      </div>
    </div>
    <router-view />
  </section>
</template>

<script>
export default {
  name: 'CampDetailsContainer'
}
</script>

<style scoped>
</style>
