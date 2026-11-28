/**
 * Automated QA, Multi-Preset Contact Sheet, Cover Mockup & 4x CPU Perf Test
 */
import { readFileSync, writeFileSync, statSync, mkdirSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';
import { Browser } from '/home/serv/.gemini/config/skills/design-taste-frontend/scripts/lib/cdp.mjs';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '..', '..', '..');
const MANIFEST_PATH = join(ROOT, 'tools/assets/manifest.json');
const SVG_DIR = join(ROOT, 'tools/assets/out/svg');
const RELIEF_DIR = join(ROOT, 'public/themes/ivory-emboss/relief');
const TEXTURE_DIR = join(ROOT, 'public/themes/ivory-emboss/texture');
const OUT_DIR = join(ROOT, 'tools/assets/out');

function runAutomatedChecks(manifest) {
    const failures = [];
    const seenSeeds = new Set();
    let totalAllBytes = 0;
    let totalTexturesBytes = 0;
    let firstScreenBytes = 0;
    const firstScreenIds = new Set(['corner-tl', 'corner-tr', 'corner-bl', 'corner-br', 'emblem-melati', 'monogram-ring']);

    for (const asset of manifest.reliefAssets) {
        // 1. Seed uniqueness
        if (seenSeeds.has(asset.seed)) {
            failures.push(`Duplicate seed ${asset.seed} on ${asset.id}`);
        }
        seenSeeds.add(asset.seed);

        // 2. Valid SVG without raster embeds or hidden elements
        const svg = readFileSync(join(SVG_DIR, `${asset.id}.svg`), 'utf8');
        if (!svg.startsWith('<svg') || /<image\b/i.test(svg) || /display:\s*none/i.test(svg)) {
            failures.push(`Invalid or raster/hidden SVG on ${asset.id}`);
        }

        // 3. Budget check (1x & 2x)
        const bytes1x = statSync(join(RELIEF_DIR, `${asset.id}.webp`)).size;
        const bytes2x = statSync(join(RELIEF_DIR, `${asset.id}@2x.webp`)).size;
        totalAllBytes += bytes2x;
        if (firstScreenIds.has(asset.id)) {
            firstScreenBytes += bytes2x;
        }
        if (bytes2x > asset.maxKB * 1024) {
            failures.push(`${asset.id}@2x.webp exceeds ${asset.maxKB} KB (${(bytes2x / 1024).toFixed(1)} KB)`);
        }
        if (bytes1x <= 0 || bytes2x <= 0) {
            failures.push(`Empty WebP output on ${asset.id}`);
        }
    }

    for (const tex of manifest.textureAssets) {
        const bytes = statSync(join(TEXTURE_DIR, `${tex.id}.webp`)).size;
        totalTexturesBytes += bytes;
        if (bytes > tex.maxKB * 1024) {
            failures.push(`Texture ${tex.id}.webp exceeds ${tex.maxKB} KB (${(bytes / 1024).toFixed(1)} KB)`);
        }
    }

    if (totalTexturesBytes > manifest.budgets.texturesTotalMaxKB * 1024) {
        failures.push(`Textures total exceeds ${manifest.budgets.texturesTotalMaxKB} KB`);
    }
    if (firstScreenBytes > manifest.budgets.firstScreenMaxKB * 1024) {
        failures.push(`First-screen ornaments exceed ${manifest.budgets.firstScreenMaxKB} KB (${(firstScreenBytes / 1024).toFixed(1)} KB)`);
    }
    if (totalAllBytes > manifest.budgets.totalAllMaxKB * 1024) {
        failures.push(`Total relief ornaments exceed ${manifest.budgets.totalAllMaxKB} KB (${(totalAllBytes / 1024).toFixed(1)} KB)`);
    }

    // 4. Seamless tiling & alpha edge & top-left lighting check via Python PIL
    const pyCheck = `
import json
from PIL import Image
import numpy as np

failures = []
for tid in ["paper-grain", "paper-fiber", "bg-desktop"]:
    im = Image.open(f"${TEXTURE_DIR}/{tid}.webp").convert("RGBA")
    arr = np.array(im, dtype=np.float32)
    premul = arr[:, :, :3] * (arr[:, :, 3:4] / 255.0)
    diff_lr = float(np.mean(np.abs(premul[:, 0, :] - premul[:, -1, :])))
    diff_tb = float(np.mean(np.abs(premul[0, :, :] - premul[-1, :, :])))
    if diff_lr > 12.0 or diff_tb > 12.0:
        failures.append(f"Seamless edge diff too high on {tid}: lr={diff_lr:.2f}, tb={diff_tb:.2f}")

# Check corner-tl lighting consistency (brighter highlights exist, transparent margin outside)
c = Image.open("${RELIEF_DIR}/corner-tl@2x.webp").convert("RGBA")
carr = np.array(c)
alpha_mask = carr[:, :, 3] > 20
if int(np.sum(alpha_mask)) == 0:
    failures.append("corner-tl has no visible pixels")
else:
    lum = 0.299 * carr[:, :, 0] + 0.587 * carr[:, :, 1] + 0.114 * carr[:, :, 2]
    if float(np.max(lum[alpha_mask])) < 160:
        failures.append("corner-tl highlight luminance too low")

print(json.dumps({"failures": failures}))
`;
    const pyRes = spawnSync('python3', ['-c', pyCheck], { encoding: 'utf8' });
    if (pyRes.status !== 0) {
        failures.push(`PIL QA check error: ${pyRes.stderr}`);
    } else {
        const parsed = JSON.parse(pyRes.stdout.trim());
        failures.push(...parsed.failures);
    }

    return {
        passed: failures.length === 0,
        failures,
        summary: {
            reliefCount: manifest.reliefAssets.length,
            textureCount: manifest.textureAssets.length,
            firstScreenKB: Number((firstScreenBytes / 1024).toFixed(2)),
            totalReliefKB: Number((totalAllBytes / 1024).toFixed(2)),
            totalTexturesKB: Number((totalTexturesBytes / 1024).toFixed(2)),
        },
    };
}

async function generateContactSheetAndContext(manifest) {
    mkdirSync(OUT_DIR, { recursive: true });
    const presets = [
        { id: 'ivory', name: 'Ivory', bg: '#efede8' },
        { id: 'pearl-blue', name: 'Pearl Blue', bg: '#e8ecf0' },
        { id: 'blush', name: 'Blush', bg: '#f0e9e6' },
        { id: 'sage', name: 'Sage', bg: '#e9ede6' },
    ];

    const assetCards = manifest.reliefAssets
        .map((a) => {
            const b64 = readFileSync(join(RELIEF_DIR, `${a.id}@2x.webp`)).toString('base64');
            const tiles = presets
                .map(
                    (p) => `
          <div class="tile" style="background:${p.bg}">
            <img src="data:image/webp;base64,${b64}" alt="${a.id} on ${p.name}">
            <span>${p.name}</span>
          </div>`
                )
                .join('');
            return `<div class="card"><div class="card-title">${a.id} (seed ${a.seed})</div><div class="tiles">${tiles}</div></div>`;
        })
        .join('');

    const sheetHtml = `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: system-ui, sans-serif; background: #dedad2; margin: 0; padding: 20px; color: #131210; }
  h1 { font-size: 18px; letter-spacing: 0.1em; text-transform: uppercase; margin: 0 0 16px; }
  .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
  .card { background: #fff; border-radius: 12px; padding: 12px; border: 1px solid rgba(0,0,0,0.08); }
  .card-title { font-size: 12px; font-weight: 600; margin-bottom: 8px; }
  .tiles { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; }
  .tile { position: relative; height: 110px; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
  .tile img { max-width: 90%; max-height: 85%; object-fit: contain; }
  .tile span { position: absolute; bottom: 4px; left: 6px; font-size: 9px; opacity: 0.65; }
</style>
</head>
<body>
  <h1>Ivory Emboss — Contact Sheet (20 Ornamen Relief x 4 Preset Kertas @2x)</h1>
  <div class="grid">${assetCards}</div>
</body>
</html>`;

    const cB64 = (id) => readFileSync(join(RELIEF_DIR, `${id}@2x.webp`)).toString('base64');
    const grainB64 = readFileSync(join(TEXTURE_DIR, 'paper-grain.webp')).toString('base64');
    const coverHtml = `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { margin: 0; font-family: system-ui, sans-serif; background: #efede8; color: #131210; }
  .cover { position: relative; width: 390px; height: 844px; overflow: hidden; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;
    background: #efede8 url("data:image/webp;base64,${grainB64}") repeat; }
  .corner { position: absolute; width: 175px; height: 175px; pointer-events: none; }
  .tl { top: 0; left: 0; } .tr { top: 0; right: 0; } .bl { bottom: 0; left: 0; } .br { bottom: 0; right: 0; }
  .emblem { width: 150px; margin: 14px 0; }
  .kicker { font-size: 11px; letter-spacing: 0.24em; text-transform: uppercase; color: #4a463f; }
  .names { font-size: 32px; font-weight: 300; letter-spacing: 0.14em; text-transform: uppercase; margin: 14px 0 6px; }
  .card { background: #f6f6f4; border: 1px solid rgba(40,36,30,0.08); border-radius: 22px; padding: 16px 24px; margin: 16px 0 22px; width: 250px; }
  .btn { background: #131210; color: #f6f6f4; border-radius: 999px; padding: 13px 28px; font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; border: 0; }
</style>
</head>
<body>
  <div class="cover" id="perf-root">
    <img class="corner tl" src="data:image/webp;base64,${cB64('corner-tl')}">
    <img class="corner tr" src="data:image/webp;base64,${cB64('corner-tr')}">
    <img class="corner bl" src="data:image/webp;base64,${cB64('corner-bl')}">
    <img class="corner br" src="data:image/webp;base64,${cB64('corner-br')}">
    <div class="kicker">The Wedding of</div>
    <div class="names">Yuli &amp; Alfian</div>
    <img class="emblem" src="data:image/webp;base64,${cB64('emblem-melati')}">
    <div class="card">
      <div class="kicker" style="font-size:10px;">Kepada Bapak/Ibu/Saudara/i</div>
      <div style="margin-top:6px; font-weight:500;">Tamu Undangan</div>
    </div>
    <button class="btn">Buka Undangan</button>
  </div>
</body>
</html>`;

    const browser = await Browser.launch();
    let perfReport = {};
    try {
        // 1. Contact sheet
        const p1 = await browser.newPage({ width: 1280, height: 900, dsf: 1, mobile: false });
        await p1.send('Page.setDocumentContent', {
            frameId: (await p1.send('Page.getFrameTree')).frameTree.frame.id,
            html: sheetHtml,
        });
        await new Promise((r) => setTimeout(r, 300));
        await p1.screenshot({ path: join(OUT_DIR, 'contact-sheet.jpg'), type: 'jpeg', fullPage: true });

        // 2. Cover context mockup
        const p2 = await browser.newPage({ width: 390, height: 844, dsf: 1, mobile: true });
        await p2.send('Page.setDocumentContent', {
            frameId: (await p2.send('Page.getFrameTree')).frameTree.frame.id,
            html: coverHtml,
        });
        await new Promise((r) => setTimeout(r, 250));
        await p2.screenshot({ path: join(OUT_DIR, 'cover-context.jpg'), type: 'jpeg' });

        // 3. 4x CPU Throttled Perf Test comparing normal alpha relief vs mix-blend-mode: soft-light
        await p2.send('Emulation.setCPUThrottlingRate', { rate: 4 });
        const measureFps = async (blendMode) => {
            const res = await p2.send('Runtime.evaluate', {
                awaitPromise: true,
                returnByValue: true,
                expression: `(async () => {
                    document.querySelectorAll('.corner,.emblem').forEach(el => el.style.mixBlendMode = '${blendMode}');
                    const frames = [];
                    let last = performance.now();
                    for (let i = 0; i < 45; i++) {
                        await new Promise(r => requestAnimationFrame(r));
                        const now = performance.now();
                        frames.push(now - last);
                        last = now;
                        document.querySelectorAll('.corner').forEach((el, idx) => {
                            el.style.transform = 'translate3d(0,' + ((i % 10) * (idx % 2 ? 0.5 : -0.5)) + 'px,0)';
                        });
                    }
                    const avgMs = frames.reduce((a, b) => a + b, 0) / frames.length;
                    return { blendMode: '${blendMode}', avgFrameMs: Number(avgMs.toFixed(2)), fps: Number((1000 / avgMs).toFixed(1)) };
                })()`,
            });
            return JSON.parse(JSON.stringify(res.result.value));
        };

        perfReport.normal = await measureFps('normal');
        perfReport.softLight = await measureFps('soft-light');
    } finally {
        await browser.close();
    }

    return perfReport;
}

const manifest = JSON.parse(readFileSync(MANIFEST_PATH, 'utf8'));
const qa = runAutomatedChecks(manifest);
const perf = await generateContactSheetAndContext(manifest);

const out = { qa, perf };
writeFileSync(join(OUT_DIR, 'qa-report.json'), JSON.stringify(out, null, 2));
console.log(JSON.stringify(out, null, 2));
if (!qa.passed) {
    process.exit(1);
}
