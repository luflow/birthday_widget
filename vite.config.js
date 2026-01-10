import { createAppConfig } from "@nextcloud/vite-config";
import { join, resolve } from "path";

export default createAppConfig(
  {
    dashboard: resolve(join("src", "dashboard.js")),
    settings: resolve(join("src", "settings.js")),
  },
  {
    createEmptyCSSEntryPoints: true,
    extractLicenseInformation: true,
    thirdPartyLicense: false,
    config: {
      build: {
        chunkSizeWarningLimit: 1500,
      },
    },
  }
);
