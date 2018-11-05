<template>
  <div
    id="app"
    class="layout">
    <section class="layout-header container">
      <div
        class="btn-group float-right"
        style="margin-top: 7px">
        <router-link
          :to="{ name: 'home' }"
          class="btn btn-sm btn-primary">
          Home
        </router-link>
        <router-link
          :to="{ name: 'login' }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Login
        </router-link>
        <router-link
          :to="{ name: 'camps', params: { groupName: 'Pfadi Bewegung Schweiz' } }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Camps
        </router-link>
        <router-link
          :to="{ name: 'camp', params: { groupName: 'Pfadi Bewegung Schweiz', campId: '1fb176e0' } }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Single camp
        </router-link>
        <router-link
          :to="{ name: 'camp/periods', params: { groupName: 'Pfadi Bewegung Schweiz', campId: '1fb176e0' } }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Camp periods
        </router-link>
      </div>
      <h1>eCamp3</h1>
      <hr>
    </section>
    <router-view />
  </div>
</template>

<style lang="scss">
  @import '../node_modules/bootstrap/scss/bootstrap.scss';
</style>
