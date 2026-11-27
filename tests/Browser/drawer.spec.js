import { test, expect } from '@playwright/test';
import { directions, focusIsWithin, open } from './helpers.js';

const panelOf = (dialog) => dialog.locator('> div').nth(1);

for (const mode of directions) {
    test.describe(`drawer · ${mode.name}`, () => {
        test('the end drawer opens on the logical end edge', async ({ page }) => {
            await open(page, 'drawer', mode);

            const dialog = page.getByRole('dialog', { name: 'Filters' });
            await page.getByRole('button', { name: 'Open end drawer' }).click();
            await expect(dialog).toBeVisible();
            await page.waitForTimeout(350); // Wait for enter transition to finish

            const box = await panelOf(dialog).boundingBox();
            const viewport = page.viewportSize();

            if (mode.dir === 'ltr') {
                expect(Math.abs(box.x + box.width - viewport.width)).toBeLessThan(2);
            } else {
                expect(Math.abs(box.x)).toBeLessThan(2);
            }
        });

        test('the start drawer opens on the logical start edge', async ({ page }) => {
            await open(page, 'drawer', mode);

            const dialog = page.getByRole('dialog', { name: 'Menu' });
            await page.getByRole('button', { name: 'Open start drawer' }).click();
            await expect(dialog).toBeVisible();
            await page.waitForTimeout(350);

            const box = await panelOf(dialog).boundingBox();
            const viewport = page.viewportSize();

            if (mode.dir === 'ltr') {
                expect(Math.abs(box.x)).toBeLessThan(2);
            } else {
                expect(Math.abs(box.x + box.width - viewport.width)).toBeLessThan(2);
            }
        });

        test('traps focus, closes with Escape and restores focus', async ({ page }) => {
            await open(page, 'drawer', mode);

            const opener = page.getByRole('button', { name: 'Open end drawer' });
            const dialog = page.getByRole('dialog', { name: 'Filters' });

            await opener.click();
            await expect.poll(() => focusIsWithin(page, '[role=dialog]')).toBe(true);
            for (let step = 0; step < 6; step++) {
                await page.keyboard.press('Tab');
                expect(await focusIsWithin(page, '[role=dialog]')).toBe(true);
            }

            await page.keyboard.press('Escape');
            await expect(dialog).toBeHidden();
            await expect(opener).toBeFocused();
        });
    });
}
