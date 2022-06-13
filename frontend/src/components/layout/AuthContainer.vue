<template>
  <v-container class="sso--container fill-height justify-center align-center" fluid>
    <v-col
      cols="12"
      sm="8"
      md="5"
      lg="4"
      class="pa-0 pa-sm-4 pb-sm-12"
      :class="{ 'fill-height': $vuetify.breakpoint.xsOnly }"
    >
      <v-card
        class="pa-8 sso--card"
        :tile="$vuetify.breakpoint.xsOnly"
        :class="{ 'fill-min-height': $vuetify.breakpoint.xsOnly }"
      >
        <slot />
      </v-card>
    </v-col>
    <span v-if="$vuetify.breakpoint.smAndUp" class="photo_credits ma-3 pa-1">
      {{ $tc('components.layout.authContainer.photoCredits') }}
    </span>
  </v-container>
</template>

<script>
export default {
  name: 'AuthContainer',
}
</script>

<style lang="scss" scoped>
.photo_credits {
  position: absolute;
  bottom: 0;
  right: 0;
  background: #000;
  color: #fff;
  font-size: 10px;
  max-width: calc(34vw / 2);
  text-decoration: none;
}
.sso--container {
  background-image: url('~@/assets/080720_lotos_7896_small.jpg');
  background-size: cover;
  overflow: auto;
}
.sso--card {
  z-index: 3;
}
.fill-min-height {
  min-height: 100%;
}
</style>
