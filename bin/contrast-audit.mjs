#!/usr/bin/env node
// WCAG 2.x color contrast audit for color pairs used across package components.
// Values are converted from the Tailwind v4 palette (oklch) to sRGB; results are approximate (±0.1), so rely on axe for final verification.
// Run: npm run audit:contrast — exits with code 1 if text drops below 4.5:1 or non-text elements drop below 3:1.

const P = {
    slate: { 50: [.984, .003, 247.858], 100: [.968, .007, 247.896], 200: [.929, .013, 255.508], 300: [.869, .022, 252.894], 400: [.704, .04, 256.788], 500: [.554, .046, 257.417], 600: [.446, .043, 257.281], 700: [.372, .044, 257.287], 800: [.279, .041, 260.031], 900: [.208, .042, 265.755] },
    emerald: { 50: [.979, .021, 166.113], 100: [.95, .052, 163.051], 300: [.845, .143, 164.978], 400: [.765, .177, 163.223], 500: [.696, .17, 162.48], 600: [.596, .145, 163.225], 700: [.508, .118, 165.612], 800: [.432, .095, 166.913], 900: [.378, .077, 168.94] },
    red: { 50: [.971, .013, 17.38], 100: [.936, .032, 17.717], 300: [.808, .114, 19.571], 400: [.704, .191, 22.216], 500: [.637, .237, 25.331], 600: [.577, .245, 27.325], 700: [.505, .213, 27.518], 800: [.444, .177, 26.899], 900: [.396, .141, 25.723] },
    amber: { 50: [.987, .022, 95.277], 100: [.962, .059, 95.617], 300: [.879, .169, 91.605], 500: [.769, .188, 70.08], 600: [.666, .179, 58.318], 700: [.555, .163, 48.4], 800: [.473, .137, 46.201], 900: [.414, .112, 45.904] },
    sky: { 50: [.977, .013, 236.62], 100: [.951, .026, 236.824], 300: [.828, .111, 230.318], 500: [.685, .169, 237.323], 600: [.588, .158, 241.966], 700: [.5, .134, 242.749], 900: [.391, .09, 240.876] },
};
const BRAND = { 50: '#eff6ff', 100: '#dbeafe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' };

const hex = (value) => [1, 3, 5].map((index) => parseInt(value.slice(index, index + 2), 16) / 255);

function oklch([L, C, H]) {
    const h = (H * Math.PI) / 180, a = C * Math.cos(h), b = C * Math.sin(h);
    const l = (L + 0.3963377774 * a + 0.2158037573 * b) ** 3;
    const m = (L - 0.1055613458 * a - 0.0638541728 * b) ** 3;
    const s = (L - 0.0894841775 * a - 1.291485548 * b) ** 3;
    const linear = [4.0767416621 * l - 3.3077115913 * m + 0.2309699292 * s, -1.2684380046 * l + 2.6097574011 * m - 0.3413193965 * s, -0.0041960863 * l - 0.7034186147 * m + 1.707614701 * s];
    const gamma = (x) => { x = Math.min(1, Math.max(0, x)); return x <= 0.0031308 ? 12.92 * x : 1.055 * x ** (1 / 2.4) - 0.055; };
    return linear.map(gamma);
}

const color = (name) => {
    if (name === 'white') return [1, 1, 1];
    const [family, shade] = name.split('-');
    return family === 'brand' ? hex(BRAND[shade]) : oklch(P[family][shade]);
};
const parse = (spec) => { const [name, alpha] = spec.split('/'); return { name, alpha: alpha ? Number(alpha) / 100 : 1 }; };
const mix = (top, alpha, bottom) => top.map((value, index) => value * alpha + bottom[index] * (1 - alpha));
const luminance = (rgb) => { const [r, g, b] = rgb.map((v) => (v <= 0.04045 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4)); return 0.2126 * r + 0.7152 * g + 0.0722 * b; };
const ratio = (a, b) => { const [hi, lo] = [luminance(a), luminance(b)].sort((x, y) => y - x); return (hi + 0.05) / (lo + 0.05); };

const LIGHT = color('white'), DARK = color('slate-900');
const contrast = (fg, bg, base) => {
    const back = parse(bg), front = parse(fg);
    const surface = mix(color(back.name), back.alpha, base);
    return ratio(mix(color(front.name), front.alpha, surface), surface);
};

// [description, foreground, background, theme base]
const TEXT = [
    ['button primary', 'white', 'brand-600', LIGHT], ['button primary hover', 'white', 'brand-700', LIGHT],
    ['button success', 'white', 'emerald-700', LIGHT], ['button success hover', 'white', 'emerald-800', LIGHT],
    ['button danger', 'white', 'red-600', LIGHT], ['button danger hover', 'white', 'red-700', LIGHT],
    ['button secondary', 'slate-900', 'slate-100', LIGHT], ['button secondary (dark)', 'slate-100', 'slate-800', DARK],
    ['outline / ghost', 'slate-700', 'white', LIGHT], ['outline / ghost (dark)', 'slate-200', 'slate-900', DARK],
    ['badge brand', 'brand-700', 'brand-50', LIGHT], ['badge success', 'emerald-700', 'emerald-50', LIGHT], ['badge warning', 'amber-800', 'amber-50', LIGHT],
    ['badge danger', 'red-700', 'red-50', LIGHT], ['badge info', 'sky-700', 'sky-50', LIGHT], ['badge neutral', 'slate-700', 'slate-100', LIGHT],
    ['badge brand (dark)', 'brand-300', 'brand-500/10', DARK], ['badge success (dark)', 'emerald-300', 'emerald-500/10', DARK],
    ['badge warning (dark)', 'amber-300', 'amber-500/10', DARK], ['badge danger (dark)', 'red-300', 'red-500/10', DARK],
    ['badge info (dark)', 'sky-300', 'sky-500/10', DARK], ['badge neutral (dark)', 'slate-300', 'slate-800', DARK],
    ['alert success', 'emerald-900', 'emerald-50', LIGHT], ['alert warning', 'amber-900', 'amber-50', LIGHT], ['alert danger', 'red-900', 'red-50', LIGHT], ['alert info', 'sky-900', 'sky-50', LIGHT],
    ['alert success (dark)', 'emerald-100', 'emerald-500/10', DARK], ['alert warning (dark)', 'amber-100', 'amber-500/10', DARK], ['alert danger (dark)', 'red-100', 'red-500/10', DARK], ['alert info (dark)', 'sky-100', 'sky-500/10', DARK],
    ['muted / hint text', 'slate-500', 'white', LIGHT], ['muted on card footer', 'slate-500', 'slate-50', LIGHT], ['muted (dark)', 'slate-400', 'slate-900', DARK],
    ['placeholder / empty value', 'slate-500', 'white', LIGHT], ['placeholder (dark)', 'slate-400', 'slate-900', DARK],
    ['error text', 'red-600', 'white', LIGHT], ['error text (dark)', 'red-400', 'slate-900', DARK],
    ['stat trend up', 'emerald-700', 'white', LIGHT], ['stat trend up (dark)', 'emerald-400', 'slate-900', DARK], ['stat trend down', 'red-600', 'white', LIGHT],
    ['link', 'brand-600', 'white', LIGHT], ['link on footer', 'brand-600', 'slate-50', LIGHT], ['link (dark)', 'brand-400', 'slate-900', DARK],
    ['tab active', 'brand-700', 'white', LIGHT], ['tab inactive', 'slate-500', 'white', LIGHT], ['tab active (dark)', 'brand-300', 'slate-900', DARK], ['tab inactive (dark)', 'slate-400', 'slate-900', DARK],
    ['pagination current', 'white', 'brand-600', LIGHT], ['pagination link', 'slate-700', 'white', LIGHT],
    ['steps upcoming', 'slate-500', 'white', LIGHT], ['steps upcoming (dark)', 'slate-400', 'slate-900', DARK],
    ['combobox active option', 'brand-700', 'brand-50', LIGHT], ['combobox active (dark)', 'brand-300', 'brand-500/10', DARK],
    ['date weekday header', 'slate-500', 'white', LIGHT], ['table head', 'slate-600', 'slate-50', LIGHT], ['table head (dark)', 'slate-300', 'slate-800/60', DARK],
    ['avatar initials', 'brand-700', 'brand-100', LIGHT], ['avatar initials (dark)', 'brand-300', 'brand-500/20', DARK],
    ['tooltip', 'white', 'slate-900', LIGHT], ['tooltip (dark)', 'white', 'slate-700', DARK],
    ['dropdown danger item', 'red-600', 'white', LIGHT], ['dropdown danger (dark)', 'red-400', 'slate-900', DARK],
];

const NON_TEXT = [
    ['progress brand / track', 'brand-600', 'slate-200', LIGHT], ['progress success / track', 'emerald-700', 'slate-200', LIGHT],
    ['progress warning / track', 'amber-700', 'slate-200', LIGHT], ['progress danger / track', 'red-600', 'slate-200', LIGHT],
    ['progress brand (dark)', 'brand-500', 'slate-800', DARK], ['progress success (dark)', 'emerald-500', 'slate-800', DARK],
    ['progress warning (dark)', 'amber-500', 'slate-800', DARK], ['progress danger (dark)', 'red-500', 'slate-800', DARK],
    ['alert icon success', 'emerald-600', 'emerald-50', LIGHT], ['alert icon warning', 'amber-600', 'amber-50', LIGHT], ['alert icon info', 'sky-600', 'sky-50', LIGHT],
    ['focus ring', 'brand-500', 'white', LIGHT], ['focus ring (dark)', 'brand-500', 'slate-900', DARK],
];

// Documented design decision: input borders are lighter than 3:1 (see README -> Accessibility notes)
const ADVISORY = [
    ['input border', 'slate-300', 'white', LIGHT], ['input border (dark)', 'slate-700', 'slate-900', DARK],
];

let failures = 0;
const run = (title, rows, minimum, blocking) => {
    console.log(`\n${title} (minimum ${minimum}:1)`);
    for (const [name, fg, bg, base] of rows) {
        const value = contrast(fg, bg, base);
        const low = value < minimum;
        if (low && blocking) failures++;
        console.log(`${low ? (blocking ? 'FAIL' : 'note') : 'ok  '} ${value.toFixed(2).padStart(5)}  ${name}  (${fg} on ${bg})`);
    }
};

run('Text', TEXT, 4.5, true);
run('Non-text elements', NON_TEXT, 3, true);
run('Design notes (non-blocking)', ADVISORY, 3, false);

console.log(failures ? `\n${failures} pair(s) below threshold.` : '\nAll color pairs meet minimum thresholds.');
process.exit(failures ? 1 : 0);
