import { test, expect } from '@playwright/test';
import { directions, open } from './helpers.js';

for (const mode of directions) {
    test.describe(`date picker · ${mode.name}`, () => {
        test('opens a calendar for the selected month starting on the configured weekday', async ({ page }) => {
            await open(page, 'date-picker', mode);

            const trigger = page.locator('#date-due');
            const dialog = page.getByRole('dialog', { name: 'Due date' });

            await expect(dialog).toBeHidden();
            await expect(trigger).toContainText('November 19, 2026');
            await trigger.click();

            await expect(dialog).toBeVisible();
            await expect(dialog.getByText('November 2026')).toBeVisible();
            await expect(dialog.locator('.grid-cols-7').first().locator('span').first()).toHaveText('Sat');
        });

        test('disables days outside min and max', async ({ page }) => {
            await open(page, 'date-picker', mode);

            await page.locator('#date-due').click();
            const dialog = page.getByRole('dialog', { name: 'Due date' });

            await expect(dialog.getByRole('button', { name: /November 9, 2026/ })).toBeDisabled();
            await expect(dialog.getByRole('button', { name: /November 10, 2026/ })).toBeEnabled();
            await expect(dialog.getByRole('button', { name: /November 25, 2026/ })).toBeEnabled();
            await expect(dialog.getByRole('button', { name: /November 26, 2026/ })).toBeDisabled();
        });

        test('picking a day updates the hidden ISO value, fires events and restores focus', async ({ page }) => {
            await open(page, 'date-picker', mode);

            const trigger = page.locator('#date-due');
            const hidden = page.locator('input[type=hidden][name=due]');
            const dialog = page.getByRole('dialog', { name: 'Due date' });

            await hidden.evaluate((element) => {
                window.__changes = 0;
                element.addEventListener('change', () => window.__changes++);
            });

            await trigger.click();
            await dialog.getByRole('button', { name: /November 12, 2026/ }).click();

            await expect(dialog).toBeHidden();
            await expect(hidden).toHaveValue('2026-11-12');
            await expect(trigger).toContainText('November 12, 2026');
            await expect(trigger).toBeFocused();
            await expect.poll(() => page.evaluate(() => window.__changes)).toBeGreaterThan(0);

            await trigger.click();
            await expect(dialog.getByRole('button', { name: /November 12, 2026/ })).toHaveAttribute('aria-pressed', 'true');
        });

        test('navigates between months', async ({ page }) => {
            await open(page, 'date-picker', mode);

            await page.locator('#date-due').click();
            const dialog = page.getByRole('dialog', { name: 'Due date' });

            await dialog.getByRole('button', { name: 'Next month' }).click();
            await expect(dialog.getByText('December 2026')).toBeVisible();

            await dialog.getByRole('button', { name: 'Previous month' }).click();
            await dialog.getByRole('button', { name: 'Previous month' }).click();
            await expect(dialog.getByText('October 2026')).toBeVisible();
        });

        test('Today and Clear buttons work; Clear is absent when required', async ({ page }) => {
            await open(page, 'date-picker', mode);

            const hidden = page.locator('input[type=hidden][name=start]');
            const expectedToday = await page.evaluate(() => {
                const date = new Date();
                const pad = (number) => String(number).padStart(2, '0');
                return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
            });

            await page.locator('#date-start').click();
            const dialog = page.getByRole('dialog', { name: 'Start date' });
            await dialog.getByRole('button', { name: 'Today' }).click();
            await expect(hidden).toHaveValue(expectedToday);

            await page.locator('#date-start').click();
            await dialog.getByRole('button', { name: 'Clear' }).click();
            await expect(hidden).toHaveValue('');

            await page.locator('#date-required_date').click();
            const required = page.getByRole('dialog', { name: 'Required date' });
            await expect(required.getByRole('button', { name: 'Today' })).toBeVisible();
            await expect(required.getByRole('button', { name: 'Clear' })).toHaveCount(0);
        });

        test('Escape closes the calendar and returns focus to the trigger', async ({ page }) => {
            await open(page, 'date-picker', mode);

            const trigger = page.locator('#date-due');
            await trigger.click();
            await expect(page.getByRole('dialog', { name: 'Due date' })).toBeVisible();

            await page.keyboard.press('Escape');

            await expect(page.getByRole('dialog', { name: 'Due date' })).toBeHidden();
            await expect(trigger).toBeFocused();
        });
    });
}
