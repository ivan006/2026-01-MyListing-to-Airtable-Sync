<template>
  <q-page class="q-pa-md">

    <!-- Title -->
    <div class="text-h5 q-mb-md">
      My-Listing → Airtable Sync
    </div>

    <!-- Top row -->
    <div class="row q-col-gutter-md q-mb-md">

      <!-- Source (empty / later populated) -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-subtitle1 q-mb-sm">Source</div>

            <div class="q-pa-sm" style="
                background:#111;
                color:#777;
                border-radius:4px;
                min-height:180px;
                font-size:12px;
              ">
              —
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Target controls -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">Target</div>

            <q-select v-model="entity" :options="entities" option-label="label" option-value="value" emit-value
              map-options label="Entity" outlined dense class="q-mb-sm" />

            <q-input v-model="recordId" label="ID" outlined dense class="q-mb-sm" />

            <q-btn label="Fetch" color="primary" unelevated :loading="loading" @click="fetchTarget" />

          </q-card-section>
        </q-card>
      </div>

    </div>

    <!-- Bottom row -->
    <div class="row q-col-gutter-md">

      <!-- Empty left (future diff / actions) -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>
            <div class="q-pa-sm" style="
                background:#111;
                color:#777;
                border-radius:4px;
                min-height:260px;
                font-size:12px;
              ">
              —
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Source Record (raw JSON for now) -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">
              Source Record
            </div>

            <pre class="q-pa-sm" style="
                background:#111;
                color:#0f0;
                border-radius:4px;
                min-height:260px;
                overflow:auto;
                font-size:12px;
              ">{{ targetRaw }}</pre>

          </q-card-section>
        </q-card>
      </div>

    </div>

  </q-page>
</template>

<script>
export default {
  name: 'RevisionBridgeTargetFetch',

  data() {
    return {
      entity: null,
      recordId: '',
      entities: [],
      loading: false,
      targetRaw: ''
    }
  },

  async mounted() {
    await this.loadConfigs()
  },

  methods: {
    async loadConfigs() {
      const API = import.meta.env.VITE_CONNECTOR_BASE
      const res = await fetch(`${API}/index.php?endpoint=configs-fetch`)
      const json = await res.json()

      this.entities = json.entities.map(e => ({
        label: e.target_entity_name,
        value: e.target_entity_name
      }))
    },

    async fetchTarget() {
      if (!this.entity || !this.recordId) return

      this.loading = true
      this.targetRaw = ''

      try {
        const API = import.meta.env.VITE_CONNECTOR_BASE
        const res = await fetch(
          `${API}/index.php?endpoint=target-fetch&entity=${encodeURIComponent(this.entity)}&id=${encodeURIComponent(this.recordId)}`
        )
        const json = await res.json()
        this.targetRaw = JSON.stringify(json, null, 2)
      } catch (e) {
        this.targetRaw = `Error: ${e.message}`
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
