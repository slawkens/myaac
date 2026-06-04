const { defineConfig } = require("cypress");

module.exports = defineConfig({
  expose: {
    serverPath: '/home/runner/work/myaac/myaac/ots',
  },
  allowCypressEnv: false,
  e2e: {
    baseUrl: 'http://localhost:8080',
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
