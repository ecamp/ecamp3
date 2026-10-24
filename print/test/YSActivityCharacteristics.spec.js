import { describe, expect, it } from 'vitest'
import { shallowMount } from '@vue/test-utils'
import YSActivityCharacteristics from '../components/scheduleEntry/contentNode/YSActivityCharacteristics.vue'
import fr from '../../common/locales/fr.json'

describe('YSActivityCharacteristics print', () => {
  it('prints only selected forms with their translations', () => {
    const wrapper = shallowMount(YSActivityCharacteristics, {
      props: {
        contentNode: {
          data: {
            options: {
              outdoorTechnique: { checked: true },
              community: { checked: false },
              natureAndEnvironment: { checked: true },
            },
          },
        },
      },
      global: {
        mocks: { $t: (key) => key.split('.').reduce((value, part) => value[part], fr) },
        stubs: { ContentNodeContent: { template: '<div><slot /></div>' } },
      },
    })
    expect(wrapper.findAll('.entry')).toHaveLength(2)
    expect(wrapper.text()).toContain(
      'Utiliser efficacement des techniques de vie en plein air et de construction de camp'
    )
    expect(wrapper.text()).toContain(
      'Interagir de manière respectueuse avec son environnement'
    )
    expect(wrapper.text()).not.toContain(
      'Renforcer la cohésion du groupe avec bienveillance'
    )
  })
})
