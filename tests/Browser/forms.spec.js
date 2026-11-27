import { test, expect } from '@playwright/test';
import { directions, open } from './helpers.js';

for (const mode of directions) {
    test.describe(`forms · ${mode.name}`, () => {
        test('the password field reveals and hides its value', async ({ page }) => {
            await open(page, 'forms', mode);

            const field = page.locator('#input-password');
            const toggle = page.getByRole('button', { name: 'Show password' });

            await expect(field).toHaveAttribute('type', 'password');
            await toggle.click();
            await expect(field).toHaveAttribute('type', 'text');

            const hide = page.getByRole('button', { name: 'Hide password' });
            await expect(hide).toHaveAttribute('aria-pressed', 'true');
            await hide.click();
            await expect(field).toHaveAttribute('type', 'password');
        });

        test('the switch toggles with the mouse and the Space key', async ({ page }) => {
            await open(page, 'forms', mode);

            const notify = page.getByRole('switch', { name: 'Notifications' });

            await expect(notify).not.toBeChecked();
            await page.getByText('Notifications', { exact: true }).click();
            await expect(notify).toBeChecked();

            await notify.focus();
            await page.keyboard.press('Space');
            await expect(notify).not.toBeChecked();
        });

        test('checkbox and radio are operable from their labels', async ({ page }) => {
            await open(page, 'forms', mode);

            await page.getByText('I agree to the terms').click();
            await expect(page.getByRole('checkbox', { name: 'I agree to the terms' })).toBeChecked();

            await page.getByText('Pro plan').click();
            await expect(page.getByRole('radio', { name: 'Pro plan' })).toBeChecked();
        });

        test('a field with an error exposes it to assistive technology', async ({ page }) => {
            await open(page, 'forms', mode);

            const email = page.getByRole('textbox', { name: /Email/ });

            await expect(email).toHaveAttribute('aria-invalid', 'true');
            const describedBy = await email.getAttribute('aria-describedby');
            expect(describedBy).toContain('input-email-error');
            await expect(page.locator('#input-email-error')).toHaveText('The email address is invalid.');
        });

        test('the select shows its options', async ({ page }) => {
            await open(page, 'forms', mode);

            const role = page.getByRole('combobox', { name: 'Role' });

            await role.selectOption('editor');
            await expect(role).toHaveValue('editor');
        });

        test('the tooltip shows on hover and focus and hides with Escape', async ({ page }) => {
            await open(page, 'forms', mode);

            const trigger = page.getByRole('button', { name: 'Copy' });
            const tooltip = page.getByRole('tooltip');

            await expect(tooltip).toBeHidden();
            await trigger.hover();
            await expect(tooltip).toBeVisible();
            await expect(tooltip).toHaveText('Copy the link');

            await page.mouse.move(0, 0);
            await expect(tooltip).toBeHidden();

            await trigger.focus();
            await expect(tooltip).toBeVisible();
            await page.keyboard.press('Escape');
            await expect(tooltip).toBeHidden();
        });
    });
}
