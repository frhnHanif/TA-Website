import { defineConfig, devices } from '@playwright/test';

/**
 * Konfigurasi Playwright untuk Black Box Testing SiMaggot
 * Base URL: http://localhost:8000 (Laravel artisan serve)
 */
export default defineConfig({
  testDir: './tests/playwright/specs',
  /* Jalankan test secara sequential untuk menghindari race condition (DB mutation) */
  fullyParallel: false,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on CI only */
  retries: process.env.CI ? 2 : 0,
  /* Sequential untuk menghindari konflik resource */
  workers: 1,
  /* Reporter: HTML (bawaan Playwright) + list untuk terminal */
  reporter: [
    ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ['list'],
  ],
  /* Timeout global */
  timeout: 60000,
  expect: {
    timeout: 15000,
  },
  /* Shared settings */
  use: {
    baseURL: 'http://localhost:8000',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },

  /* Configure projects */
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],

  /* Laravel dev server */
  webServer: {
    command: 'php artisan serve --port=8000',
    url: 'http://localhost:8000',
    reuseExistingServer: !process.env.CI,
    timeout: 120000,
  },
});
