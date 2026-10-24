import { test as base } from '@playwright/test'
import { runIdFixture, RunIdFixtureType } from '@/utils/fixtures/runId'
import {
  loginPageFixture,
  LoginPageFixtureType,
} from '@/utils/fixtures/pageObjects/loginPage'
import {
  camplistPageFixture,
  CampListPageFixtureType,
} from '@/utils/fixtures/pageObjects/campListPage'
import { campFixture, CampFixtureType } from '@/utils/fixtures/domainObjects/camp'

const fixtureObject = {
  ...runIdFixture,
  ...loginPageFixture,
  ...camplistPageFixture,
  ...campFixture,
}

export const test = base.extend<
  LoginPageFixtureType & CampListPageFixtureType & RunIdFixtureType & CampFixtureType
>(fixtureObject)
