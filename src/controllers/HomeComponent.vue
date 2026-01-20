<template>
  <q-page class="q-pa-md">

    <!-- Title -->
    <div class="text-h5 q-mb-md">
      My-Listing → Airtable Sync
    </div>

    <!-- Top Panels -->
    <div class="row q-col-gutter-md q-mb-md">

      <!-- Source -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>

            <div class="text-subtitle1 q-mb-sm">
              Source
            </div>

            <q-select
              v-model="entity"
              :options="entities"
              label="Entity"
              outlined
              dense
              class="q-mb-sm"
            />

            <q-input
              v-model="recordId"
              label="ID"
              outlined
              dense
              class="q-mb-sm"
            />

            <q-btn
              label="Fetch Target"
              color="primary"
              unelevated
              :loading="loading"
              @click="fetchTarget"
            />

          </q-card-section>
        </q-card>
      </div>

      <!-- Target -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-subtitle1 q-mb-sm">
              Target
            </div>

            <pre
              class="q-pa-sm"
              style="
                background:#111;
                color:#0f0;
                border-radius:4px;
                max-height:200px;
                overflow:auto;
                font-size:12px;
              "
            >
{{ targetRaw }}
            </pre>
          </q-card-section>
        </q-card>
      </div>

    </div>

    <!-- Bottom Panels -->
    <div class="row q-col-gutter-md">

      <!-- Source Record -->
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-subtitle1 q-mb-sm">
              Source Record
            </div>

            <pre
              class="q-pa-sm"
              style="
                background:#111;
                color:#888;
                border-radius:4px;
                min-height:200px;
                font-size:12px;
              "
            >
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

            <pre
              class="q-pa-sm"
              style="
                background:#111;
                color:#0f0;
                border-radius:4px;
                min-height:200px;
                overflow:auto;
                font-size:12px;
              "
            >
{{ targetRaw }}
            </pre>
          </q-card-section>
        </q-card>
      </div>

    </div>

  </q-page>
</template>

<script>
export default {
  data () {
    return {
      entity: null,
      recordId: '',
      entities: ['artworks'],
      loading: false,
      targetRaw: ''
    }
  },

  methods: {
    async fetchTarget () {
      if (!this.entity || !this.recordId) return

      this.loading = true
      try {
        const API = import.meta.env.VITE_CONNECTOR_BASE
        const res = await fetch(
          `${API}/index.php?endpoint=target-fetch&entity=${this.entity}&id=${this.recordId}`
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
