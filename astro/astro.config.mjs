// astro.config.mjs
import { defineConfig } from "astro/config";
import { fileURLToPath } from "url";

export default defineConfig({
  vite: {
    resolve: {
      alias: {
        "@services": fileURLToPath(new URL("./src/services", import.meta.url)),
        "@components": fileURLToPath(
          new URL("./src/components", import.meta.url),
        ),
        "@layouts": fileURLToPath(new URL("./src/layouts", import.meta.url)),
        "@scripts": fileURLToPath(new URL("./src/scripts", import.meta.url)),
        "@assets": fileURLToPath(new URL("./src/assets", import.meta.url)),
      },
    },
    optimizeDeps: {
      include: [
        "astro-leaflet > leaflet",
        "chart.js",
        "chart.js/auto",
        "bootstrap",
      ],
      // Exclude these from optimization to prevent stale cache errors
      exclude: [
        "leaflet",
        "sweetalert2",
        "astro/runtime/client/dev-toolbar/entrypoint.js",
      ],
    },
    ssr: {
      // These packages need to be bundled for SSR
      noExternal: ["sweetalert2", "astro-leaflet"],
    },
    server: {
      fs: {
        // Allow serving files from the project root
        strict: false,
      },
    },
  },
  server: {
    port: 4321,
    host: true, // Listen on all addresses
  },
  // Output directory for production build
  outDir: "./dist",
  // Public directory for static assets
  publicDir: "./public",
  devToolbar: {
    enabled: true, // Set to false if you want to disable it
  },
});
