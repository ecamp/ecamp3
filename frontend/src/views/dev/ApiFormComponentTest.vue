<template>
  <v-container fluid class="api-form-component-test pa-6">
    <h1 class="text-h6 mb-4" data-testid="api-form-test-component-name">
      {{ componentName || 'API form component test' }}
    </h1>

    <template v-if="resolvedComponent">
      <template v-if="entity && !entity._meta.loading">
        <api-form :entity="entity">
          <div data-testid="api-form-test-subject">
            <component :is="resolvedComponent" :path="path" v-bind="extraProps" />
          </div>
        </api-form>

        <v-divider class="my-4" />

        <div class="text-caption text-medium-emphasis">
          persisted value of "{{ path }}"
        </div>
        <pre data-testid="api-form-test-value" class="api-form-test-value">{{
          valueJson
        }}</pre>
        <v-btn
          size="small"
          variant="tonal"
          data-testid="api-form-test-reload"
          :loading="reloading"
          @click="reload"
        >
          Reload from API
        </v-btn>
      </template>
      <div v-else data-testid="api-form-test-loading">Loading…</div>
    </template>

    <div v-else data-testid="api-form-test-unknown">
      <p v-if="componentName" class="text-error">
        Unknown API form component "{{ componentName }}".
      </p>
      <p class="mb-2">Available components:</p>
      <ul>
        <li v-for="name in availableComponents" :key="name">
          <router-link :to="{ name: 'apiFormTest', params: { component: name } }">
            {{ name }}
          </router-link>
        </li>
      </ul>
    </div>
  </v-container>
</template>

<script>
import ApiForm from '@/components/form/api/ApiForm.vue'

const EXCLUDED = ['ApiForm', 'ApiWrapper', 'ApiWrapperAppend', 'ApiSortable']
const modules = import.meta.glob('../../components/form/api/Api*.vue', {
  eager: true,
})
const registry = {}
for (const path in modules) {
  const name = path.split('/').pop().replace('.vue', '')
  if (!EXCLUDED.includes(name)) {
    registry[name] = modules[path].default
  }
}

const SINGLETON_COLLECTION = '/form_test_data'

export default {
  name: 'ApiFormComponentTest',
  components: { ApiForm },
  data() {
    return {
      uri: null,
      reloading: false,
    }
  },
  computed: {
    componentName() {
      return this.$route.params.component ?? ''
    },
    resolvedComponent() {
      const name = this.componentName
      if (!name) return null
      if (registry[name]) return registry[name]
      const normalized = name.replace(/-/g, '').toLowerCase()
      const key = Object.keys(registry).find((k) => k.toLowerCase() === normalized)
      return key ? registry[key] : null
    },
    availableComponents() {
      return Object.keys(registry).sort()
    },
    path() {
      return this.$route.query.path ?? 'text'
    },
    extraProps() {
      const raw = this.$route.query.props
      if (!raw) return {}
      try {
        return JSON.parse(raw)
      } catch {
        return {}
      }
    },
    entity() {
      return this.uri ? this.api.get(this.uri) : null
    },
    valueJson() {
      if (!this.entity) return ''
      try {
        return JSON.stringify(this.entity[this.path])
      } catch {
        return String(this.entity[this.path])
      }
    },
  },
  async created() {
    const collection = await this.api.get(SINGLETON_COLLECTION)._meta.load
    this.uri = collection.items[0]._meta.self
    await this.api.get(this.uri)._meta.load
  },
  methods: {
    async reload() {
      if (!this.uri) return
      this.reloading = true
      try {
        await this.api.reload(this.api.get(this.uri))
      } finally {
        this.reloading = false
      }
    },
  },
}
</script>

<style scoped>
.api-form-test-value {
  margin: 0 0 8px;
  padding: 8px;
  background: rgba(0, 0, 0, 0.04);
  border-radius: 4px;
  white-space: pre-wrap;
  word-break: break-all;
}
</style>
