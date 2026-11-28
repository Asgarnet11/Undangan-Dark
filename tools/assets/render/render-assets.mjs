/**
 * Relief Map & Texture Renderer (Ivory Emboss)
 *
 * Renders procedural botanical SVGs through an SVG blind-emboss lighting filter
 * (top-left azimuth 225, elevation 52) in headless Chromium at 1x and 2x,
 * then encodes metadata-free WebP files into public/themes/ivory-emboss/relief/
 * and generates seamless textures into public/themes/ivory-emboss/texture/.
 */
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';
import { Browser } from '/home/serv/.gemini/config/skills/design-taste-frontend/scripts/lib/cdp.mjs';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '..', '..', '..');
const MANIFEST_PATH = join(ROOT, 'tools/assets/manifest.json');
const OUT_SVG_DIR = join(ROOT, 'tools/assets/out/svg');
const OUT_RAW_DIR = join(ROOT, 'tools/assets/out/raw');
const RELIEF_DIR = join(ROOT, 'public/themes/ivory-emboss/relief');
const TEXTURE_DIR = join(ROOT, 'public/themes/ivory-emboss/texture');

function buildEmbossHtml(svgMarkup, width, height) {
    // Strip outer <svg ...> and </svg> to inject into filtered group
    const inner = svgMarkup
        .replace(/^<svg[^>]*>/i, '')
        .replace(/<\/svg>\s*$/i, '');

    return `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  html, body { margin: 0; padding: 0; background: transparent; overflow: hidden; }
  svg { display: block; width: ${width}px; height: ${height}px; }
</style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${width} ${height}" width="${width}" height="${height}">
  <defs>
    <filter id="iv-emboss" x="-4%" y="-4%" width="108%" height="108%" color-interpolation-filters="sRGB">
      <feGaussianBlur in="SourceAlpha" stdDeviation="1.35" result="height"/>
      <feDiffuseLighting in="height" surfaceScale="2.1" diffuseConstant="1.08" lighting-color="#ffffff" result="lit">
        <feDistantLight azimuth="225" elevation="52"/>
      </feDiffuseLighting>
      <feComposite in="lit" in2="SourceAlpha" operator="in" result="relief"/>
      <feOffset in="SourceAlpha" dx="1.6" dy="1.6" result="off"/>
      <feGaussianBlur in="off" stdDeviation="1.25" result="shadowBlur"/>
      <feFlood flood-color="#3c3428" flood-opacity="0.34" result="shadowColor"/>
      <feComposite in="shadowColor" in2="shadowBlur" operator="in" result="shadow"/>
      <feMerge>
        <feMergeNode in="shadow"/>
        <feMergeNode in="relief"/>
      </feMerge>
    </filter>
  </defs>
  <g filter="url(#iv-emboss)">
    ${inner}
  </g>
</svg>
</body>
</html>`;
}

function encodeWebP(pngPath, webpPath, maxKB) {
    const targetKB = Math.min(maxKB, 44);
    const py = `
from PIL import Image
import os
im = Image.open(${JSON.stringify(pngPath)}).convert("RGBA")
for q in [82, 74, 66, 58, 50, 42]:
    im.save(${JSON.stringify(webpPath)}, "WEBP", quality=q, alpha_quality=65, method=6, lossless=False)
    if os.path.getsize(${JSON.stringify(webpPath)}) <= ${targetKB} * 1024:
        break
print(os.path.getsize(${JSON.stringify(webpPath)}))
`;
    const res = spawnSync('python3', ['-c', py], { encoding: 'utf8' });
    if (res.status !== 0) {
        throw new Error(`WebP encode failed for ${webpPath}: ${res.stderr}`);
    }
    return Number(res.stdout.trim());
}

function generateTextures() {
    mkdirSync(TEXTURE_DIR, { recursive: true });
    const py = `
import os, math, random
from PIL import Image, ImageDraw, ImageFilter

tex_dir = ${JSON.stringify(TEXTURE_DIR)}

# 1. paper-grain.webp (512x512 seamless neutral gray cotton grain)
rng = random.Random(9001)
w, h = 512, 512
grain = Image.new("RGBA", (w, h), (128, 128, 128, 255))
px = grain.load()
for y in range(h):
    for x in range(w):
        # Periodic seamless noise + fine cotton weave
        n = rng.randint(-10, 10)
        weave = int(3 * math.sin(2 * math.pi * x / 8.0) * math.cos(2 * math.pi * y / 8.0))
        v = max(0, min(255, 128 + n + weave))
        px[x, y] = (v, v, v, 24)
grain.save(os.path.join(tex_dir, "paper-grain.webp"), "WEBP", quality=78, method=6)

# 2. paper-fiber.webp (512x512 seamless fine cotton fibers, 3x3 wrap blur)
fiber_big = Image.new("RGBA", (w * 3, h * 3), (0, 0, 0, 0))
draw = ImageDraw.Draw(fiber_big)
for i in range(420):
    x0 = rng.randint(0, w - 1)
    y0 = rng.randint(0, h - 1)
    ang = rng.uniform(0, math.tau)
    length = rng.uniform(6, 18)
    x1 = x0 + math.cos(ang) * length
    y1 = y0 + math.sin(ang) * length
    shade = 245 if i % 2 == 0 else 72
    alpha = rng.randint(12, 26)
    for dx in (0, w, 2 * w):
        for dy in (0, h, 2 * h):
            draw.line((x0 + dx, y0 + dy, x1 + dx, y1 + dy), fill=(shade, shade, shade, alpha), width=1)
fiber = fiber_big.filter(ImageFilter.GaussianBlur(0.5)).crop((w, h, 2 * w, 2 * h))
fiber.save(os.path.join(tex_dir, "paper-fiber.webp"), "WEBP", quality=78, method=6)

# 3. highlight-sweep.webp (512x256 diagonal soft highlight band)
sw, sh = 512, 256
sweep = Image.new("RGBA", (sw, sh), (255, 255, 255, 0))
spx = sweep.load()
for y in range(sh):
    for x in range(sw):
        u = (x / sw) * 0.75 + (y / sh) * 0.25
        dist = abs(u - 0.5) * 4.2
        a = int(max(0, 1 - dist * dist) * 95) if dist < 1 else 0
        spx[x, y] = (255, 255, 255, a)
sweep.save(os.path.join(tex_dir, "highlight-sweep.webp"), "WEBP", quality=82, method=6)

# 4. bg-desktop.webp (512x512 seamless subtle jasmine diamond lattice, 3x3 wrap blur)
bgd_big = Image.new("RGBA", (w * 3, h * 3), (0, 0, 0, 0))
bdraw = ImageDraw.Draw(bgd_big)
step = 128
for gy in range(0, h, step):
    for gx in range(0, w, step):
        for dx, dy in ((0, 0), (step // 2, step // 2)):
            cx, cy = (gx + dx) % w, (gy + dy) % h
            for ox in (0, w, 2 * w):
                for oy in (0, h, 2 * h):
                    px0, py0 = cx + ox, cy + oy
                    for p in range(6):
                        ang = (p * math.tau / 6) + 0.2
                        ex = px0 + math.cos(ang) * 14
                        ey = py0 + math.sin(ang) * 14
                        bdraw.ellipse((ex - 4, ey - 4, ex + 4, ey + 4), fill=(255, 255, 255, 22), outline=(80, 72, 60, 14))
bgd = bgd_big.filter(ImageFilter.GaussianBlur(0.6)).crop((w, h, 2 * w, 2 * h))
bgd.save(os.path.join(tex_dir, "bg-desktop.webp"), "WEBP", quality=78, method=6)

# 5. og-default.webp (1200x630 clean cotton paper OG preview)
og = Image.new("RGBA", (1200, 630), (239, 237, 232, 255))
odraw = ImageDraw.Draw(og)
odraw.rounded_rectangle((36, 36, 1164, 594), radius=24, outline=(255, 255, 255, 220), width=3)
odraw.rounded_rectangle((40, 40, 1160, 590), radius=22, outline=(60, 52, 40, 40), width=2)
og.convert("RGB").save(os.path.join(tex_dir, "og-default.webp"), "WEBP", quality=84, method=6)
print("Textures generated successfully")
`;
    const res = spawnSync('python3', ['-c', py], { encoding: 'utf8' });
    if (res.status !== 0) {
        throw new Error(`Texture generation failed: ${res.stderr}`);
    }
}

export async function renderAll() {
    mkdirSync(OUT_RAW_DIR, { recursive: true });
    mkdirSync(RELIEF_DIR, { recursive: true });
    generateTextures();

    const manifest = JSON.parse(readFileSync(MANIFEST_PATH, 'utf8'));
    const browser = await Browser.launch();
    const results = [];

    try {
        for (const asset of manifest.reliefAssets) {
            const [w, h] = asset.viewBox;
            const svgMarkup = readFileSync(join(OUT_SVG_DIR, `${asset.id}.svg`), 'utf8');
            const html = buildEmbossHtml(svgMarkup, w, h);

            // Render 1x and 2x with transparent background
            for (const scale of [1, 2]) {
                const page = await browser.newPage({
                    width: w,
                    height: h,
                    dsf: scale,
                    mobile: false,
                });
                await page.send('Emulation.setDefaultBackgroundColorOverride', {
                    color: { r: 0, g: 0, b: 0, a: 0 },
                });
                await page.send('Page.setDocumentContent', {
                    frameId: (await page.send('Page.getFrameTree')).frameTree.frame.id,
                    html,
                });
                await new Promise((r) => setTimeout(r, 80));

                const rawPng = join(OUT_RAW_DIR, `${asset.id}@${scale}x.png`);
                const { data } = await page.send('Page.captureScreenshot', {
                    format: 'png',
                    fromSurface: true,
                });
                writeFileSync(rawPng, Buffer.from(data, 'base64'));
                await page.send('Target.closeTarget', { targetId: page.targetId }).catch(() => {});

                const suffix = scale === 2 ? '@2x' : '';
                const webpPath = join(RELIEF_DIR, `${asset.id}${suffix}.webp`);
                const bytes = encodeWebP(rawPng, webpPath, asset.maxKB);
                results.push({ id: asset.id, scale: `${scale}x`, bytes, kb: Number((bytes / 1024).toFixed(2)) });
            }
        }
    } finally {
        await browser.close();
    }

    return results;
}

if (import.meta.url === `file://${process.argv[1]}`) {
    const results = await renderAll();
    console.log(JSON.stringify(results, null, 2));
}
