<template>
  <svg
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:cc="http://creativecommons.org/ns#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:svg="http://www.w3.org/2000/svg"
    xmlns="http://www.w3.org/2000/svg"
    version="1.1"
    xml:space="preserve"
    width="8"
    height="8"
    :style="{ transform: `scale(${scale})`, marginTop: '-0.15em', display: 'inline' }"
  >
    <metadata id="metadata13">
      <rdf:RDF>
        <cc:Work rdf:about="">
          <dc:format>image/svg+xml</dc:format>
          <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage" />
          <dc:title></dc:title>
        </cc:Work>
      </rdf:RDF>
    </metadata>
    <circle r="4" cy="4" cx="4" style="fill: green" />
    <polygon
      points="3.3,4.725 2.25,3.65 1.6,4.275 3.3,5.975 6.4,2.9 5.75,2.275"
      fill="white"
    />
  </svg>
</template>

<script>
export default {
  props: {
    size: { type: Number, default: 12 },
  },
  computed: {
    scale() {
      return this.size / 8.0
    },
  },
}
</script>
