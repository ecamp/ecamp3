<template>
  <content-group>
    <slot name="title">
      <div class="ec-content-group__title py-1 subtitle-1">
        {{ $tc('components.camp.campMaterialLists.title') }}
        <dialog-material-list-create :camp="camp()">
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
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-list>
      <camp-material-lists-item
        v-for="materialList in materialLists.items"
        :key="materialList.id"
        class="px-0"
        :material-list="materialList" />
    </v-list>
  </content-group>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd'
import CampMaterialListsItem from '@/components/camp/CampMaterialListsItem'
import DialogMaterialListCreate from '@/components/dialog/DialogMaterialListCreate'
import ContentGroup from '@/components/layout/ContentGroup'

export default {
  name: 'CampMaterialLists',
  components: { ContentGroup, ButtonAdd, CampMaterialListsItem, DialogMaterialListCreate },
  props: {
    camp: { type: Function, required: true }
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
