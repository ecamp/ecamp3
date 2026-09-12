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
            v-model="values[item.id]"
            v-bind="{ ...item.props, ...config }"
          />
          <span v-else v-text="values[item.id]" />
        </template>
        <template #[`item.e`]="{ item }">
          <component
            :is="item.component('e')"
            v-model="values[item.id]"
            v-bind="{ ...item.props, ...config }"
          />
        </template>
        <template #[`item.e-ro`]="{ item }">
          <component
            :is="item.component('e')"
            v-model="values[item.id]"
            v-bind="{ ...item.props, ...config }"
            :disabled="true"
            :readonly="true"
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
        <template #[`item.api-ro`]="{ item }">
          <component
            :is="item.component('api')"
            v-if="item.props.uri !== null"
            v-bind="{ ...item.props, ...config }"
            :auto-save="false"
            :disabled="true"
            :readonly="true"
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
import ETimeField from '@/components/form/base/ETimeField.vue'
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
import { VTextField, VTextarea, VCheckbox, VSwitch, VSelect } from 'vuetify/components'

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
    ETimeField,
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

    colorValue: null,

    values: {
      textfield: 'FFFFFFFFFF',
      textarea: 'FFFFFFFFFF',
      richtext: '<p>FFFFFFFFFF</p>',
      checkbox: false,
      switch: false,
      select: null,
      numberfield: 10,
      datepicker: '2020-01-01',
      timepicker: '2020-01-01T14:45:00+00:00',
      timefield: '05:00',
      colorpicker: '#FF9800',
      colorfield: '#229800',
    },

    headers: [
      { title: 'Type', value: 'id' },
      { title: 'v-input', value: 'v', sortable: false },
      { title: 'e-input', value: 'e', sortable: false },
      { title: 'e-input readonly', value: 'e-ro', sortable: false },
      { title: 'api-input', value: 'api', sortable: false },
      { title: 'api-input readonly', value: 'api-ro', sortable: false },
      { title: 'api-input.autosave', value: 'api.autosave', sortable: false },
    ],
  }),

  head: {
    title: 'Controls',
  },

  computed: {
    items() {
      return [
        {
          id: 'textfield',
          component: (type) => `${type}-text-field`,
          props: {
            placeholder: this.placeholder,
            path: 'text',
            uri: this.formTestData,
            veeRules: 'required',
          },
        },
        {
          id: 'numberfield',
          component: (type) => (type === 'v' ? '' : `${type}-number-field`),
          props: {
            placeholder: this.placeholder,
            inputmode: 'decimal',
            path: 'number',
            uri: this.formTestData,
          },
        },
        {
          id: 'textarea',
          component: (type) => `${type}-textarea`,
          props: {
            placeholder: this.placeholder,
            rows: 3,
            path: 'html',
            uri: this.formTestData,
          },
        },
        {
          id: 'richtext',
          component: (type) => (type === 'v' ? 'v-tiptap-editor' : `${type}-richtext`),
          props: {
            placeholder: this.placeholder,
            rows: 3,
            path: 'multilineText',
            uri: this.formTestData,
          },
        },
        {
          id: 'select',
          component: (type) => `${type}-select`,
          props: {
            path: 'language',
            placeholder: this.placeholder,
            items: this.availableLocales,
            uri: this.formTestData,
            itemTitle: 'text',
            itemValue: 'value',
            veeRules: 'required',
          },
        },
        {
          id: 'checkbox',
          component: (type) => `${type}-checkbox`,
          props: {
            path: 'flag',
            uri: this.formTestData,
          },
        },
        {
          id: 'switch',
          component: (type) => `${type}-switch`,
          props: {
            path: 'flag',
            uri: this.formTestData,
          },
        },
        {
          id: 'datepicker',
          component: (type) => (type === 'v' ? '' : `${type}-date-picker`),
          props: {
            placeholder: this.placeholder,
            path: 'date',
            uri: this.formTestData,
          },
        },
        {
          id: 'timepicker',
          component: (type) => (type === 'v' ? '' : `${type}-time-picker`),
          props: {
            placeholder: this.placeholder,
            'value-format': 'YYYY-MM-DDTHH:mm:ssZ',
            path: 'time',
            uri: this.formTestData,
          },
        },
        {
          id: 'timefield',
          component: (type) => (type === 'e' ? `${type}-time-field` : ''),
          props: {
            placeholder: this.placeholder,
            path: 'time',
            uri: this.formTestData,
          },
        },
        {
          id: 'colorpicker',
          component: (type) => (type === 'v' ? '' : `${type}-color-picker`),
          props: {
            placeholder: this.placeholder,
            path: 'color',
            uri: this.formTestData,
            veeRules: 'required',
          },
        },
        {
          id: 'colorfield',
          component: (type) => (type !== 'v' ? `${type}-color-field` : ''),
          props: {
            placeholder: this.placeholder,
            path: 'color',
            uri: this.formTestData,
            veeRules: 'required',
          },
        },
      ]
    },
    formTestData() {
      return this.api.get().formTestData({ id: '0123456789ab' })
    },
    availableLocales() {
      return VueI18n.global.availableLocales.map((l) => ({
        value: l,
        text: this.$t('global.language', 1, { locale: l }),
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
