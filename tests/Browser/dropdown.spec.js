import { test, expect } from '@playwright/test';
import { directions, open } from './helpers.js';

for (const mode of directions) {
    test.describe(`dropdown · ${mode.name}`, () => {
        test('opens, moves focus with arrows (wrapping) and closes with Escape', async ({ page }) => {
            await open(page, 'dropdown', mode);

            const trigger = page.getByRole('button', { name: 'Account' });
            const menu = page.getByRole('menu');
            const item = (name) => page.getByRole('menuitem', { name });

            await expect(menu).toBeHidden();
            await trigger.click();

            await expect(menu).toBeVisible();
            await expect(trigger).toHaveAttribute('aria-expanded', 'true');
            await expect(trigger).toHaveAttribute('aria-haspopup', 'menu');
            await expect(item('Profile')).toBeFocused();

            await page.keyboard.press('ArrowDown');
            await expect(item('Settings')).toBeFocused();
            await page.keyboard.press('ArrowDown');
            await expect(item('Sign out')).toBeFocused();
            await page.keyboard.press('ArrowDown');
            await expect(item('Profile')).toBeFocused();
            await page.keyboard.press('ArrowUp');
            await expect(item('Sign out')).toBeFocused();

            await page.keyboard.press('Escape');
            await expect(menu).toBeHidden();
            await expect(trigger).toBeFocused();
            await expect(trigger).toHaveAttribute('aria-expanded', 'false');
        });

        test('ArrowDown on the trigger opens the menu', async ({ page }) => {
            await open(page, 'dropdown', mode);

            await page.getByRole('button', { name: 'Account' }).focus();
            await page.keyboard.press('ArrowDown');

            await expect(page.getByRole('menu')).toBeVisible();
        });

        test('closes on an outside click and after choosing an item', async ({ page }) => {
            await open(page, 'dropdown', mode);

            const trigger = page.getByRole('button', { name: 'Account' });
            const menu = page.getByRole('menu');

            await trigger.click();
            await expect(menu).toBeVisible();
            await page.locator('#outside').click();
            await expect(menu).toBeHidden();

            await trigger.click();
            await page.getByRole('menuitem', { name: 'Settings' }).click();
            await expect(menu).toBeHidden();
        });

        test('the panel aligns to the logical start edge of the trigger', async ({ page }) => {
            await open(page, 'dropdown', mode);

            const trigger = page.getByRole('button', { name: 'Account' });
            await trigger.click();
            await expect(page.getByRole('menu')).toBeVisible();
            await page.waitForTimeout(150);

            const triggerBox = await trigger.boundingBox();
            const panelBox = await page.getByRole('menu').boundingBox();

            if (mode.dir === 'ltr') {
                expect(Math.abs(panelBox.x - triggerBox.x)).toBeLessThan(2);
            } else {
                expect(Math.abs(panelBox.x + panelBox.width - (triggerBox.x + triggerBox.width))).toBeLessThan(2);
            }
        });
    });
}
