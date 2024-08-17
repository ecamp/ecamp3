<!--
Critical operations on camp
-->

<template>
  <v-expansion-panel active-class="red lighten-5 red--text text--darken-4">
    <v-expansion-panel-header>
      <h2 class="subtitle-1 font-weight-bold">
        {{ $tc('components.campAdmin.campDangerZone.title') }}
      </h2>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <v-skeleton-loader v-if="camp._meta.loading" type="article" />
      <div v-else>
        <v-list class="py-0" color="transparent">
          <v-list-item class="px-0">
            <v-list-item-content>
              <v-list-item-title>
                {{ $tc('components.campAdmin.campDangerZone.deleteCamp.title') }}
              </v-list-item-title>
              <div class="body-2 grey--text text--darken-3">
                {{ $tc('components.campAdmin.campDangerZone.deleteCamp.description') }}
              </div>
            </v-list-item-content>
            <v-list-item-action>
              <dialog-entity-delete
                :entity="camp"
                :submit-enabled="promptText === camp.title"
                icon="mdi-bomb"
                @submit="$router.push({ name: 'camps' })"
              >
                <template #activator="{ on }">
                  <button-delete
                    icon="mdi-bomb"
                    :text="false"
                    dark
                    outlined
                    color="blue-grey"
                    @click.prevent="on.click"
                  >
                    {{ $tc('global.button.delete') }}
                  </button-delete>
                </template>
                <p class="body-1">
                  {{
                    $tc('components.campAdmin.campDangerZone.deleteCamp.explanation', 0, {
                      campTitle: camp.title,
                    })
                  }}
                </p>
                <label>
                  {{
                    $tc('components.campAdmin.campDangerZone.deleteCamp.label', 0, {
                      campTitle: camp.title,
                    })
                  }}
                  <e-text-field v-model="promptText" />
                </label>
              </dialog-entity-delete>
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </div>
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>

<script>
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import ETextField from '@/components/form/base/ETextField.vue'

export default {
  name: 'CampDangerZone',
  components: {
    ETextField,
    DialogEntityDelete,
    ButtonDelete,
  },
  props: {
    camp: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      promptText: '',
    }
  },
}
</script>

<style scoped></style>
