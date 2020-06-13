<template>
  <div>
    <v-container fluid>
      <v-row no-gutters class="text-subtitle-2">
        <v-col cols="2">
          {{ $t('column1') }}
        </v-col>
        <v-col cols="7">
          {{ $t('column2') }}
        </v-col>
        <v-col cols="2">
          {{ $t('column3') }}
        </v-col>
        <v-col cols="1" />
      </v-row>
      <div v-for="section in activityContent.sections().items" :key="section._meta.self">
        <api-form :entity="section">
          <v-row dense>
            <v-col cols="2">
              <api-textarea
                fieldname="column1"
                auto-grow
                rows="2" />
            </v-col>
            <v-col cols="7">
              <api-textarea
                fieldname="column2"
                auto-grow
                rows="4" />
            </v-col>
            <v-col cols="2">
              <api-textarea
                fieldname="column3"
                auto-grow
                rows="2" />
            </v-col>
            <v-col cols="1">
              <div class="float-right section-buttons">
                <v-btn icon class="float-right"><v-icon>mdi-arrow-up-bold</v-icon></v-btn>
                <dialog-entity-delete :entity="section">
                  <template v-slot:activator="{ on }">
                    <button-delete icon-only
                                   class="float-right"
                                   v-on="on" />
                  </template>
                </dialog-entity-delete>
                <v-btn icon class="float-right" color="success"><v-icon>mdi-plus</v-icon></v-btn>
                <v-btn icon class="float-right"><v-icon>mdi-arrow-down-bold</v-icon></v-btn>
              </div>
            </v-col>
          </v-row>
        </api-form>
      </div>
    </v-container>
  </div>
</template>

<i18n>
{
  "en": {
    "column1": "When",
    "column2": "What",
    "column3": "Who"
  },
  "de": {
    "column1": "Zeit",
    "column2": "Programm / Ablauf",
    "column3": "Verantwortlich"
  }
}
</i18n>

<script>
import ApiTextarea from '@/components/form/api/ApiTextarea'
import ApiForm from '@/components/form/api/ApiForm'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import ButtonDelete from '@/components/buttons/ButtonDelete'

export default {
  name: 'Storyboard',
  components: {
    ApiForm,
    ApiTextarea,
    DialogEntityDelete,
    ButtonDelete
  },
  props: {
    activityContent: { type: Object, required: true }
  }
}
</script>

<style scoped>
.section-buttons{
  width:40px;
}
</style>
