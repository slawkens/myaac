const { defineConfig } = require("cypress");

module.exports = defineConfig({
  expose: {
    serverPath: 'F:\\path-to-your-server-files',
  },
  allowCypressEnv: false,
  e2e: {
    baseUrl: 'http://localhost',
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
