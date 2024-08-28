import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router/composables'
import * as XLSX from 'xlsx'
import { slugify } from '@/plugins/slugify.js'
import i18n from '@/plugins/i18n/index.js'
import dayjs from '@/common/helpers/dayjs.js'
import { campShortTitle } from '@/common/helpers/campShortTitle.js'
import { apiStore } from '@/plugins/store/index.js'
import { materialListFromRoute } from '@/router.js'
import shortScheduleEntryDescription from './shortScheduleEntryDescription.js'

function generateFilename(camp, materialList) {
  const description = materialList
    ? [i18n.tc('components.material.useMaterialViewHelper.detail'), materialList]
    : [i18n.tc('components.material.useMaterialViewHelper.overview')]
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
            materialList ?? i18n.tc('components.material.useMaterialViewHelper.overview')
          }: ${period.description}`,
        ],
        [
          i18n.tc('entity.materialItem.fields.quantity'),
          i18n.tc('entity.materialItem.fields.unit'),
          i18n.tc('entity.materialItem.fields.article'),
          ...(!materialList ? [i18n.tc('entity.materialItem.fields.list')] : []),
          i18n.tc('entity.materialItem.fields.reference'),
        ],
      ]
      await Promise.all(
        materialItems.items.map(async (materialItem) => {
          const activity = await getActivity(camp, materialItem)
          const scheduleEntries = activity
            ?.scheduleEntries()
            .items.map((item) => shortScheduleEntryDescription(item, i18n.tc.bind(i18n)))
            .join(', ')
          data.push([
            materialItem.quantity,
            materialItem.unit,
            materialItem.article,
            ...(!materialList ? [materialItem.materialList().name] : []),
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

/**
 * @param {object} camp
 * @param {{value: {period: object, materialItems: {items: array}}[]}} collection
 * @param {string} materialList materialList name if set
 */
function downloadMaterialList(camp, collection, materialList) {
  return async () => {
    await camp.activities().$loadItems()

    const workbook = XLSX.utils.book_new()
    const sheets = await getSheets(camp, collection.value, materialList)
    sheets.forEach(({ sheetName, data }) => {
      const worksheet = XLSX.utils.aoa_to_sheet(data)
      const validSheetName = sheetName.replaceAll(/[?*[\]/\\:]/g, '')
      workbook.SheetNames.push(validSheetName)
      workbook.Sheets[validSheetName] = worksheet
    })
    XLSX.writeFile(workbook, generateFilename(camp, materialList))
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

  const downloadXlsx = downloadMaterialList(camp, collection, computedList.value?.name)
  const openPeriods = loadPeriods(camp)

  onMounted(() => {
    collection.value.map(({ materialItems }) => materialItems.$reload())
  })

  return {
    collection,
    downloadXlsx,
    openPeriods,
  }
}
