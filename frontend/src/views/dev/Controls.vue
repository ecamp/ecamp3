<template>
  <v-container fluid>
    <content-card title="Controls" toolbar>
      <v-card-text>
        <div class="d-flex gap-2">
          <div class="d-grid flex-grow-1">
            <v-text-field v-model="hint" label="Hint" hide-details />
            <v-checkbox v-model="persistentHint" label="persistent-hint" class="mt-0" />
          </div>
          <div class="d-grid flex-grow-1">
            <v-text-field v-model="labelText" label="Label" hide-details />
            <v-checkbox v-model="label" label="label" class="mt-0" />
          </div>
          <div class="d-flex flex-grow-1">
            <v-text-field v-model="placeholder" label="Placeholder" />
          </div>
        </div>
      </v-card-text>
      <v-divider />
      <v-card-title>
        <h2>Input Table</h2>
      </v-card-title>
      <v-data-table
        :headers="headers"
        :items="items"
        disable-pagination
        hide-default-footer
        :items-per-page="-1"
      >
        <template #[`item.id`]="{ item }">{{ item.id }}</template>
        <template #[`item.v`]="{ item }">
          <component
            :is="item.component('v')"
            v-if="item.component('v') !== ''"
            v-model="item.value"
            v-bind="{ ...item.props, ...config }"
          />
          <span v-else v-text="item.value" />
        </template>
        <template #[`item.e`]="{ item }">
          <component
            :is="item.component('e')"
            v-model="item.value"
            v-bind="{ ...item.props, ...config }"
          />
        </template>
        <template #[`item.api`]="{ item }">
          <component
            :is="item.component('api')"
            v-if="item.props.uri !== null"
            v-bind="{ ...item.props, ...config }"
            :auto-save="false"
          />
        </template>
        <template #[`item.api.autosave`]="{ item }">
          <component
            :is="item.component('api')"
            v-if="item.props.uri !== null"
            v-bind="{ ...item.props, ...config }"
          />
        </template>
      </v-data-table>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import VTiptapEditor from '@/components/form/tiptap/VTiptapEditor.vue'
import ETextField from '@/components/form/base/ETextField.vue'
import ENumberField from '@/components/form/base/ENumberField.vue'
import ETextarea from '@/components/form/base/ETextarea.vue'
import ERichtext from '@/components/form/base/ERichtext.vue'
import ECheckbox from '@/components/form/base/ECheckbox.vue'
import ESwitch from '@/components/form/base/ESwitch.vue'
import ESelect from '@/components/form/base/ESelect.vue'
import EDatePicker from '@/components/form/base/EDatePicker.vue'
import ETimePicker from '@/components/form/base/ETimePicker.vue'
import EColorPicker from '@/components/form/base/EColorPicker.vue'
import EColorField from '@/components/form/base/EColorField.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiNumberField from '@/components/form/api/ApiNumberField.vue'
import ApiTextarea from '@/components/form/api/ApiTextarea.vue'
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import ApiCheckbox from '@/components/form/api/ApiCheckbox.vue'
import ApiSwitch from '@/components/form/api/ApiSwitch.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ApiColorField from '@/components/form/api/ApiColorField.vue'
import ApiDatePicker from '@/components/form/api/ApiDatePicker.vue'
import ApiTimePicker from '@/components/form/api/ApiTimePicker.vue'
import ApiColorPicker from '@/components/form/api/ApiColorPicker.vue'
import VueI18n from '@/plugins/i18n'
import { VTextField, VTextarea, VCheckbox, VSwitch, VSelect } from 'vuetify/lib'

export default {
  name: 'Controls',
  components: {
    ContentCard,
    VTextField,
    ETextField,
    ApiTextField,
    ENumberField,
    ApiNumberField,
    VTextarea,
    ETextarea,
    ApiTextarea,
    VTiptapEditor,
    ERichtext,
    ApiRichtext,
    VCheckbox,
    ECheckbox,
    ApiCheckbox,
    VSwitch,
    ESwitch,
    ApiSwitch,
    VSelect,
    ESelect,
    ApiSelect,
    EDatePicker,
    ApiDatePicker,
    ETimePicker,
    ApiTimePicker,
    EColorPicker,
    EColorField,
    ApiColorPicker,
    ApiColorField,
  },
  data: () => ({
    placeholder: 'Dummy placeholder',
    persistentHint: false,
    hint: 'Dummy hint',
    label: false,
    labelText: 'Label',

    textfieldValue: 'FFFFFFFFFF',
    numberfieldValue: 10,
    textareaValue: 'FFFFFFFFFF',
    richtextValue: '<p>FFFFFFFFFF</p>',
    checkboxValue: false,
    colorValue: null,
    selectValue: null,
    dateValue: '2020-01-01',
    timeValue: '2020-01-01T14:45:00+00:00',

    headers: [
      { text: 'Type', value: 'id' },
      { text: 'v-input', value: 'v', sortable: false },
      { text: 'e-input', value: 'e', sortable: false },
      { text: 'api-input', value: 'api', sortable: false },
      { text: 'api-input.autosave', value: 'api.autosave', sortable: false },
    ],
  }),
  computed: {
    items() {
      return [
        {
          id: 'text-field',
          component: (type) => `${type}-text-field`,
          value: this.textfieldValue,
          props: {
            placeholder: this.placeholder,
            path: 'nickname',
            uri: this.profileUri,
          },
        },
        {
          id: 'number-field',
          component: (type) => (type === 'v' ? '' : `${type}-number-field`),
          value: this.numberfieldValue,
          props: {
            placeholder: this.placeholder,
            inputmode: 'decimal',
            path: 'quantity',
            uri: this.materialUri,
          },
        },
        {
          id: 'textarea',
          component: (type) => `${type}-textarea`,
          value: this.textareaValue,
          props: {
            placeholder: this.placeholder,
            rows: 3,
            path: 'data.html',
            uri: this.singleTextUri,
          },
        },
        {
          id: 'richtext',
          component: (type) => (type === 'v' ? 'v-tiptap-editor' : `${type}-richtext`),
          value: this.richtextValue,
          props: {
            placeholder: this.placeholder,
            rows: 3,
            path: 'data.html',
            uri: this.singleTextUri,
          },
        },
        {
          id: 'select',
          component: (type) => `${type}-select`,
          value: this.selectValue,
          props: {
            path: 'language',
            placeholder: this.placeholder,
            items: this.availableLocales,
            uri: this.profileUri,
          },
        },
        {
          id: 'checkbox',
          component: (type) => `${type}-checkbox`,
          value: this.checkboxValue,
          props: {
            path: 'printYSLogoOnPicasso',
            uri: this.campUri,
          },
        },
        {
          id: 'switch',
          component: (type) => `${type}-switch`,
          value: this.checkboxValue,
          props: {
            path: 'printYSLogoOnPicasso',
            uri: this.campUri,
          },
        },
        {
          id: 'date-picker',
          component: (type) => (type === 'v' ? '' : `${type}-date-picker`),
          value: this.dateValue,
          props: {
            placeholder: this.placeholder,
            path: 'start',
            uri: this.periodUri,
          },
        },
        {
          id: 'time-picker',
          component: (type) => (type === 'v' ? '' : `${type}-time-picker`),
          value: this.timeValue,
          props: {
            placeholder: this.placeholder,
            'value-format': 'YYYY-MM-DDTHH:mm:ssZ',
            path: 'start',
            uri: this.scheduleEntryUri,
          },
        },
        {
          id: 'color-picker',
          component: (type) => (type === 'v' ? '' : `${type}-color-picker`),
          value: this.colorValue,
          props: {
            placeholder: this.placeholder,
            path: 'color',
            uri: this.categoryUri,
            veeRules: 'required',
          },
        },
        {
          id: 'color-field',
          component: (type) => (type !== 'v' ? `${type}-color-field` : ''),
          value: this.colorValue,
          props: {
            placeholder: this.placeholder,
            path: 'color',
            uri: this.campCollaborationUri,
            veeRules: 'required',
          },
        },
      ]
    },
    profileUri() {
      return this.$store.state.auth.user?.profile()._meta.self ?? null
    },
    campUri() {
      return '/api/camps/6973c230d6b1' // Harry Potter - Lager
    },
    periodUri() {
      return '/api/periods/fe47dfd2b541' // Harry Potter - Hauptlager
    },
    categoryUri() {
      return '/api/categories/e7559fc16388' // Harry Potter - Lageraktivität
    },
    materialUri() {
      return '/api/material_items/04be1b6159dc' // Harry Potter- LA Lagerbau - Schatztruhe
    },
    singleTextUri() {
      return '/api/content_node/single_texts/d5c2ece2bedf' // Harry Potter - LA Lagerbau - Roter Faden
    },
    scheduleEntryUri() {
      return '/api/schedule_entries/b6668dffbb2b' // Harry Potter - LA Lagerbau
    },
    campCollaborationUri() {
      return '/camp_collaborations/3229d273decd' // Harry Potter - Snoopy
    },
    availableLocales() {
      return VueI18n.availableLocales.map((l) => ({
        value: l,
        text: this.$tc('global.language', 1, l),
      }))
    },
    config() {
      return {
        hint: this.hint,
        'persistent-hint': this.persistentHint,
        label: this.label ? this.labelText : undefined,
      }
    },
  },
}
</script>

<style scoped></style>
