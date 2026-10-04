import { Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'
import { LoginPage } from '@/utils/fixtures/pageObjects/loginPage'
import { CampInfo } from '@/utils/fixtures/pageObjects/camp/admin/campInfo'
import { CampActivitySettings } from '@/utils/fixtures/pageObjects/camp/admin/campActivitySettings'
import {
  CreatedCampCallback,
  CreateCampDialogStep2,
} from '@/utils/fixtures/pageObjects/createCamp/createCampDialogStep2'
import { CampListPage } from '@/utils/fixtures/pageObjects/campListPage'

type CampPrototype = 'empty' | string

export type CampFixtureType = {
  campRegistry: CampRegistry
  createCamp: (prototype: CampPrototype) => Promise<Camp>
  openCreateCampStep2: () => Promise<CreateCampDialogStep2>
  cleanupCamps: () => Promise<void>
}

type CampRegistry = {
  register: (campInfo: CampInfo, campTitle: string) => Camp
  cleanup: () => Promise<void>
}

export const campFixture = {
  campRegistry: async (
    { page }: { page: Page },
    use: (a: CampRegistry) => Promise<void>
  ) => {
    const camps: Camp[] = []
    await use({
      register: (campInfo, campTitle) => {
        const camp = new Camp(page, campInfo.campId, campTitle, campInfo)
        camps.push(camp)
        return camp
      },
      cleanup: async () => {
        for (const camp of camps) await camp.delete()
        camps.length = 0
      },
    })
  },
  cleanupCamps: async (
    { campRegistry }: { campRegistry: CampRegistry },
    use: (a: CampFixtureType['cleanupCamps']) => Promise<void>
  ) => {
    await use(campRegistry.cleanup)
  },
  createCamp: async (
    {
      page,
      runId,
      campRegistry,
    }: { page: Page; runId: string; campRegistry: CampRegistry },
    use: (a: CampFixtureType['createCamp']) => Promise<void>
  ) => {
    await use((prototype) =>
      new CreateCamp(page, prototype, runId, campRegistry.register).create()
    )
  },
  openCreateCampStep2: async (
    {
      page,
      runId,
      campRegistry,
    }: { page: Page; runId: string; campRegistry: CampRegistry },
    use: (a: CampFixtureType['openCreateCampStep2']) => Promise<void>
  ) => {
    await use(() =>
      new CreateCamp(page, null, runId, campRegistry.register).openCreateCampStep2(
        undefined,
        (campInfo) => campRegistry.register(campInfo, `camp ${runId}`)
      )
    )
  },
}

class CreateCamp {
  constructor(
    private readonly _page: Page,
    private readonly _campPrototype: CampPrototype | null,
    private readonly _runId: string,
    private readonly _registerCamp: (campInfo: CampInfo, campTitle: string) => void,
    private readonly _campTitle = `camp ${_runId}`
  ) {}

  @boxedStep
  async create(user = undefined) {
    const createCampDialogStep2 = await this.openCreateCampStep2(user)
    const { campInfo } = await createCampDialogStep2
      .selectPrototype(this._campPrototype!)
      .then((value) =>
        value.submit((campInfo) => this._registerCamp(campInfo, this._campTitle))
      )

    return new Camp(this._page, campInfo.campId, this._campTitle, campInfo)
  }

  @boxedStep
  async openCreateCampStep2(user = undefined, onCreatedCamp?: CreatedCampCallback) {
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    const in2Days = new Date()
    in2Days.setDate(in2Days.getDate() + 2)

    const loginPage = await new LoginPage(this._page).open()
    const campListPage = await loginPage.loginToCampList(user)
    const createCampDialogStep1 = await campListPage.openCreateCampDialog()
    await createCampDialogStep1.fillForm(tomorrow, in2Days, this._campTitle)

    return await createCampDialogStep1.next(onCreatedCamp)
  }
}

export class Camp {
  constructor(
    private readonly _page: Page,
    private readonly _campId: string,
    private readonly _campTitle: string,
    private readonly _campInfo: CampInfo
  ) {}

  get campId(): string {
    return this._campId
  }

  get campTitle(): string {
    return this._campTitle
  }

  get campInfo(): CampInfo {
    return this._campInfo
  }

  get campActivitySettings() {
    return new CampActivitySettings(this._page, this._campId)
  }

  @boxedStep
  async delete() {
    const campInfo = this._campInfo
    await campInfo.goto()
    const dialog = await campInfo.openDeleteDialog()
    await dialog.fillPrompt(this._campTitle)
    await dialog.submit()

    const campListPage = await new CampListPage(this._page).goto()
    await campListPage.expectCampNotListed(this._campTitle)
  }
}
