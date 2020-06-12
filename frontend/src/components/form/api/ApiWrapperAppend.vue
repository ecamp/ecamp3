<template>
  <div class="d-flex">
    <!-- Retry/Cancel button if saving failed -->
    <template v-if="wrapper.autoSave && wrapper.hasServerError">
      <button-retry type="submit" @click="wrapper.on.save" />

      <v-tooltip bottom class="ml-auto">
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            color="grey"
            v-on="on"
            @click="wrapper.on.reset">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </template>
        <span>Cancel</span>
      </v-tooltip>
    </template>

    <!-- Retry button if loading failed -->
    <button-retry v-if="wrapper.hasLoadingError" type="submit" @click="wrapper.on.reload" />

    <!-- Success icon after saving -->
    <icon-success :visible="wrapper.status === 'success'" />
  </div>
</template>

<script>
import IconSuccess from './IconSuccess'
import ButtonRetry from './ButtonRetry'

export default {
  name: 'ApiWrapperAppend',
  components: { IconSuccess, ButtonRetry },
  props: {
    wrapper: {
      required: true,
      type: Object
    }
  }
}

</script>

<style scoped>
.v-btn {
    position:relative;
    top:-5px;
}
</style>
