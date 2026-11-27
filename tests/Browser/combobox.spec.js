import { test, expect } from '@playwright/test';
import { directions, open } from './helpers.js';

for (const mode of directions) {
    test.describe(`combobox · ${mode.name}`, () => {
        test('opens a listbox with all options and filters while typing', async ({ page }) => {
            await open(page, 'combobox', mode);

            const input = page.getByRole('combobox', { name: 'Country' });
            const options = page.getByRole('option');

            await expect(page.getByRole('listbox')).toBeHidden();
            await input.click();

            await expect(page.getByRole('listbox')).toBeVisible();
            await expect(input).toHaveAttribute('aria-expanded', 'true');
            await expect(options).toHaveCount(6);

            await input.fill('ar');
            await expect(options).toHaveText(['Saudi Arabia', 'United Arab Emirates']);
        });

        test('selects with the keyboard and submits the value through a hidden input', async ({ page }) => {
            await open(page, 'combobox', mode);

            const input = page.getByRole('combobox', { name: 'Country' });
            const hidden = page.locator('input[type=hidden][name=country]');

            await input.click();
            await page.keyboard.press('ArrowDown');
            await expect(page.getByRole('option').nth(1)).toHaveAttribute('aria-selected', 'false');
            await expect(input).toHaveAttribute('aria-activedescendant', /country-option-1$/);

            await page.keyboard.press('Enter');

            await expect(hidden).toHaveValue('ae');
            await expect(input).toHaveValue('United Arab Emirates');
            await expect(page.getByRole('listbox')).toBeHidden();
        });

        test('shows a status message instead of an invalid empty listbox', async ({ page }) => {
            await open(page, 'combobox', mode);

            const input = page.getByRole('combobox', { name: 'Country' });

            await input.fill('zzz');

            await expect(page.getByRole('status').filter({ hasText: 'No results found' })).toBeVisible();
            await expect(page.getByRole('listbox')).toBeHidden();
        });

        test('Escape and outside clicks close it and restore the selected label', async ({ page }) => {
            await open(page, 'combobox', mode);

            const input = page.getByRole('combobox', { name: 'Country' });

            await input.click();
            await page.getByRole('option', { name: 'Egypt' }).click();
            await expect(input).toHaveValue('Egypt');

            await input.click();
            await input.fill('mor');
            await page.keyboard.press('Escape');
            await expect(page.getByRole('listbox')).toBeHidden();
            await expect(input).toHaveValue('Egypt');

            await input.click();
            await page.locator('#outside').click();
            await expect(page.getByRole('listbox')).toBeHidden();
        });
    });
}
