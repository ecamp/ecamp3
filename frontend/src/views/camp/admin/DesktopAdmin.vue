<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.admin.title')">
    <v-card-text>
      <v-row>
        <v-col cols="12" lg="6">
          <content-group :title="$tc('components.camp.campSettings.title')">
            <camp-settings :camp="camp" :disabled="!isManager" />
          </content-group>
          <camp-address :camp="camp" :disabled="!isManager" />

          <v-btn v-if="$vuetify.breakpoint.xsOnly" :to="{name: 'camp/collaborators', query: {isDetail: true}}">
            {{ $tc('views.camp.admin.collaborators') }}
          </v-btn>

          <content-group>
            <slot name="title">
              <div class="ec-content-group__title py-1 subtitle-1">
                {{ $tc('components.camp.campPeriods.title', api.get().camps().items.length) }}
                <dialog-period-create v-if="!disabled" :camp="camp()">
                  <template #activator="{ on }">
                    <button-add color="secondary" text
                                class="my-n2"
                                :hide-label="true"
                                v-on="on">
                      {{ $tc('components.camp.campPeriods.createPeriod') }}
                    </button-add>
                  </template>
                </dialog-period-create>
              </div>
            </slot>
            <camp-periods :camp="camp" :disabled="!isManager" />
          </content-group>
        </v-col>
        <v-col cols="12" lg="6">
          <content-group>
            <slot name="title">
              <div class="ec-content-group__title py-1 subtitle-1">
                {{ $tc('components.camp.campCategories.title') }}
                <dialog-category-create v-if="!isManager" :camp="camp()">
                  <template #activator="{ on }">
                    <button-add color="secondary" text
                                :hide-label="true"
                                class="my-n2"
                                v-on="on">
                      {{ $tc('components.camp.campCategories.create') }}
                    </button-add>
                  </template>
                </dialog-category-create>
              </div>
            </slot>
            <camp-categories :camp="camp" :disabled="!isManager" />
          </content-group>

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
            <camp-material-lists :camp="camp" :disabled="!isManager" />
          </content-group>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="6">
          <content-group :title="$tc('components.camp.campDangerzone.title')">
            <camp-danger-zone v-if="isManager" :camp="camp" />
          </content-group>
        </v-col>
      </v-row>
    </v-card-text>
  </content-card>
</template>

<script>
import CampSettings from '@/components/campAdmin/CampSettings.vue'
import CampAddress from '@/components/campAdmin/CampAddress.vue'
import CampPeriods from '@/components/campAdmin/CampPeriods.vue'
import CampMaterialLists from '@/components/campAdmin/CampMaterialLists.vue'
import CampCategories from '@/components/campAdmin/CampCategories.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import CampDangerZone from '@/components/campAdmin/CampDangerZone.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import DialogPeriodCreate from '@/components/campAdmin/DialogPeriodCreate.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import DialogMaterialListCreate from '@/components/campAdmin/DialogMaterialListCreate.vue'
import DialogCategoryCreate from '@/components/campAdmin/DialogCategoryCreate.vue'

export default {
  name: 'DesktopAdmin',
  components: {
    DialogCategoryCreate,
    DialogMaterialListCreate,
    ContentGroup,
    ButtonAdd,
    DialogPeriodCreate,
    CampDangerZone,
    ContentCard,
    CampSettings,
    CampAddress,
    CampPeriods,
    CampMaterialLists,
    CampCategories
  },
  mixins: [campRoleMixin],
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {}
  },
  mounted () {
    this.api.reload(this.camp())
    this.api.reload(this.camp().materialLists())
  }
}
</script>

<style scoped>
</style>
