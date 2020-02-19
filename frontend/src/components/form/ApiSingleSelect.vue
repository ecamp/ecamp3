<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <span>
    <span v-if="!editing">{{ value }}</span>
    <span v-if="editing">
      <v-form
        inline
        class="mb-2">

        <v-select
          id="api-single-select"
          v-model="localValue"
          :label="label"
          name="api-single-select"
          class="mr-2 ml-2"
          :error-messages="errorMessage"
          v-bind="$attrs"
          :items="['member', 'manager']"
          @input="onInput"
          @blur="$v.localValue.$touch()">

          <v-icon
            slot="append"
            color="green" />
          <v-icon
            v-if="showSuccessIcon"
            slot="append"
            color="green">mdi-check</v-icon>
          <v-progress-circular
            v-if="isSaving"
            slot="append"
            indeterminate
            color="primary"
            size="20"
            class="mr-2" />
        </v-select>
      </v-form>
    </span>
  </span>
</template>

<script>
import { apiMixin } from '@/mixins/apiMixin'

export default {
  name: 'ApiSingleSelect',
  mixins: [apiMixin],
  data () {
    return {
    }
  },

  methods: {

    save: function (event) {
      this.$v.localValue.$touch()
      if (this.required && this.$v.localValue.$anyError) {
        return
      }

      this.isSaving = true
      this.api.patch(this.uri, { [this.fieldname]: this.localValue }).then(() => {
        this.isSaving = false
        this.$v.localValue.$reset()

        this.showSuccessIcon = true
        setTimeout(() => { this.showSuccessIcon = false }, 2000)
      }, (e, a) => {
        console.log('error')
      })
    }
  }
}
</script>

<style scoped>
  .dirty{
    border:1px red solid;
  }
</style>
