import { test, expect } from '@playwright/test';
import { directions, focusIsWithin, open } from './helpers.js';

for (const mode of directions) {
    test.describe(`modal · ${mode.name}`, () => {
        test('opens, traps focus, locks scroll, closes with Escape and restores focus', async ({ page }) => {
            await open(page, 'modal', mode);

            const opener = page.getByRole('button', { name: 'Open modal' });
            const dialog = page.getByRole('dialog', { name: 'Confirm action' });

            await expect(dialog).toBeHidden();
            await opener.click();
            await expect(dialog).toBeVisible();

            // Focus moves inside the modal and remains trapped within it.
            await expect.poll(() => focusIsWithin(page, '[role=dialog]')).toBe(true);
            for (let step = 0; step < 8; step++) {
                await page.keyboard.press('Tab');
                expect(await focusIsWithin(page, '[role=dialog]')).toBe(true);
            }

            // Page scrolling is locked while the modal is open.
            expect(await page.evaluate(() => document.documentElement.style.overflow)).toBe('hidden');

            await page.keyboard.press('Escape');
            await expect(dialog).toBeHidden();
            await expect(opener).toBeFocused();
            expect(await page.evaluate(() => document.documentElement.style.overflow)).not.toBe('hidden');
        });

        test('closes from the close button, the footer button and the backdrop', async ({ page }) => {
            await open(page, 'modal', mode);

            const opener = page.getByRole('button', { name: 'Open modal' });
            const dialog = page.getByRole('dialog', { name: 'Confirm action' });

            await opener.click();
            await dialog.getByRole('button', { name: 'Close' }).click();
            await expect(dialog).toBeHidden();

            await opener.click();
            await dialog.getByRole('button', { name: 'Cancel' }).click();
            await expect(dialog).toBeHidden();

            await opener.click();
            await expect(dialog).toBeVisible();
            await page.mouse.click(4, 4); // Backdrop click
            await expect(dialog).toBeHidden();
        });

        test('a non-closeable modal ignores Escape and backdrop clicks', async ({ page }) => {
            await open(page, 'modal', mode);

            const dialog = page.getByRole('dialog', { name: 'Terms' });

            await page.getByRole('button', { name: 'Open locked' }).click();
            await expect(dialog).toBeVisible();
            await expect(dialog.getByRole('button', { name: 'Close' })).toHaveCount(0);

            await page.keyboard.press('Escape');
            await page.mouse.click(4, 4);
            await expect(dialog).toBeVisible();

            await dialog.getByRole('button', { name: 'Accept' }).click();
            await expect(dialog).toBeHidden();
        });
    });
}
