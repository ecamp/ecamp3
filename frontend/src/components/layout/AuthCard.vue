<template>
  <v-container class="sso--container fill-height justify-center align-center" fluid>
    <v-col cols="12" sm="8"
           md="5" lg="4"
           class="pa-0 pa-sm-4"
           :class="{'fill-height': $vuetify.breakpoint.xsOnly}">
      <v-card class="pa-8"
              :tile="$vuetify.breakpoint.xsOnly"
              :class="{'fill-min-height': $vuetify.breakpoint.xsOnly}">
        <slot />
      </v-card>
    </v-col>
    <span v-if="$vuetify.breakpoint.smAndUp" class="photo_credits">
      Photo by Markus Rohner / Lotos
    </span>
  </v-container>
</template>

<script>
export default {
  name: 'AuthCard'
}
</script>

<style scoped>
  .photo_credits {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #000;
    color: #fff;
    font-size: 10px;
    padding: 4px;
    text-decoration: none;
  }
  .sso--container {
    background-image: url("~@/assets/080720_lotos_7896_small.jpg");
    background-size: cover;
  }
  .fill-min-height{
    min-height: 100%;
  }

</style>
