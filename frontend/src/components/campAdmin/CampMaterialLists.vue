<template>
  <content-group>
    <slot name="title">
      <div class="ec-content-group__title py-1 subtitle-1">
        {{ $tc('components.camp.campMaterialLists.title') }}
        <dialog-material-list-create v-if="!disabled" :camp="camp()">
          <template #activator="{ on }">
            <button-add color="secondary" text
                        class="my-n1"
                        v-on="on">
              {{ $tc('components.camp.campMaterialLists.createMaterialList') }}
            </button-add>
          </template>
        </dialog-material-list-create>
      </div>
    </slot>
    <v-skeleton-loader v-if="camp().materialLists()._meta.loading" type="article" />
    <v-list>
      <camp-material-lists-item
        v-for="materialList in materialLists.items"
        :key="materialList._meta.self"
        class="px-0"
        :material-list="materialList"
        :disabled="disabled" />
    </v-list>
  </content-group>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import CampMaterialListsItem from '@/components/campAdmin/CampMaterialListsItem.vue'
import DialogMaterialListCreate from './DialogMaterialListCreate.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'

export default {
  name: 'CampMaterialLists',
  components: { ContentGroup, ButtonAdd, CampMaterialListsItem, DialogMaterialListCreate },
  props: {
    camp: { type: Function, required: true },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
    }
  },
  computed: {
    materialLists () {
      return this.camp().materialLists()
    }
  }
}
</script>

<style scoped>
</style>
