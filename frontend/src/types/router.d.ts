import 'vue-router'
import type { RouteLocationRaw } from 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    /**
     * Target route (name/path/object) or boolean for the back button.
     * When set, shows a back button in ContentCard.
     */
    back?: RouteLocationRaw | boolean

    /**
     * When true, shows the back button only on mobile devices (e.g. for master-detail views).
     */
    backMobile?: boolean
  }
}
