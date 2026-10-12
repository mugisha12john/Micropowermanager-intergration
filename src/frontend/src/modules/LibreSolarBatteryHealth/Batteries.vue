<template>
  <div class="batteries-page">
    <md-card>
      <md-card-header>
        <div class="md-title">Batteries</div>
        <div class="md-subhead">Low-frequency battery health summaries</div>
      </md-card-header>
      <md-card-content>
        <md-progress-spinner v-if="loading" md-mode="indeterminate" />
        <p v-else-if="error" class="error-message">{{ error }}</p>
        <md-table v-else v-model="batteries" md-card>
          <md-table-row
            slot="md-table-row"
            slot-scope="{ item }"
            class="cursor-pointer"
            @click="openBattery(item.battery_id)"
          >
            <md-table-cell md-label="Battery">
              {{ item.battery_id }}
            </md-table-cell>
            <md-table-cell md-label="Site">{{ item.site_id }}</md-table-cell>
            <md-table-cell md-label="SoC">
              {{ percentage(item.soc) }}
            </md-table-cell>
            <md-table-cell md-label="SoH / cycles">
              {{ percentage(item.soh) }} / {{ valueOrDash(item.cycle_count) }}
            </md-table-cell>
            <md-table-cell md-label="Cell imbalance">
              {{ numberWithUnit(item.cell_imbalance_v, "V") }}
            </md-table-cell>
            <md-table-cell md-label="Max temperature">
              {{ numberWithUnit(item.max_temperature_c, "°C") }}
            </md-table-cell>
            <md-table-cell md-label="Last seen">
              <span
                :class="['status-badge', lastSeenStatus(item.last_seen_at)]"
              >
                {{ lastSeenLabel(item.last_seen_at) }}
              </span>
            </md-table-cell>
          </md-table-row>
        </md-table>
        <p v-if="!loading && !error && !batteries.length" class="empty-message">
          No battery health summaries have been received.
        </p>
      </md-card-content>
    </md-card>
  </div>
</template>

<script>
import moment from "moment-timezone"

import BatteryHealthRepository from "@/repositories/LibreSolarBatteryHealthRepository.js"

export default {
  name: "Batteries",
  data() {
    return {
      batteries: [],
      loading: true,
      error: null,
      refreshTimer: null,
      refreshing: false,
      disposed: false,
    }
  },
  mounted() {
    this.loadBatteries()
  },
  beforeDestroy() {
    this.disposed = true
    clearTimeout(this.refreshTimer)
  },
  methods: {
    async loadBatteries() {
      if (this.refreshing || this.disposed) return
      this.refreshing = true
      clearTimeout(this.refreshTimer)
      try {
        const response = await BatteryHealthRepository.list()
        if (!this.disposed) {
          this.batteries = response.data.data.data || []
          this.error = null
        }
      } catch (error) {
        if (!this.disposed) {
          this.error =
            error.response?.data?.message || "Unable to load batteries."
        }
      } finally {
        this.refreshing = false
        this.loading = false
        if (!this.disposed) {
          this.refreshTimer = setTimeout(() => this.loadBatteries(), 10000)
        }
      }
    },
    openBattery(batteryId) {
      this.$router.push(`/batteries/${encodeURIComponent(batteryId)}`)
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
    lastSeenStatus(value) {
      if (!value) return "offline"
      return moment().diff(moment(value), "minutes") > 5 ? "stale" : "online"
    },
    lastSeenLabel(value) {
      if (!value) return "Never"
      return `${this.lastSeenStatus(value)} · ${moment(value).fromNow()}`
    },
  },
}
</script>

<style lang="scss" scoped>
.batteries-page {
  padding: 24px;
}

.md-progress-spinner {
  margin: 32px auto;
}

.error-message {
  color: #b3261e;
}

.empty-message {
  color: #667085;
  margin: 24px 0 0;
}

.status-badge {
  border-radius: 4px;
  display: inline-block;
  font-size: 12px;
  padding: 4px 8px;
  text-transform: capitalize;
}

.status-badge.online {
  background: #e7f6ec;
  color: #176b36;
}

.status-badge.stale,
.status-badge.offline {
  background: #fff0e6;
  color: #a23b00;
}
</style>
