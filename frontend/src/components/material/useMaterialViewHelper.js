import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import * as XLSX from 'xlsx'
import { slugify } from '@/plugins/slugify.js'
import { componentI18n } from '@/plugins/i18n/index.js'
import dayjs from '@/common/helpers/dayjs.js'
import { campShortTitle } from '@/common/helpers/campShortTitle.js'
import { apiStore } from '@/plugins/store/index.js'
import { materialListFromRoute } from '@/router.js'
import shortScheduleEntryDescription from './shortScheduleEntryDescription.js'

function generateFilename(camp, materialList) {
  const description = materialList
    ? [componentI18n.t('components.material.useMaterialViewHelper.detail'), materialList]
    : [componentI18n.t('components.material.useMaterialViewHelper.overview')]
  const filename = [campShortTitle(camp), ...description].map(slugify)
  return [...filename, dayjs().format('YYMMDDHHmmss')].join('_') + '.xlsx'
}

async function getActivity(camp, materialItem) {
  if (!materialItem.materialNode) {
    return null
  }
  const root = await materialItem.materialNode().$href('root')
  return camp
    .activities()
    .items.find((activity) => activity.rootContentNode()._meta.self === root)
}

async function getSheets(camp, collection, materialList) {
  return await Promise.all(
    collection.map(async ({ period, materialItems }) => {
      const data = [
        [
          `${
            materialList ??
            componentI18n.t('components.material.useMaterialViewHelper.overview')
          }: ${period.description}`,
        ],
        [
          componentI18n.t('entity.materialItem.fields.quantity'),
          componentI18n.t('entity.materialItem.fields.unit'),
          componentI18n.t('entity.materialItem.fields.article'),
          ...(!materialList ? [componentI18n.t('entity.materialItem.fields.list')] : []),
          componentI18n.t('entity.materialItem.fields.reference'),
        ],
      ]
      await Promise.all(
        materialItems.items.map(async (materialItem) => {
          const activity = await getActivity(camp, materialItem)
          const scheduleEntries = activity
            ?.scheduleEntries()
            .items.map((item) =>
              shortScheduleEntryDescription(item, componentI18n.t.bind(componentI18n))
            )
            .join(', ')
          data.push([
            materialItem.quantity,
            materialItem.unit,
            materialItem.article,
            ...(!materialList ? [materialItem.materialList?.().name] : []),
            activity?.title
              ? `${activity.category().short} ${activity?.title}: ${scheduleEntries}`
              : period.description,
          ])
        })
      )
      return { sheetName: period.description, data }
    })
  )
}

function randomString(len) {
  const p = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  return [...Array(len)].reduce((a) => a + p[~~(Math.random() * p.length)], '')
}

/**
 * @param sheets {array} array of {sheetName: string, data: array}
 * @returns {object} an XLSX workbook
 */
export function toWorkbook(sheets) {
  const workbook = XLSX.utils.book_new()
  sheets.forEach(({ sheetName, data }) => {
    const worksheet = XLSX.utils.aoa_to_sheet(data)
    const validSheetName = sheetName.replaceAll(/[?*[\]/\\:]/g, '')
    let slicedSheetName = validSheetName.slice(0, 31)
    if (workbook.SheetNames.includes(slicedSheetName)) {
      slicedSheetName = validSheetName.slice(0, 28) + randomString(3)
    }
    workbook.SheetNames.push(slicedSheetName)
    workbook.Sheets[slicedSheetName] = worksheet
  })
  return workbook
}

/**
 * @param {object} camp
 * @param {{value: {period: object, materialItems: {items: array}}[]}} collection
 * @param {{value: {name: string}|null}|null} materialList computed ref to material list, or null for overview
 */
function downloadMaterialList(camp, collection, materialList) {
  return async () => {
    await camp.activities().$loadItems()

    const materialListName = materialList?.value?.name ?? null
    const sheets = await getSheets(camp, collection.value, materialListName)
    const workbook = toWorkbook(sheets)
    XLSX.writeFile(workbook, generateFilename(camp, materialListName))
  }
}

function loadPeriods(camp) {
  const openPeriods = ref([])

  camp.periods()._meta.load.then((period) =>
    period.items.forEach((period, index) => {
      if (Date.parse(period.end) >= Date.now()) {
        openPeriods.value.push(index)
      }
    })
  )

  return openPeriods
}

/**
 * @param {Object} camp
 * @param {boolean} [list]
 */
export function useMaterialViewHelper(camp, list) {
  const computedList = computed(() => (list ? materialListFromRoute(useRoute()) : null))

  const collection = computed(() => {
    const materialList = computedList.value?._meta.self
    return camp.periods().items.map((period) => ({
      period,
      materialItems: apiStore.get().materialItems({
        period: period._meta.self,
        materialList,
      }),
    }))
  })

  const downloadXlsx = downloadMaterialList(camp, collection, computedList)
  const openPeriods = loadPeriods(camp)

  onMounted(async () => {
    await Promise.all([
      apiStore
        .get()
        .contentNodes({
          isRoot: 'true',
          camp: camp._meta.self,
        })
        .$loadItems(),
      ...collection.value.map(({ materialItems }) => materialItems.$reload()),
      camp.categories().$loadItems(),
    ])
  })

  return {
    collection,
    downloadXlsx,
    downloadMaterialList,
    openPeriods,
  }
}
