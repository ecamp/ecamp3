<template>
  <DialogBottomSheet
    v-if="$vuetify.breakpoint.smAndDown"
    :saving-override.sync="isSaving"
    :value="value"
    v-bind="{ ...$props, ...$attrs }"
    v-on="$listeners"
  >
    <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
    <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </DialogBottomSheet>
  <DialogForm
    v-else
    :saving-override.sync="isSaving"
    content-class="ec-dialog-form"
    eager
    :value="value"
    v-bind="{ ...$props, ...$attrs }"
    v-on="$listeners"
  >
    <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
    <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
      <slot :name="name" v-bind="slotData" />
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
