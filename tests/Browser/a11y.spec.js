import AxeBuilder from '@axe-core/playwright';
import { test, expect } from '@playwright/test';
import { modes, open } from './helpers.js';

/** Run WCAG 2.1 A/AA checks; fail on serious and critical violations and log advisory findings. */
async function scan(page) {
    const results = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
        .analyze();

    const blocking = results.violations.filter((violation) => ['serious', 'critical'].includes(violation.impact));
    const advisory = results.violations.filter((violation) => !blocking.includes(violation));

    if (advisory.length) {
        console.log('axe advisory:', advisory.map((v) => `${v.id} (${v.impact})`).join(', '));
    }

    return blocking.map((violation) => ({
        rule: violation.id,
        impact: violation.impact,
        help: violation.help,
        targets: violation.nodes.slice(0, 5).map((node) => node.target.join(' ')),
    }));
}

const scenarios = [
    { name: 'gallery', page: 'gallery' },
    { name: 'forms', page: 'forms' },
    { name: 'disclosure', page: 'disclosure' },
    { name: 'feedback', page: 'feedback' },
    {
        name: 'modal (open)',
        page: 'modal',
        setup: async (page) => {
            await page.getByRole('button', { name: 'Open modal' }).click();
            await expect(page.getByRole('dialog')).toBeVisible();
        },
    },
    {
        name: 'dropdown (open)',
        page: 'dropdown',
        setup: async (page) => {
            await page.getByRole('button', { name: 'Account' }).click();
            await expect(page.getByRole('menu')).toBeVisible();
        },
    },
    {
        name: 'combobox (open)',
        page: 'combobox',
        setup: async (page) => {
            await page.getByRole('combobox', { name: 'Country' }).click();
            await expect(page.getByRole('listbox')).toBeVisible();
        },
    },
    {
        name: 'combobox (no results)',
        page: 'combobox',
        setup: async (page) => {
            await page.getByRole('combobox', { name: 'Country' }).fill('zzz');
            await expect(page.getByRole('status').filter({ hasText: 'No results found' })).toBeVisible();
        },
    },
    {
        name: 'date picker (open)',
        page: 'date-picker',
        setup: async (page) => {
            await page.locator('#date-due').click();
            await expect(page.getByRole('dialog')).toBeVisible();
        },
    },
    {
        name: 'drawer (open)',
        page: 'drawer',
        setup: async (page) => {
            await page.getByRole('button', { name: 'Open end drawer' }).click();
            await expect(page.getByRole('dialog')).toBeVisible();
            await page.waitForTimeout(350);
        },
    },
    {
        name: 'toast (visible)',
        page: 'feedback',
        setup: async (page) => {
            await page.getByRole('button', { name: 'Show sticky toast' }).click();
            await expect(page.getByText('Sticky toast', { exact: true })).toBeVisible();
        },
    },
];

for (const scenario of scenarios) {
    for (const mode of modes) {
        test(`axe · ${scenario.name} · ${mode.name}`, async ({ page }) => {
            await open(page, scenario.page, mode);
            await scenario.setup?.(page);

            expect(await scan(page), 'serious/critical accessibility violations').toEqual([]);
        });
    }
}
