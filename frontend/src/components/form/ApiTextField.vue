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

        <v-text-field
          id="api-text-field"
          v-model="localValue"
          :label="label"
          name="api-text-field"
          class="mr-2 ml-2"
          :error-messages="errorMessage"
          :state="required && $v.localValue.$dirty ? !$v.localValue.$error : null"
          v-bind="$attrs"
          required
          @input="onInput"
          @blur="$v.localValue.$touch()">

          <template slot="append-outer">
            <v-btn
              v-if="!autoSave"
              small
              color="warning"
              class="mb-1"
              @click="reset">

              Reset
            </v-btn>

            <v-btn
              color="primary"
              small
              :disabled="isSaving || (required && $v.localValue.$invalid)"
              class="mr-2 ml-2 mb-1"
              @click="save">

              <v-progress-circular
                v-if="isSaving"
                indeterminate
                color="primary"
                size="20"
                class="mr-2" />

              <v-icon
                v-if="showSuccessIcon"
                left>
                mdi-check</v-icon>

              Save
            </v-btn>
          </template>
        </v-text-field>

      </v-form>
    </span>
  </span>
</template>

<script>
import { apiMixin } from '@/mixins/apiMixin'

export default {
  name: 'ApiTextField',
  mixins: [apiMixin],
  data () {
    return {}
  }
}
</script>

<style scoped>
  .dirty{
    border:1px red solid;
  }
</style>
