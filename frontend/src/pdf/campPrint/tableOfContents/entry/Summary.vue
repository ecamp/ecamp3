<script>
import PdfComponent from '@/pdf/PdfComponent.js'

export default {
  name: 'Summary',
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    periods() {
      return this.entry.options.periods.map((periodUri) => this.api.get(periodUri))
    },
  },
}
</script>
