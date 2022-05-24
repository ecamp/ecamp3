<!--
Critical operations on camp
-->

<template>
  <content-group :title="$tc('components.camp.campDangerzone.title')">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <v-list>
        <v-list-item class="px-0" three-line>
          <v-list-item-content>
            <v-list-item-title>
              {{
                $tc('components.camp.campDangerzone.deleteCamp.title')
              }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{
                $tc('components.camp.campDangerzone.deleteCamp.description')
              }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <dialog-entity-delete
              :entity="camp()"
              :submit-enabled="promptText === camp().name"
              icon="mdi-bomb"
              @submit="$router.push({ name: 'camps' })">
              <template #activator="{ on }">
                <button-delete
                  icon="mdi-bomb"
                  :text="false"
                  dark
                  outlined
                  color="blue-grey"
                  @click.prevent="on.click">
                  {{ $tc('components.camp.campDangerzone.deleteCamp.action') }}
                </button-delete>
              </template>
              <p class="body-1">
                {{
                  $tc('components.camp.campDangerzone.deleteCamp.explanation', 0, {
                    campName: camp().name,
                  })
                }}
              </p>
              <label>
                {{
                  $tc('components.camp.campDangerzone.deleteCamp.label', 0, {
                    campName: camp().name,
                  })
                }}
                <e-text-field v-model="promptText" />
              </label>
            </dialog-entity-delete>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </div>
  </content-group>
</template>

<script>
import ContentGroup from '@/components/layout/ContentGroup.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import ETextField from '@/components/form/base/ETextField.vue'

export default {
  name: 'CampDangerZone',
  components: { ETextField, DialogEntityDelete, ButtonDelete, ContentGroup },
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      promptText: ''
    }
  }
}
</script>

<style scoped></style>
