import { expect } from '@playwright/test';

/** Test modes: page direction × light/dark theme. */
export const modes = [
    { name: 'ltr · light', dir: 'ltr', dark: false },
    { name: 'rtl · light', dir: 'rtl', dark: false },
    { name: 'ltr · dark', dir: 'ltr', dark: true },
    { name: 'rtl · dark', dir: 'rtl', dark: true },
];

export const directions = modes.filter((mode) => !mode.dark);

/** Opens a generated fixture page, waits for Alpine readiness, and applies direction and theme. */
export async function open(page, name, { dir = 'ltr', dark = false } = {}) {
    await page.goto(`/${name}.html`);
    await page.waitForFunction(() => document.documentElement.dataset.alpine === 'ready');
    await page.evaluate(
        ({ dir, dark }) => {
            document.documentElement.dir = dir;
            document.documentElement.classList.toggle('dark', dark);
        },
        { dir, dark },
    );
    await expect(page.locator('html')).toHaveAttribute('dir', dir);
}

/** Checks whether the active element is inside the given container selector. */
export const focusIsWithin = (page, selector) =>
    page.evaluate((css) => !!document.activeElement?.closest(css), selector);
