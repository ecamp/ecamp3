<template>
  <DialogBottomSheet
    v-if="$vuetify.breakpoint.smAndDown"
    :saving-override.sync="isSaving"
    :value="value"
    v-bind="{ ...$props, ...$attrs }"
    v-on="$listeners"
  >
    <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
    <template v-for="(_, name) in $scopedSlots" #[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </DialogBottomSheet>
  <DialogForm
    v-else
    content-class="ec-dialog-form"
    eager
    :saving-override.sync="isSaving"
    :value="value"
    v-bind="{ ...$props, ...$attrs }"
    v-on="$listeners"
  >
    <template v-for="(_, name) in $slots" #[name]>
      <slot :name="name" />
    </template>
    <template v-for="(_, name) in $scopedSlots" #[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </DialogForm>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBottomSheet from '@/components/dialog/DialogBottomSheet.vue'
import DialogUiBase from '@/components/dialog/DialogUiBase.vue'

export default {
  name: 'DetailEdit',
  components: { DialogBottomSheet, DialogForm },
  extends: DialogUiBase,
}
</script>
