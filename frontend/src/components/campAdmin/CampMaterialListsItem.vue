<template>
  <v-list-item class="px-2 rounded" two-line v-on="$listeners">
    <v-list-item-content class="pt-0 pb-2">
      <v-list-item-title>{{ materialList.name }}</v-list-item-title>
      <v-list-item-subtitle>{{
        $tc(
          'components.campAdmin.campMaterialListsItem.materialsCount',
          materialList.materialItems().totalItems,
          {
            count: materialList.materialItems().totalItems,
          }
        )
      }}</v-list-item-subtitle>
    </v-list-item-content>

    <v-list-item-action v-if="!disabled" style="display: inline">
      <button-edit color="primary--text" text class="my-n1 v-btn--has-bg" />
    </v-list-item-action>

    <v-menu v-if="!disabled" offset-y>
      <template #activator="{ on, attrs }">
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-item-group>
          <v-list-item-action>
            <dialog-entity-delete
              :entity="materialList"
              :error-handler="deleteErrorHandler"
            >
              <template #activator="{ on }">
                <button-delete v-on="on" />
              </template>
              {{ $tc('components.campAdmin.campMaterialListsItem.deleteWarning') }} <br />
              <ul>
                <li>
                  {{ materialList.name }}
                </li>
              </ul>
            </dialog-entity-delete>
          </v-list-item-action>
        </v-item-group>
      </v-card>
    </v-menu>
  </v-list-item>
</template>

<script>
import DialogMaterialListEdit from './DialogMaterialListEdit.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'CampMaterialListsItem',
  components: { DialogEntityDelete, DialogMaterialListEdit, ButtonEdit, ButtonDelete },
  props: {
    materialList: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  methods: {
    deleteErrorHandler(e) {
      if (e?.response?.status === 422 /* Validation Error */) {
        return this.$tc('components.campAdmin.campMaterialListsItem.deleteError')
      }

      return null
    },
  },
}
</script>

<style scoped></style>
