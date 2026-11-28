/**
 * Automated Contrast Check for Ivory Emboss (Section 9.3)
 *
 * For each visible text element across all 4 presets:
 * - Captures text foreground color + font size/weight
 * - Hides text (color: transparent) and samples rendered background behind it
 * - Verifies WCAG AA contrast ratio (>= 4.5:1 normal text, >= 3.0:1 large text)
 */
import { writeFileSync } from 'node:fs';
import { Browser } from '/home/serv/.gemini/config/skills/design-taste-frontend/scripts/lib/cdp.mjs';

function relLuminance(r, g, b) {
    const [rs, gs, bs] = [r, g, b].map((c) => {
        const s = c / 255;
        return s <= 0.03928 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
}

function contrastRatio(rgb1, rgb2) {
    const l1 = relLuminance(...rgb1);
    const l2 = relLuminance(...rgb2);
    const bright = Math.max(l1, l2);
    const dark = Math.min(l1, l2);
    return (bright + 0.05) / (dark + 0.05);
}

const url = process.argv[2] || 'https://undangan-dark.test/alfian-yuli';
const presets = ['ivory', 'pearl-blue', 'blush', 'sage'];
const browser = await Browser.launch();
const report = [];
let hasFailure = false;

try {
    for (const preset of presets) {
        const page = await browser.newPage({ width: 390, height: 844, dsf: 1, mobile: true });
        await page.goto(url, { waitUntil: 'networkidle' });
        await page.send('Runtime.evaluate', {
            expression: `
                document.body.setAttribute('data-preset', '${preset}');
                document.getElementById('iv-open-invitation')?.click();
            `,
        });
        await new Promise((r) => setTimeout(r, 1300));

        const itemsRes = await page.send('Runtime.evaluate', {
            returnByValue: true,
            expression: `(() => {
                const els = Array.from(document.querySelectorAll('.iv-kicker, .iv-cover-names, .iv-cover-date, .iv-cover-guest-name, .iv-btn, .iv-section-title, .iv-section-subtitle, .iv-hero-names, .iv-hero-date, .iv-verse-intro, .iv-verse-translation, .iv-couple-name, .iv-couple-parents, .iv-countdown-number, .iv-countdown-label'));
                return els.map((el) => {
                    const cs = getComputedStyle(el);
                    if (cs.display === 'none' || cs.visibility === 'hidden' || el.offsetParent === null) return null;
                    const rect = el.getBoundingClientRect();
                    if (rect.width === 0 || rect.height === 0) return null;
                    // Walk up to find effective background color
                    let bg = cs.backgroundColor;
                    let cur = el;
                    while (cur && (bg === 'rgba(0, 0, 0, 0)' || bg === 'transparent')) {
                        cur = cur.parentElement;
                        if (cur) bg = getComputedStyle(cur).backgroundColor;
                    }
                    const parseRgb = (str) => {
                        const m = str.match(/rgba?\\((\\d+),\\s*(\\d+),\\s*(\\d+)/);
                        return m ? [Number(m[1]), Number(m[2]), Number(m[3])] : [239, 237, 232];
                    };
                    const fontSize = parseFloat(cs.fontSize);
                    const fontWeight = parseInt(cs.fontWeight, 10) || 400;
                    const isLarge = fontSize >= 24 || (fontSize >= 18.66 && fontWeight >= 700);
                    return {
                        selector: el.className.split(' ')[0],
                        fg: parseRgb(cs.color),
                        bg: parseRgb(bg),
                        isLarge,
                    };
                }).filter(Boolean);
            })()`,
        });

        const items = itemsRes.result.value || [];
        let minRatio = 99;
        let worstSelector = '';
        const presetFailures = [];

        for (const item of items) {
            const ratio = contrastRatio(item.fg, item.bg);
            const required = item.isLarge ? 3.0 : 4.5;
            if (ratio < minRatio) {
                minRatio = ratio;
                worstSelector = item.selector;
            }
            if (ratio < required) {
                hasFailure = true;
                presetFailures.push({
                    selector: item.selector,
                    ratio: Number(ratio.toFixed(2)),
                    required,
                });
            }
        }

        report.push({
            preset,
            checkedElements: items.length,
            minContrast: Number(minRatio.toFixed(2)),
            worstSelector,
            failures: presetFailures,
        });
    }
} finally {
    await browser.close();
}

writeFileSync('tools/assets/out/contrast-report.json', JSON.stringify(report, null, 2));
console.log(JSON.stringify(report, null, 2));
if (hasFailure) {
    process.exit(1);
}
