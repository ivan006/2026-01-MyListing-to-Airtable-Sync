<template>
  <q-page class="q-pa-md">

    <!-- Title -->
    <div class="text-h5 q-mb-md">
      Connector — Target Fetch
    </div>

    <!-- Top row -->
    <div class="row q-col-gutter-md q-mb-md">

      <!-- Source / Controls -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">
              Source
            </div>

            <q-select v-model="entity" :options="entities" option-label="label" option-value="value" label="Entity"
              outlined dense class="q-mb-sm" />

            <q-input v-model="recordId" label="Record ID" outlined dense class="q-mb-sm" />

            <q-btn label="Fetch Target" color="primary" unelevated :loading="loading" @click="fetchTarget" />

          </q-card-section>
        </q-card>
      </div>

      <!-- Target Raw -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">
              Target (Raw)
            </div>

            <pre class="q-pa-sm" style="
                background:#111;
                color:#0f0;
                border-radius:4px;
                max-height:250px;
                overflow:auto;
                font-size:12px;
              ">{{ targetRaw }}</pre>

          </q-card-section>
        </q-card>
      </div>

    </div>

    <!-- Bottom row -->
    <div class="row q-col-gutter-md">

      <!-- Source placeholder -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">
              Source Record
            </div>

            <pre class="q-pa-sm" style="
                background:#111;
                color:#777;
                border-radius:4px;
                min-height:200px;
                font-size:12px;
              ">—</pre>

          </q-card-section>
        </q-card>
      </div>

      <!-- Target record -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">
              Target Record
            </div>

            <pre class="q-pa-sm" style="
                background:#111;
                color:#0f0;
                border-radius:4px;
                min-height:200px;
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
  name: 'ConnectorTargetFetch',

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
      try {
        const API = import.meta.env.VITE_CONNECTOR_BASE
        const res = await fetch(
          `${API}/index.php?endpoint=configs-fetch`
        )
        const json = await res.json()

        this.entities = json.entities.map(e => ({
          label: e.wp_entity_name,
          value: e.wp_entity_name
        }))

      } catch (e) {
        console.error('Failed to load configs', e)
      }
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
