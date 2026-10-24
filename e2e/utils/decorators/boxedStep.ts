import { test } from '@/utils/etest'

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
export function boxedStep(target: Function, context: ClassMethodDecoratorContext) {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  return function replacementMethod(this: any, ...args: any) {
    const name = this.constructor.name + '.' + (context.name as string)
    return test.step(
      name,
      async () => {
        return await target.call(this, ...args)
      },
      { box: true }
    )
  }
}
