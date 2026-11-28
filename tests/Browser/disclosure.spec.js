import { test, expect } from '@playwright/test';
import { directions, open } from './helpers.js';

for (const mode of directions) {
    test.describe(`tabs · ${mode.name}`, () => {
        test('clicking a tab shows its panel and updates ARIA state', async ({ page }) => {
            await open(page, 'disclosure', mode);

            const security = page.getByRole('tab', { name: 'Security' });

            await expect(page.getByRole('tabpanel')).toHaveText('Profile panel');
            await security.click();

            await expect(security).toHaveAttribute('aria-selected', 'true');
            await expect(page.getByRole('tab', { name: 'Profile' })).toHaveAttribute('aria-selected', 'false');
            await expect(page.getByRole('tabpanel')).toHaveText('Security panel');
        });

        test('arrow keys follow the reading direction; Home and End jump to the ends', async ({ page }) => {
            await open(page, 'disclosure', mode);

            const next = mode.dir === 'ltr' ? 'ArrowRight' : 'ArrowLeft';
            const previous = mode.dir === 'ltr' ? 'ArrowLeft' : 'ArrowRight';

            await page.getByRole('tab', { name: 'Profile' }).focus();

            await page.keyboard.press(next);
            await expect(page.getByRole('tab', { name: 'Security' })).toBeFocused();
            await expect(page.getByRole('tabpanel')).toHaveText('Security panel');

            await page.keyboard.press(previous);
            await expect(page.getByRole('tab', { name: 'Profile' })).toBeFocused();

            await page.keyboard.press('End');
            await expect(page.getByRole('tab', { name: 'Billing' })).toBeFocused();

            await page.keyboard.press('Home');
            await expect(page.getByRole('tab', { name: 'Profile' })).toBeFocused();
        });
    });

    test.describe(`accordion · ${mode.name}`, () => {
        test('single mode keeps one item open', async ({ page }) => {
            await open(page, 'disclosure', mode);

            const one = page.getByRole('button', { name: 'Question one' });
            const two = page.getByRole('button', { name: 'Question two' });

            await expect(one).toHaveAttribute('aria-expanded', 'false');
            await one.click();
            await expect(one).toHaveAttribute('aria-expanded', 'true');
            await expect(page.getByText('Answer one', { exact: true })).toBeVisible();

            await two.click();
            await expect(two).toHaveAttribute('aria-expanded', 'true');
            await expect(one).toHaveAttribute('aria-expanded', 'false');
            await expect(page.getByText('Answer one', { exact: true })).toBeHidden();
        });

        test('multiple mode allows several items open and toggling closed', async ({ page }) => {
            await open(page, 'disclosure', mode);

            const one = page.getByRole('button', { name: 'Multi one' });
            const two = page.getByRole('button', { name: 'Multi two' });

            await one.click();
            await two.click();
            await expect(page.getByText('Multi answer one')).toBeVisible();
            await expect(page.getByText('Multi answer two')).toBeVisible();

            await one.click();
            await expect(page.getByText('Multi answer one')).toBeHidden();
            await expect(page.getByText('Multi answer two')).toBeVisible();
        });
    });
}
