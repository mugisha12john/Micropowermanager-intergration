<template>
  <div class="battery-detail-page">
    <md-button class="md-primary" @click="$router.push('/batteries')">
      Back to batteries
    </md-button>
    <p v-if="error" class="error-message">{{ error }}</p>
    <md-card v-if="battery">
      <md-card-header>
        <div class="md-title">{{ battery.battery_id }}</div>
        <div class="md-subhead">
          {{ battery.site_id }} · {{ battery.bms_state || "Unknown state" }}
        </div>
      </md-card-header>
      <md-card-content>
        <div class="health-grid">
          <div>
            <strong>SoC</strong>
            <span>{{ percentage(battery.soc) }}</span>
          </div>
          <div>
            <strong>SoH</strong>
            <span>{{ percentage(battery.soh) }}</span>
          </div>
          <div>
            <strong>Cycles</strong>
            <span>{{ valueOrDash(battery.cycle_count) }}</span>
          </div>
          <div>
            <strong>Cell imbalance</strong>
            <span>{{ numberWithUnit(battery.cell_imbalance_v, "V") }}</span>
          </div>
          <div>
            <strong>Max temperature</strong>
            <span>{{ numberWithUnit(battery.max_temperature_c, "°C") }}</span>
          </div>
          <div>
            <strong>Last seen</strong>
            <span>{{ lastSeenLabel(battery.last_seen_at) }}</span>
          </div>
        </div>
        <h3>Fault log</h3>
        <md-table
          v-if="battery.faults && battery.faults.length"
          v-model="battery.faults"
          md-card
        >
          <md-table-row slot="md-table-row" slot-scope="{ item }">
            <md-table-cell md-label="Severity">
              {{ item.severity }}
            </md-table-cell>
            <md-table-cell md-label="Rule">{{ item.rule_id }}</md-table-cell>
            <md-table-cell md-label="Title">{{ item.title }}</md-table-cell>
            <md-table-cell md-label="Status">{{ item.status }}</md-table-cell>
            <md-table-cell md-label="Occurred">
              {{ formatDate(item.occurred_at) }}
            </md-table-cell>
          </md-table-row>
        </md-table>
        <p v-else>No faults recorded.</p>
      </md-card-content>
    </md-card>
    <md-progress-spinner v-else-if="loading" md-mode="indeterminate" />
  </div>
</template>

<script>
import moment from "moment-timezone"

import BatteryHealthRepository from "@/repositories/LibreSolarBatteryHealthRepository.js"

export default {
  name: "BatteryDetail",
  data() {
    return {
      battery: null,
      loading: true,
      error: null,
      refreshTimer: null,
      refreshing: false,
      disposed: false,
    }
  },
  mounted() {
    this.loadBattery()
  },
  beforeDestroy() {
    this.disposed = true
    clearTimeout(this.refreshTimer)
  },
  watch: {
    "$route.params.batteryId"() {
      clearTimeout(this.refreshTimer)
      this.battery = null
      this.error = null
      this.loading = true
      this.loadBattery()
    },
  },
  methods: {
    async loadBattery() {
      if (this.refreshing || this.disposed) return
      this.refreshing = true
      clearTimeout(this.refreshTimer)
      const batteryId = this.$route.params.batteryId
      try {
        const response = await BatteryHealthRepository.detail(batteryId)
        if (!this.disposed && batteryId === this.$route.params.batteryId) {
          this.battery = response.data.data
          this.error = null
        }
      } catch (error) {
        if (!this.disposed && batteryId === this.$route.params.batteryId) {
          this.error =
            error.response?.data?.message || "Unable to load battery."
        }
      } finally {
        this.refreshing = false
        if (!this.disposed) {
          if (batteryId === this.$route.params.batteryId) {
            this.loading = false
            this.refreshTimer = setTimeout(() => this.loadBattery(), 10000)
          } else {
            this.loadBattery()
          }
        }
      }
    },
    percentage(value) {
      return value === null || value === undefined ? "-" : `${value}%`
    },
    valueOrDash(value) {
      return value === null || value === undefined ? "-" : value
    },
    numberWithUnit(value, unit) {
      return value === null || value === undefined ? "-" : `${value} ${unit}`
    },
    formatDate(value) {
      return value ? moment(value).format("YYYY-MM-DD HH:mm") : "-"
    },
    lastSeenLabel(value) {
      return value
        ? `${moment(value).fromNow()} (${this.formatDate(value)})`
        : "Never"
    },
  },
}
</script>

<style lang="scss" scoped>
.battery-detail-page {
  padding: 24px;
}

.health-grid {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  margin-bottom: 32px;
}

.health-grid div {
  background: #f6f8fa;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px;
}

.health-grid span {
  font-size: 20px;
}

h3 {
  margin: 0 0 16px;
}

.error-message {
  color: #b3261e;
}
</style>
