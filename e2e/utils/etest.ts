import { test as base } from '@playwright/test'
import { runIdFixture, RunIdFixtureType } from '@/utils/fixtures/runId'
import {
  loginPageFixture,
  LoginPageFixtureType,
} from '@/utils/fixtures/pageObjects/loginPage'

const fixtureObject = {
  ...runIdFixture,
  ...loginPageFixture,
}

export const test = base.extend<LoginPageFixtureType & RunIdFixtureType>(fixtureObject)
