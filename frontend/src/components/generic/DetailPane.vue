<template>
  <DialogBottomSheet
    v-if="$vuetify.breakpoint.smAndDown"
    v-model:saving-override="isSaving"
    :value="value"
    v-bind="{ ...$props, ...$attrs }"
    v-on="$listeners"
  >
    <template v-for="(_, name) in $slots" #[name]>
      <slot :name="name" />
    </template>
  </DialogBottomSheet>
  <DialogForm
    v-else
    v-model:saving-override="isSaving"
    content-class="ec-dialog-form"
    eager
    :value="value"
    v-bind="{ ...$props, ...$attrs }"
    v-on="$listeners"
  >
    <template v-for="(_, name) in $slots" #[name]>
      <slot :name="name" />
    </template>
  </DialogForm>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBottomSheet from '@/components/dialog/DialogBottomSheet.vue'
import DialogUiBase from '@/components/dialog/DialogUiBase.vue'

export default {
  name: 'DetailPane',
  components: { DialogBottomSheet, DialogForm },
  extends: DialogUiBase,
}
</script>
