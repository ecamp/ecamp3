import { HalLinks } from '@/shared-types/hal'

export interface CampItem {
  motto?: string
  title: string
  _links: HalLinks
}

export interface PeriodItem {
  _links: { self: { href: string } }
}
