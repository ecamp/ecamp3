const ELASTICSEARCH_URL = process.env.ELASTICSEARCH_URL
const MAX_INDEX_AGE = parseInt(process.env.MAX_INDEX_AGE)

const MILLIS_OF_DAY = 24 * 60 * 60 * 1000;

process.on("SIGINT", () => {
  process.exit(1)
});

process.on("SIGTERM", () => {
  process.exit(1)
});

// noinspection InfiniteLoopJS
while (true) {
  const response = await fetch(`${ELASTICSEARCH_URL}/_cat/indices/logstash*?h=index,id,creation.date.string&format=json`)
  const body = await response.json()

  for (const index of body) {
    const date = new Date(index["creation.date.string"])
    if (Date.now() - date > MAX_INDEX_AGE * MILLIS_OF_DAY) {
      console.log(`Deleting ${index.index} with creation date '${date}'`)
      await fetch(`${ELASTICSEARCH_URL}/${index.index}`, {
        method: "DELETE"
      })
    }
    await new Promise((resolve) => setTimeout(resolve, 100))
  }
  
  await new Promise((resolve) => setTimeout(resolve, MILLIS_OF_DAY))
}
