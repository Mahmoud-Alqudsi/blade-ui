import { test, expect } from '@playwright/test';
import { directions, open } from './helpers.js';

for (const mode of directions) {
    test.describe(`feedback · ${mode.name}`, () => {
        test('a dismissible alert disappears; a plain alert needs no JavaScript to render', async ({ page }) => {
            await open(page, 'feedback', mode);

            const danger = page.getByRole('alert');
            await expect(danger).toBeVisible();
            await danger.getByRole('button', { name: 'Close' }).click();
            await expect(danger).toBeHidden();

            await expect(page.getByText('Plain informational alert.')).toBeVisible();
        });

        test('a toast appears and dismisses itself', async ({ page }) => {
            await open(page, 'feedback', mode);

            await page.getByRole('button', { name: 'Show toast' }).click();

            const toast = page.getByText('Saved successfully');
            await expect(toast).toBeVisible();
            await expect(toast).toBeHidden({ timeout: 4000 });
        });

        test('a toast can be closed manually', async ({ page }) => {
            await open(page, 'feedback', mode);

            await page.getByRole('button', { name: 'Show sticky toast' }).click();
            const toast = page.getByText('Sticky toast');
            await expect(toast).toBeVisible();

            await page.locator('[aria-live=polite]').getByRole('button', { name: 'Close' }).click();
            await expect(toast).toBeHidden();
        });

        test('the toast stack sits on the logical end corner', async ({ page }) => {
            await open(page, 'feedback', mode);

            await page.getByRole('button', { name: 'Show sticky toast' }).click();
            const box = await page.getByText('Sticky toast').boundingBox();
            const viewport = page.viewportSize();

            if (mode.dir === 'ltr') {
                expect(box.x).toBeGreaterThan(viewport.width / 2);
            } else {
                expect(box.x + box.width).toBeLessThan(viewport.width / 2);
            }
        });

        test('the theme toggle switches the dark class and remembers the choice', async ({ page }) => {
            await open(page, 'feedback', { ...mode, dark: false });

            const toggle = page.getByRole('button', { name: 'Toggle dark mode' });

            await toggle.click();
            await expect(page.locator('html')).toHaveClass(/dark/);
            await expect(toggle).toHaveAttribute('aria-pressed', 'true');
            expect(await page.evaluate(() => localStorage.getItem('theme'))).toBe('dark');

            await toggle.click();
            await expect(page.locator('html')).not.toHaveClass(/dark/);
            expect(await page.evaluate(() => localStorage.getItem('theme'))).toBe('light');
        });
    });
}
