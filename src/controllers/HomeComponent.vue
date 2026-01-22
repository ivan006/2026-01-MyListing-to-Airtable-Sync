<template>
  <q-page class="q-pa-md">

    <div class="text-h5 q-mb-md">
      My-Listing → Airtable Sync
    </div>

    <!-- Top row -->
    <div class="row q-col-gutter-md q-mb-md">

      <!-- Source (placeholder for now) -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-subtitle1 q-mb-sm">Source</div>

            <pre class="q-pa-sm" style="background:#111;color:#777;border-radius:4px;min-height:180px;font-size:12px;">
{{ record.source.data
  ? JSON.stringify(record.source.data, null, 2)
  : '—' }}
            </pre>
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

            <q-btn label="Fetch Target" color="primary" unelevated :loading="record.target.loading"
              @click="fetchTarget" />

          </q-card-section>
        </q-card>
      </div>

    </div>

    <!-- Bottom row -->
    <div class="row q-col-gutter-md">

      <!-- Future diff / actions -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>
            <pre class="q-pa-sm" style="background:#111;color:#777;border-radius:4px;min-height:260px;font-size:12px;">
—
            </pre>
          </q-card-section>
        </q-card>
      </div>

      <!-- Target Record -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">
              Target Record
            </div>

            <pre class="q-pa-sm"
              style="background:#111;color:#0f0;border-radius:4px;min-height:260px;overflow:auto;font-size:12px;">
{{ record.target.data
  ? JSON.stringify(record.target.data, null, 2)
  : '—' }}
            </pre>

          </q-card-section>
        </q-card>
      </div>

    </div>

  </q-page>
</template>

<script>
export default {
  name: 'RevisionBridge',

  data() {
    return {
      entity: null,
      recordId: '',
      entities: [],

      record: {
        source: {
          data: null,
          loading: false,
          error: null
        },
        target: {
          data: null,
          loading: false,
          error: null
        }
      }
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

      this.record.target.loading = true
      this.record.target.error = null
      this.record.target.data = null

      try {
        const API = import.meta.env.VITE_CONNECTOR_BASE
        const res = await fetch(
          `${API}/index.php?endpoint=target-fetch&entity=${encodeURIComponent(this.entity)}&id=${encodeURIComponent(this.recordId)}`
        )
        const json = await res.json()
        this.record.target.data = json
      } catch (e) {
        this.record.target.error = e.message
      } finally {
        this.record.target.loading = false
      }
    }
  }
}
</script>
