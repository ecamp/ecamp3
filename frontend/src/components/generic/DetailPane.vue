<template>
  <DialogBottomSheet
    v-if="$vuetify.display.smAndDown"
    :saving-override.sync="isSaving"
    :model-value
    v-bind="$props"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <!-- passing through all slots -->
    <template v-for="(_, slot) of $slots" #[slot]="slotData">
      <slot :name="slot" v-bind="slotData || {}"></slot>
    </template>
  </DialogBottomSheet>
  <DialogForm
    v-else
    :saving-override.sync="isSaving"
    content-class="ec-dialog-form"
    eager
    :model-value
    v-bind="$props"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <!-- passing through all slots -->
    <template v-for="(_, slot) of $slots" #[slot]="slotData">
      <slot :name="slot" v-bind="slotData || {}"></slot>
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
  emits: ['update:modelValue'],
}
</script>
