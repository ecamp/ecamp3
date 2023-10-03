<template>
  <v-container fluid>
    <content-card title="Controls" toolbar>
      <v-card-text>
        <div class="d-flex gap-2">
          <div class="d-grid flex-grow-1">
            <v-text-field v-model="hint" label="Hint" hide-details />
            <v-checkbox v-model="persistentHint" label="persistent-hint" class="mt-0" />
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
            v-bind="{ ...item.props, ...config }"
            v-model="item.value"
          />
          <span v-else v-text="item.value" />
        </template>
        <template #[`item.e`]="{ item }">
          <component
            :is="item.component('e')"
            v-bind="{ ...item.props, ...config }"
            v-model="item.value"
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
import ETextarea from '@/components/form/base/ETextarea.vue'
import ERichtext from '@/components/form/base/ERichtext.vue'
import ECheckbox from '@/components/form/base/ECheckbox.vue'
import ESwitch from '@/components/form/base/ESwitch.vue'
import ESelect from '@/components/form/base/ESelect.vue'
import EDatePicker from '@/components/form/base/EDatePicker.vue'
import ETimePicker from '@/components/form/base/ETimePicker.vue'
import EColorPicker from '@/components/form/base/EColorPicker.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiTextarea from '@/components/form/api/ApiTextarea.vue'
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import ApiCheckbox from '@/components/form/api/ApiCheckbox.vue'
import ApiSwitch from '@/components/form/api/ApiSwitch.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
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
    ApiColorPicker,
  },
  data: () => ({
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
            fieldname: 'nickname',
            uri: this.profileUri,
          },
        },
        {
          id: 'text-field.numeric',
          component: (type) => `${type}-text-field`,
          props: {
            'v-model.number': this.textfieldValue,
            placeholder: this.placeholder,
            inputmode: 'numeric',
            fieldname: 'quantity',
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
            fieldname: 'data.html',
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
            fieldname: 'data.html',
            uri: this.singleTextUri,
          },
        },
        {
          id: 'select',
          component: (type) => `${type}-select`,
          value: this.selectValue,
          props: {
            fieldname: 'language',
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
            fieldname: 'printYSLogoOnPicasso',
            uri: this.campUri,
          },
        },
        {
          id: 'switch',
          component: (type) => `${type}-switch`,
          value: this.checkboxValue,
          props: {
            fieldname: 'printYSLogoOnPicasso',
            uri: this.campUri,
          },
        },
        {
          id: 'date-picker',
          component: (type) => (type === 'v' ? '' : `${type}-date-picker`),
          value: this.dateValue,
          props: {
            placeholder: this.placeholder,
            fieldname: 'start',
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
            fieldname: 'start',
            uri: this.scheduleEntryUri,
          },
        },
        {
          id: 'color-picker',
          component: (type) => (type === 'v' ? '' : `${type}-color-picker`),
          value: this.colorValue,
          props: {
            placeholder: this.placeholder,
            fieldname: 'color',
            uri: this.categoryUri,
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
      }
    },
  },
}
</script>

<style scoped></style>
