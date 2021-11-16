<template>
  <v-container fluid>
    <content-card title="Config">
      <v-card-text>
        <v-container class="pa-0">
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-checkbox v-model="stdConfig" label="default" hide-details="true" />
            </v-col>
            <v-col>
              <v-checkbox v-model="filled" label="filled" hide-details="true" />
            </v-col>
            <v-col>
              <v-checkbox v-model="outlined" label="outlined" hide-details="true" />
            </v-col>
            <v-col>
              <v-checkbox v-model="solo" label="solo" hide-details="true" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-text-field v-model="placeholder" label="Placeholder" />
            </v-col>
            <v-col>
              <v-checkbox v-model="persistentHint" label="persistent-hint" />
            </v-col>
            <v-col>
              <v-text-field v-model="hint" label="hint" />
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </content-card>

    <content-card title="Controls" class="mt-4">
      <v-card-text>
        <v-container>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              V-Text-Field
            </v-col>
            <v-col>
              E-Text-Field
            </v-col>
            <v-col>
              Api-Text-Field autosave
            </v-col>
            <v-col>
              Api-Text-Field
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-text-field
                v-model="textfieldValue"
                :placeholder="placeholder"
                v-bind="config" />
            </v-col>
            <v-col>
              <e-text-field
                v-model="textfieldValue"
                :placeholder="placeholder"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-text-field
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                :placeholder="placeholder"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-text-field
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                :placeholder="placeholder"
                :auto-save="false"
                v-bind="config" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              V-Textarea
            </v-col>
            <v-col>
              E-Textarea
            </v-col>
            <v-col>
              Api-Textarea, autosave
            </v-col>
            <v-col>
              Api-Textarea
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-textarea
                v-model="textareaValue"
                :placeholder="placeholder"
                :rows="3"
                auto-grow
                v-bind="config" />
            </v-col>
            <v-col>
              <e-textarea
                v-model="textareaValue"
                :placeholder="placeholder"
                :rows="3"
                auto-grow
                v-bind="config" />
            </v-col>
            <v-col>
              <api-textarea
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                :placeholder="placeholder"
                :rows="3"
                auto-grow
                v-bind="config" />
            </v-col>
            <v-col>
              <api-textarea
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                :placeholder="placeholder"
                :auto-save="false"
                :rows="3"
                auto-grow
                v-bind="config" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              V-Tiptap-Editor
            </v-col>
            <v-col>
              E-Richtext
            </v-col>
            <v-col>
              Api-Richtext, autosave
            </v-col>
            <v-col>
              Api-Richtext
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-tiptap-editor
                v-model="richtextValue"
                :placeholder="placeholder"
                v-bind="config" />
            </v-col>
            <v-col>
              <e-richtext
                v-model="richtextValue"
                :placeholder="placeholder"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-richtext
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                :placeholder="placeholder"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-richtext
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                :placeholder="placeholder"
                :auto-save="false"
                v-bind="config" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              V-Checkbox
            </v-col>
            <v-col>
              E-Checkbox
            </v-col>
            <v-col>
              Api-Checkbox, autosave
            </v-col>
            <v-col>
              Api-Checkbox
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-checkbox
                v-model="checkboxValue" />
            </v-col>
            <v-col>
              <e-checkbox
                v-model="checkboxValue" />
            </v-col>
            <v-col>
              <api-checkbox
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="isAdmin" />
            </v-col>
            <v-col>
              <api-checkbox
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="isAdmin"
                :auto-save="false" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              V-Switch
            </v-col>
            <v-col>
              E-Switch
            </v-col>
            <v-col>
              Api-Switch, autosave
            </v-col>
            <v-col>
              Api-Switch
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-switch
                v-model="checkboxValue" />
            </v-col>
            <v-col>
              <e-switch
                v-model="checkboxValue" />
            </v-col>
            <v-col>
              <api-switch
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="isAdmin" />
            </v-col>
            <v-col>
              <api-switch
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="isAdmin"
                :auto-save="false" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              V-Select
            </v-col>
            <v-col>
              E-Select
            </v-col>
            <v-col>
              Api-Select, autosave
            </v-col>
            <v-col>
              Api-Select
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              <v-select
                v-model="selectValue"
                :items="availableLocales"
                v-bind="config" />
            </v-col>
            <v-col>
              <e-select
                v-model="selectValue"
                :items="availableLocales"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-select
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="language"
                :items="availableLocales"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-select
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="language"
                :auto-save="false"
                :items="availableLocales"
                v-bind="config" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              Date
            </v-col>
            <v-col>
              E-Date-Picker
            </v-col>
            <v-col>
              Api-Date-Picker, autosave
            </v-col>
            <v-col>
              Api-Date-Picker
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              {{ dateValue }}
            </v-col>
            <v-col>
              <e-date-picker
                v-model="dateValue"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-date-picker
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="birthday"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-date-picker
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="birthday"
                :auto-save="false"
                v-bind="config" />
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              Time
            </v-col>
            <v-col>
              E-Time-Picker
            </v-col>
            <v-col>
              Api-Time-Picker, autosave
            </v-col>
            <v-col>
              Api-Time-Picker
            </v-col>
          </v-row>
          <v-row dense no-glutters justify="space-around">
            <v-col>
              {{ timeValue }}
            </v-col>
            <v-col>
              <e-time-picker
                v-model="timeValue"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-time-picker
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                v-bind="config" />
            </v-col>
            <v-col>
              <api-time-picker
                v-if="profileUri !== null"
                :uri="profileUri"
                fieldname="nickname"
                :auto-save="false"
                v-bind="config" />
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>

import ContentCard from '@/components/layout/ContentCard.vue'
import VTiptapEditor from '@/components/form/tiptap/VTiptapEditor.vue'
import ETextField from '@/components/form/base/ETextField.vue'
import ETextarea from '@/components/form/base/ETextarea.vue'
import ERichtext from '@/components/form/base/ERichtext.vue'
import ECheckbox from '@/components/form/base/ECheckbox.vue'
import ESwitch from '@/components/form/base/ESwitch.vue'
import ESelect from '@/components/form/base/ESelect.vue'
import EDatePicker from '@/components/form/base/EDatePicker.vue'
import ETimePicker from '@/components/form/base/ETimePicker.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiTextarea from '@/components/form/api/ApiTextarea.vue'
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import ApiCheckbox from '@/components/form/api/ApiCheckbox.vue'
import ApiSwitch from '@/components/form/api/ApiSwitch.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ApiDatePicker from '@/components/form/api/ApiDatePicker.vue'
import ApiTimePicker from '@/components/form/api/ApiTimePicker.vue'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'Controls',
  components: {
    ContentCard,
    VTiptapEditor,
    ETextField,
    ETextarea,
    ERichtext,
    ECheckbox,
    ESwitch,
    ESelect,
    EDatePicker,
    ETimePicker,
    ApiTextField,
    ApiTextarea,
    ApiRichtext,
    ApiCheckbox,
    ApiSwitch,
    ApiSelect,
    ApiDatePicker,
    ApiTimePicker
  },
  data: () => ({
    stdConfig: true,
    filled: null,
    outlined: null,
    solo: null,
    placeholder: 'Dummy placeholder',
    persistentHint: false,
    hint: 'Dummy hint',

    textfieldValue: 'FFFFFFFFFF',
    textareaValue: 'FFFFFFFFFF',
    richtextValue: '<p>FFFFFFFFFF</p>',
    checkboxValue: false,
    colorValue: '#FFFFFF',
    selectValue: null,
    dateValue: '2020-01-01',
    timeValue: '2020-01-01T14:45+00:00'
  }),
  computed: {
    profileUri () {
      return this.$auth.user().profile()._meta.self
    },
    availableLocales () {
      return VueI18n.availableLocales.map(l => ({
        value: l,
        text: this.$tc('global.language', 1, l)
      }))
    },
    config () {
      const c = {
        hint: this.hint,
        'persistent-hint': this.persistentHint
      }
      if (!this.stdConfig) {
        c.hint = this.hint
        c['persistent-hint'] = this.persistentHint
        c.solo = this.solo
        c.filled = this.filled
        c.outlined = this.outlined
      }
      return c
    }
  }
}

</script>

<style scoped>

</style>
