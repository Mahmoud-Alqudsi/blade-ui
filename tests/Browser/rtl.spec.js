import { test, expect } from '@playwright/test';
import { open } from './helpers.js';

test.describe('logical layout (RTL / LTR)', () => {
    test('input icons sit on the logical start edge', async ({ page }) => {
        for (const dir of ['ltr', 'rtl']) {
            await open(page, 'forms', { dir });

            const field = await page.locator('#input-search').boundingBox();
            const icon = await page.locator('#input-search').locator('xpath=preceding-sibling::span[1]').locator('svg').boundingBox();

            if (dir === 'ltr') {
                expect(icon.x - field.x).toBeLessThan(24);
            } else {
                expect(field.x + field.width - (icon.x + icon.width)).toBeLessThan(24);
            }
        }
    });

    test('input text is padded away from the icon on the correct side', async ({ page }) => {
        for (const dir of ['ltr', 'rtl']) {
            await open(page, 'forms', { dir });

            const styles = await page.locator('#input-search').evaluate((element) => {
                const computed = getComputedStyle(element);
                return { left: parseFloat(computed.paddingLeft), right: parseFloat(computed.paddingRight) };
            });

            if (dir === 'ltr') {
                expect(styles.left).toBeGreaterThan(styles.right);
            } else {
                expect(styles.right).toBeGreaterThan(styles.left);
            }
        }
    });

    test('directional icons flip in RTL and stay put in LTR', async ({ page }) => {
        const rotation = () =>
            page.locator('nav[aria-label="Pagination navigation"] a[rel=next] svg').evaluate((svg) => getComputedStyle(svg).rotate);

        await open(page, 'gallery', { dir: 'ltr' });
        expect(await rotation()).toBe('none');

        await open(page, 'gallery', { dir: 'rtl' });
        expect(await rotation()).toBe('180deg');
    });

    test('the switch changes appearance when turned on in both directions', async ({ page }) => {
        for (const dir of ['ltr', 'rtl']) {
            await open(page, 'forms', { dir });

            const notify = page.getByRole('switch', { name: 'Notifications' });
            const track = notify.locator('xpath=following-sibling::span[1]');
            const background = () => track.evaluate((element) => getComputedStyle(element).backgroundColor);

            const off = await background();
            await page.getByText('Notifications', { exact: true }).click();
            await expect(notify).toBeChecked();

            expect(await background()).not.toBe(off);
        }
    });

    test('the page never overflows horizontally in either direction', async ({ page }) => {
        for (const dir of ['ltr', 'rtl']) {
            await open(page, 'gallery', { dir });

            const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
            expect(overflow).toBeLessThanOrEqual(1);
        }
    });
});
