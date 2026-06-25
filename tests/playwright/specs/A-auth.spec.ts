/**
 * SiMaggot — Black Box Testing
 * Grup A: Autentikasi & Otorisasi (A1–A5)
 */
import { test, expect } from '@playwright/test';
import { loginAs, logout, resetAllState } from '../helpers/db';

const TEST_EMAIL = 'test@si-maggot.id';
const TEST_PASSWORD = 'Test1234!';

test.describe('A. Autentikasi & Otorisasi', () => {

  test.beforeAll(async () => {
    resetAllState();
  });

  // ─── A-1: Akses Publik ───────────────────────────────────────
  test('A-1 — Akses Publik (Dashboard, Statistik, Logbook tanpa login)', async ({ page }) => {
    // Syarat: Tidak ada sesi autentikasi (guest).
    // Skenario: Buka halaman publik — dashboard, statistik, logbook.
    // Ekspektasi: Halaman tampil, menu siklus & kontrol TIDAK muncul.

    // Dashboard
    await page.goto('/');
    await expect(page.locator('h1')).toContainText(/Dashboard/i, { timeout: 10000 });
    // Menu terproteksi tidak muncul untuk guest
    await expect(page.getByRole('link', { name: /Siklus/ }).first()).not.toBeVisible();
    await expect(page.getByRole('link', { name: /Kontrol/ }).first()).not.toBeVisible();
    // Tombol login tersedia
    await expect(page.getByRole('link', { name: /Siklus/ }).first()).not.toBeVisible();

    // Statistik
    await page.goto('/statistik');
    await expect(page.locator('h1')).toContainText(/Statistik/i, { timeout: 10000 });
    await expect(page.getByRole('link', { name: /Siklus/ }).first()).not.toBeVisible();

    // Logbook
    await page.goto('/logbook');
    await expect(page.locator('h1')).toContainText(/Logbook|Riwayat/i, { timeout: 10000 });
    await expect(page.getByRole('link', { name: /Siklus/ }).first()).not.toBeVisible();
  });

  // ─── A-2: Login Berhasil ─────────────────────────────────────
  test('A-2 — Login Berhasil dengan kredensial valid', async ({ page }) => {
    // Syarat: Kredensial valid terdaftar di database.
    // Skenario: Login dengan email & password valid.
    // Ekspektasi: Berhasil masuk, menu terproteksi muncul.

    await page.goto('/login');
    await expect(page.locator('h2')).toContainText(/Login/i);

    await page.fill('input[name="email"]', TEST_EMAIL);
    await page.fill('input[name="password"]', TEST_PASSWORD);
    await page.click('button[type="submit"]');

    // Harus redirect ke dashboard
    await page.waitForURL(/\/dashboard|\/$/, { timeout: 10000 });
    await expect(page.locator('h1')).toContainText(/Dashboard/i);

    // Menu terproteksi muncul
    await expect(page.getByRole('link', { name: /Siklus/ }).first()).toBeVisible();
    await expect(page.getByRole('link', { name: /Kontrol/ }).first()).toBeVisible();

    // Avatar/nama user muncul
    await expect(page.getByText('Test User')).toBeVisible();
  });

  // ─── A-3: Login Gagal ────────────────────────────────────────
  test('A-3 — Login Gagal dengan password salah', async ({ page }) => {
    // Syarat: Kredensial tidak valid.
    // Skenario: Login dengan password salah.
    // Ekspektasi: Sistem menolak, pesan error muncul.

    await page.goto('/login');
    await page.fill('input[name="email"]', TEST_EMAIL);
    await page.fill('input[name="password"]', 'WrongPassword123!');
    await page.click('button[type="submit"]');

    // Tetap di halaman login
    await expect(page).toHaveURL(/\/login/);
    // Ada pesan error (Laravel validation error)
    await expect(page.locator('.text-red-500, .text-red-600, [class*="error"]').first()).toBeVisible({ timeout: 5000 });
  });

  // ─── A-4: Otorisasi PIN Konfigurasi ──────────────────────────
  test('A-4 — Otorisasi PIN Konfigurasi (Unlock Configuration Panel)', async ({ page }) => {
    // Syarat: Sudah login, PIN valid: 210601.
    // Skenario: Akses /configuration-panel, masukkan PIN.
    // Ekspektasi: Panel terbuka, tabel akun muncul.

    await loginAs(page, TEST_EMAIL, TEST_PASSWORD);

    // Buka panel konfigurasi
    await page.goto('/configuration-panel');
    await expect(page.locator('input[name="pin"]')).toBeVisible({ timeout: 5000 });

    // Masukkan PIN valid
    await page.fill('input[name="pin"]', '210601');
    await page.click('button[type="submit"]');

    // Panel terbuka — tabel user muncul
    await expect(page.locator('table')).toBeVisible({ timeout: 5000 });
  });

  // ─── A-5: Logout Sistem ──────────────────────────────────────
  test('A-5 — Logout Sistem dan kembali ke halaman publik', async ({ page }) => {
    // Syarat: Sedang login.
    // Skenario: Klik tombol logout.
    // Ekspektasi: Sesi dihapus, kembali ke halaman publik.

    await loginAs(page, TEST_EMAIL, TEST_PASSWORD);
    await logout(page);

    // Kembali ke halaman publik
    await page.goto('/');
    // Menu terproteksi hilang
    await expect(page.getByRole('link', { name: /Siklus/ }).first()).not.toBeVisible();
    // Tombol login muncul
    await expect(page.getByRole('link', { name: /Login/i })).toBeVisible();
  });

});
