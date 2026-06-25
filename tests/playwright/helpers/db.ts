/**
 * Helper untuk manipulasi database dan state aplikasi
 * Digunakan oleh Playwright test specs untuk setup prekondisi.
 */
import { execSync } from 'child_process';
import { Page } from '@playwright/test';

const ARTISAN = 'php /home/hanif/Documents/Laravel/TA-Website/artisan';

export function artisan(command: string): string {
  return execSync(`${ARTISAN} ${command}`, { encoding: 'utf-8' });
}

/**
 * Reset state DeviceControl ke default (OTOMATIS, unlocked, online).
 */
export function resetDeviceControl(): void {
  artisan(`tinker --execute="
    \\$c = \\App\\Models\\DeviceControl::first();
    if (\\$c) {
      \\$c->update([
        'is_manual' => false,
        'controlled_by' => null,
        'locked_until' => null,
        'last_ping_at' => now(),
        'fan' => 0,
        'mist' => json_encode(array_fill(0, 6, 0)),
        'mist_stop_at' => json_encode(array_fill(0, 6, null)),
      ]);
      echo 'OK';
    } else {
      echo 'NO_DEVICE';
    }
  "`);
}

/**
 * Set ESP32 sebagai OFFLINE (last_ping_at > 30 detik yang lalu).
 */
export function setEsp32Offline(): void {
  artisan(`tinker --execute="
    \\$c = \\App\\Models\\DeviceControl::first();
    if (\\$c) {
      \\$c->update(['last_ping_at' => now()->subMinutes(5)]);
      echo 'OK';
    }
  "`);
}

/**
 * Set ESP32 sebagai ONLINE.
 */
export function setEsp32Online(): void {
  artisan(`tinker --execute="
    \\$c = \\App\\Models\\DeviceControl::first();
    if (\\$c) {
      \\$c->update(['last_ping_at' => now()]);
      echo 'OK';
    }
  "`);
}

/**
 * Kunci kontrol untuk user tertentu (simulasi user lain sedang mengontrol).
 */
export function lockControlForUser(userId: number): void {
  artisan(`tinker --execute="
    \\$c = \\App\\Models\\DeviceControl::first();
    if (\\$c) {
      \\$c->update([
        'is_manual' => true,
        'controlled_by' => ${userId},
        'locked_until' => \\Carbon\\Carbon::now()->addMinutes(5),
      ]);
      echo 'OK';
    }
  "`);
}

/**
 * Reset session admin (lock configuration panel).
 */
export function lockAdminPanel(): void {
  artisan(`tinker --execute="session()->forget('admin_unlocked'); echo 'OK';"`);
}

/**
 * Selesaikan semua siklus yang sedang berjalan (ubah status menjadi 'selesai').
 */
export function finishAllActiveCycles(): void {
  artisan(`tinker --execute="
    \\App\\Models\\Cycle::where('status', 'berjalan')->update(['status' => 'selesai', 'end_date' => now()]);
    echo 'OK';
  "`);
}

/**
 * Hapus siklus terakhir (untuk cleanup).
 */
export function deleteLastCycle(): void {
  artisan(`tinker --execute="
    \\$c = \\App\\Models\\Cycle::latest()->first();
    if (\\$c) { \\$c->delete(); echo 'OK'; } else { echo 'NONE'; }
  "`);
}

/**
 * Pastikan ada sensor data untuk testing.
 */
export function ensureSensorData(): void {
  artisan(`tinker --execute="
    if (\\App\\Models\\SensorData::count() === 0) {
      for (\\$i = 0; \\$i < 10; \\$i++) {
        \\App\\Models\\SensorData::create([
          'biopond' => json_encode(array_map(fn() => rand(10000, 50000), range(1,6))),
          'harvest' => rand(5000, 15000),
          'temp' => rand(260, 340) / 10,
          'hum' => rand(600, 850) / 10,
          'soil' => json_encode(array_map(fn() => rand(650, 850) / 10, range(1,6))),
          'ammonia' => rand(50, 180) / 10,
          'created_at' => now()->subHours(\\$i),
        ]);
      }
      echo 'SEEDED';
    } else {
      echo 'EXISTS';
    }
  "`);
}

/**
 * Reset semua kondisi ke state awal untuk pengujian.
 */
export function resetAllState(): void {
  resetDeviceControl();
  finishAllActiveCycles();
  lockAdminPanel();
  ensureSensorData();
}

/**
 * Helper login via UI.
 */
export async function loginAs(page: Page, email: string, password: string): Promise<void> {
  await page.goto('/login');
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
  // Tunggu redirect setelah login sukses (Laravel redirects to '/' for dashboard)
  await page.waitForTimeout(1500);
  // Login sukses: kita harus sudah tidak di halaman login lagi
  const currentUrl = page.url();
  if (currentUrl.includes('/login')) {
    throw new Error('Login gagal: masih di halaman login');
  }
}

/**
 * Helper logout via UI.
 */
export async function logout(page: Page): Promise<void> {
  await page.goto('/');
  // Hover untuk memunculkan dropdown
  const profileArea = page.locator('.group.relative.cursor-pointer').first();
  if (await profileArea.isVisible()) {
    await profileArea.hover();
    await page.waitForTimeout(300);
    const logoutBtn = page.locator('form[action*="logout"] button');
    if (await logoutBtn.isVisible()) {
      await logoutBtn.click();
      await page.waitForTimeout(1000);
    }
  }
}
