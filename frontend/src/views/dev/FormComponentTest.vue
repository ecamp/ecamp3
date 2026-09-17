<template>
  <v-container fluid class="form-component-test pa-6">
    <v-card class="pa-6">
      <h1 class="text-h6 mb-4" data-testid="form-test-component-name">
        {{ componentName || 'Form component test' }}
      </h1>

      <template v-if="resolvedComponent">
        <e-form>
          <div data-testid="form-test-subject">
            <component
              :is="resolvedComponent"
              ref="subject"
              v-model="model"
              v-bind="componentProps"
            />
          </div>
        </e-form>

        <v-divider class="my-4" />

        <div class="text-caption text-medium-emphasis">v-model value</div>
        <pre data-testid="form-test-model" class="form-test-model">{{ modelJson }}</pre>
      </template>

      <div v-else data-testid="form-test-unknown">
        <p v-if="componentName" class="text-error">
          Unknown form component "{{ componentName }}".
        </p>
        <p class="mb-2">Available components:</p>
        <ul>
          <li v-for="name in availableComponents" :key="name">
            <router-link :to="{ name: 'formTest', params: { component: name } }">
              {{ name }}
            </router-link>
          </li>
        </ul>
      </div>
    </v-card>
  </v-container>
</template>

<script>
import EForm from '@/components/form/base/EForm.vue'

const modules = import.meta.glob('../../components/form/base/E*.vue', {
  eager: true,
})
const registry = {}
for (const path in modules) {
  const name = path.split('/').pop().replace('.vue', '')
  registry[name] = modules[path].default
}

function parseJsonQuery(value, fallback) {
  if (value === undefined || value === null) return fallback
  try {
    return JSON.parse(value)
  } catch {
    return value
  }
}

export default {
  name: 'FormComponentTest',
  components: { EForm },
  data() {
    return {
      model: parseJsonQuery(this.$route.query.value, null),
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
      throw new Error(`Component ${name} not found`)
    },
    availableComponents() {
      return Object.keys(registry).sort()
    },
    componentProps() {
      return parseJsonQuery(this.$route.query.props, { name: 'test' })
    },
    modelJson() {
      try {
        return JSON.stringify(this.model)
      } catch {
        return String(this.model)
      }
    },
  },
  watch: {
    '$route.fullPath'() {
      this.model = parseJsonQuery(this.$route.query.value, null)
    },
  },
}
</script>

<style scoped>
.form-test-model {
  margin: 0;
  padding: 8px;
  background: rgba(0, 0, 0, 0.04);
  border-radius: 4px;
  white-space: pre-wrap;
  word-break: break-all;
}
</style>
