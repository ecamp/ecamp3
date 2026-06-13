import { Fixtures, test as base } from '@playwright/test'
import { runIdFixture } from '@/utils/fixtures/runId'

const fixtureObject = {
  ...runIdFixture,
}

const fixtures: Fixtures<{ fixtureObject: never }> = fixtureObject

export const test = base.extend<typeof fixtureObject>(fixtures)
