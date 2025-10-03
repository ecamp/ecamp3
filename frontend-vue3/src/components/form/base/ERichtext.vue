<template>
  <!--  <ValidationProvider-->
  <!--    v-slot="{ errors: veeErrors }"-->
  <!--    tag="div"-->
  <!--    :name="validationLabel"-->
  <!--    :vid="veeId"-->
  <!--    :rules="veeRules"-->
  <!--    :required="required"-->
  <!--    class="e-form-container"-->
  <!--  >-->
  <v-tiptap-editor
    :class="[inputClass]"
    :error-messages="veeErrors.concat(errorMessages)"
    :filled="filled"
    :hide-details="hideDetails"
    :label="labelOrEntityFieldLabel"
    :with-extensions="true"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <!-- passing through all slots -->
    <template v-for="(_, name) in $slots" #[name]>
      <slot :name="name" />
    </template>
    <template v-for="(_, name) in $slots" #[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </v-tiptap-editor>
  <!--  </ValidationProvider>-->
</template>

<script>
// import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import VTiptapEditor from '@/components/form/tiptap/VTiptapEditor.vue'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ERichtext',
  components: {
    VTiptapEditor,
    // ValidationProvider
  },
  mixins: [formComponentPropsMixin, formComponentMixin],
}
</script>
