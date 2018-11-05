<template>
  <div
    id="app"
    class="layout">
    <div class="layout-header">
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
          :to="{ name: 'camps', params: { groupname: 'Pfadi Bewegung Schweiz' } }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Camps
        </router-link>
        <router-link
          :to="{ name: 'camp/basic', params: { groupname: 'Pfadi Bewegung Schweiz', campname: 'Camp2' } }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Single camp
        </router-link>
        <router-link
          :to="{ name: 'camp/periods', params: { groupname: 'Pfadi Bewegung Schweiz', campname: 'Camp2' } }"
          class="btn btn-sm btn-primary d-none d-md-block">
          Camp periods
        </router-link>

      </div>
      <slot name="layout_title" />
    </div>
    <router-view />
  </div>
</template>

<style lang="scss">
</style>
