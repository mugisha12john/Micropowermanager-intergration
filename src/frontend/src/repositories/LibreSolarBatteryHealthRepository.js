import Client from "@/repositories/Client/AxiosClient.js"

const resource = "/api/batteries"

export default {
  list(params = {}) {
    return Client.get(`${resource}/health`, { params })
  },
  detail(batteryId) {
    return Client.get(`${resource}/health/${encodeURIComponent(batteryId)}`)
  },
  resource,
}
