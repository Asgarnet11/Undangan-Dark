/**
 * Procedural Botanical Jasmine SVG Generator (Ivory Emboss)
 *
 * Rules enforced:
 * - Deterministic mulberry32 PRNG per asset seed (never Math.random).
 * - Cubic Bezier stems with tapering width & max 2 branching levels (30-55 deg).
 * - Opposite (berhadapan) ovate-acuminate jasmine leaves (w/l = 0.45-0.60),
 *   tapering 1.0 -> 0.6 toward tips, midrib + 3-5 lateral veins as shallower grooves.
 * - Jasmine flowers with 5-9 oblong petals in 2 rotated layers + small center,
 *   plus half-open flowers & oblong buds with minimum spacing.
 * - Zero raster embeds, zero transforms that look like mirrored flips.
 */
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '..', '..', '..');
const MANIFEST_PATH = join(ROOT, 'tools/assets/manifest.json');
const OUT_SVG_DIR = join(ROOT, 'tools/assets/out/svg');

export function mulberry32(seed) {
    let a = seed >>> 0;
    return function () {
        a |= 0;
        a = (a + 0x6d2b79f5) | 0;
        let t = Math.imul(a ^ (a >>> 15), 1 | a);
        t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
}

function randRange(rng, min, max) {
    return min + (max - min) * rng();
}

function deg2rad(d) {
    return (d * Math.PI) / 180;
}

function cubicPoint(p0, p1, p2, p3, t) {
    const u = 1 - t;
    const tt = t * t;
    const uu = u * u;
    const uuu = uu * u;
    const ttt = tt * t;
    return {
        x: uuu * p0.x + 3 * uu * t * p1.x + 3 * u * tt * p2.x + ttt * p3.x,
        y: uuu * p0.y + 3 * uu * t * p1.y + 3 * u * tt * p2.y + ttt * p3.y,
    };
}

function cubicTangent(p0, p1, p2, p3, t) {
    const u = 1 - t;
    const dx =
        3 * u * u * (p1.x - p0.x) +
        6 * u * t * (p2.x - p1.x) +
        3 * t * t * (p3.x - p2.x);
    const dy =
        3 * u * u * (p1.y - p0.y) +
        6 * u * t * (p2.y - p1.y) +
        3 * t * t * (p3.y - p2.y);
    return Math.atan2(dy, dx);
}

function fmt(n) {
    return Number(n.toFixed(2));
}

/**
 * Generates a single ovate-acuminate jasmine leaf with midrib and 3-5 lateral veins.
 */
function buildLeaf(x, y, angleRad, length, rng, leafStats) {
    const ratio = randRange(rng, 0.45, 0.6);
    const width = length * ratio;
    const curveBias = randRange(rng, -0.14, 0.14) * width;
    leafStats.push(length);

    const cos = Math.cos(angleRad);
    const sin = Math.sin(angleRad);
    const lx = (u, v) => fmt(x + u * cos - v * sin);
    const ly = (u, v) => fmt(y + u * sin + v * cos);

    // Ovate-acuminate outline (base -> widest at 38% -> acuminate tip)
    const tipX = length;
    const tipY = curveBias * 0.7;
    const c1x = length * 0.22;
    const c1y = -width * 0.62 + curveBias * 0.3;
    const c2x = length * 0.68;
    const c2y = -width * 0.42 + curveBias * 0.5;

    const c3x = length * 0.68;
    const c3y = width * 0.42 + curveBias * 0.5;
    const c4x = length * 0.22;
    const c4y = width * 0.62 + curveBias * 0.3;

    const outline = `M ${lx(0, 0)} ${ly(0, 0)} C ${lx(c1x, c1y)} ${ly(c1x, c1y)}, ${lx(c2x, c2y)} ${ly(c2x, c2y)}, ${lx(tipX, tipY)} ${ly(tipX, tipY)} C ${lx(c3x, c3y)} ${ly(c3x, c3y)}, ${lx(c4x, c4y)} ${ly(c4x, c4y)}, ${lx(0, 0)} ${ly(0, 0)} Z`;

    // Midrib (shallower groove)
    const midrib = `M ${lx(length * 0.06, 0)} ${ly(length * 0.06, 0)} Q ${lx(length * 0.48, curveBias * 0.45)} ${ly(length * 0.48, curveBias * 0.45)} ${lx(length * 0.88, tipY * 0.9)} ${ly(length * 0.88, tipY * 0.9)}`;

    // 3-5 lateral veins
    const veinCount = Math.floor(randRange(rng, 3, 5.99));
    let veins = '';
    for (let i = 1; i <= veinCount; i++) {
        const t = 0.18 + (i / (veinCount + 1)) * 0.6;
        const vx0 = length * t;
        const vy0 = curveBias * t * 0.5;
        const span = width * 0.42 * (1 - t * 0.45);
        const vx1 = vx0 + length * 0.14;
        veins += `<path class="iv-vein" d="M ${lx(vx0, vy0)} ${ly(vx0, vy0)} Q ${lx(vx0 + length * 0.07, vy0 - span * 0.55)} ${ly(vx0 + length * 0.07, vy0 - span * 0.55)} ${lx(vx1, vy0 - span)} ${ly(vx1, vy0 - span)}"/>`;
        veins += `<path class="iv-vein" d="M ${lx(vx0, vy0)} ${ly(vx0, vy0)} Q ${lx(vx0 + length * 0.07, vy0 + span * 0.55)} ${ly(vx0 + length * 0.07, vy0 + span * 0.55)} ${lx(vx1, vy0 + span)} ${ly(vx1, vy0 + span)}"/>`;
    }

    return `<g class="iv-leaf-group">
      <path class="iv-silhouette iv-leaf" d="${outline}"/>
      <path class="iv-midrib" d="${midrib}"/>
      ${veins}
    </g>`;
}

/**
 * Generates an oblong jasmine petal path.
 */
function petalPath(cx, cy, angle, len, wid) {
    const cos = Math.cos(angle);
    const sin = Math.sin(angle);
    const px = (u, v) => fmt(cx + u * cos - v * sin);
    const py = (u, v) => fmt(cy + u * sin + v * cos);

    return `M ${px(0, 0)} ${py(0, 0)} C ${px(len * 0.3, -wid)} ${py(len * 0.3, -wid)}, ${px(len * 0.78, -wid * 0.72)} ${py(len * 0.78, -wid * 0.72)}, ${px(len, 0)} ${py(len, 0)} C ${px(len * 0.78, wid * 0.72)} ${py(len * 0.78, wid * 0.72)}, ${px(len * 0.3, wid)} ${py(len * 0.3, wid)}, ${px(0, 0)} ${py(0, 0)} Z`;
}

/**
 * Open jasmine flower: 5-9 oblong petals in 2 rotated layers + small center.
 */
function buildOpenFlower(cx, cy, radius, rng, forcedPetals = null) {
    const petalCount = forcedPetals ?? Math.floor(randRange(rng, 5, 9.99));
    const outerCount = Math.ceil(petalCount / 2);
    const innerCount = petalCount - outerCount;
    const baseRot = randRange(rng, 0, Math.PI * 2);
    let paths = '';

    for (let i = 0; i < outerCount; i++) {
        const a = baseRot + (i * Math.PI * 2) / outerCount + randRange(rng, -0.09, 0.09);
        const len = radius * randRange(rng, 0.92, 1.08);
        const wid = radius * randRange(rng, 0.24, 0.31);
        paths += `<path class="iv-silhouette iv-petal-outer" d="${petalPath(cx, cy, a, len, wid)}"/>`;
    }

    const innerOffset = Math.PI / Math.max(outerCount, 3);
    for (let i = 0; i < innerCount; i++) {
        const a = baseRot + innerOffset + (i * Math.PI * 2) / Math.max(innerCount, 1) + randRange(rng, -0.1, 0.1);
        const len = radius * randRange(rng, 0.76, 0.88);
        const wid = radius * randRange(rng, 0.21, 0.27);
        paths += `<path class="iv-silhouette iv-petal-inner" d="${petalPath(cx, cy, a, len, wid)}"/>`;
    }

    paths += `<circle class="iv-center" cx="${fmt(cx)}" cy="${fmt(cy)}" r="${fmt(radius * 0.15)}"/>`;
    return `<g class="iv-flower-open">${paths}</g>`;
}

/**
 * Half-open jasmine flower (side/three-quarter profile).
 */
function buildHalfFlower(cx, cy, angle, radius, rng) {
    const spread = randRange(rng, 0.42, 0.58);
    let paths = '';
    const count = 4;
    for (let i = 0; i < count; i++) {
        const offset = ((i - (count - 1) / 2) / (count / 2)) * spread;
        const len = radius * (i === 1 || i === 2 ? 0.95 : 0.82);
        const wid = radius * 0.24;
        paths += `<path class="iv-silhouette iv-petal-outer" d="${petalPath(cx, cy, angle + offset, len, wid)}"/>`;
    }
    paths += `<ellipse class="iv-calyx" cx="${fmt(cx)}" cy="${fmt(cy)}" rx="${fmt(radius * 0.18)}" ry="${fmt(radius * 0.12)}" transform="rotate(${fmt((angle * 180) / Math.PI)} ${fmt(cx)} ${fmt(cy)})"/>`;
    return `<g class="iv-flower-half">${paths}</g>`;
}

/**
 * Oblong jasmine bud.
 */
function buildBud(cx, cy, angle, length, rng) {
    const wid = length * randRange(rng, 0.26, 0.34);
    return `<g class="iv-bud">
      <path class="iv-silhouette" d="${petalPath(cx, cy, angle, length, wid)}"/>
    </g>`;
}

/**
 * Builds a tapered cubic Bezier branch with opposite leaf pairs and optional flower cluster.
 */
function buildBranch(p0, p1, p2, p3, options, rng, leafStats, placedCenters) {
    const {
        baseWidth = 4.2,
        nodeCount = 5,
        leafBaseLen = 46,
        hasCluster = true,
        clusterRadius = 26,
        depth = 0,
    } = options;

    let svg = '';

    // Tapered stem via variable-width polygon
    const steps = 16;
    const leftPts = [];
    const rightPts = [];
    for (let i = 0; i <= steps; i++) {
        const t = i / steps;
        const pt = cubicPoint(p0, p1, p2, p3, t);
        const tan = cubicTangent(p0, p1, p2, p3, t);
        const nx = -Math.sin(tan);
        const ny = Math.cos(tan);
        const halfW = (baseWidth * (1 - t * 0.7)) / 2;
        leftPts.push(`${fmt(pt.x + nx * halfW)},${fmt(pt.y + ny * halfW)}`);
        rightPts.unshift(`${fmt(pt.x - nx * halfW)},${fmt(pt.y - ny * halfW)}`);
    }
    svg += `<polygon class="iv-silhouette iv-stem" points="${leftPts.concat(rightPts).join(' ')}"/>`;

    // Opposite leaf pairs along nodes
    for (let i = 1; i <= nodeCount; i++) {
        const baseT = i / (nodeCount + 1);
        const t = Math.min(0.88, Math.max(0.14, baseT * randRange(rng, 0.92, 1.08)));
        const pt = cubicPoint(p0, p1, p2, p3, t);
        const tan = cubicTangent(p0, p1, p2, p3, t);
        const scale = 1.0 - t * 0.4; // tapers 1.0 -> 0.6
        const leafLen = leafBaseLen * scale * randRange(rng, 0.9, 1.12);

        // Opposite pair (left & right of node, 35-55 deg ±8 deg)
        const angleL = tan - deg2rad(randRange(rng, 35, 55) + randRange(rng, -8, 8));
        const angleR = tan + deg2rad(randRange(rng, 35, 55) + randRange(rng, -8, 8));
        svg += buildLeaf(pt.x, pt.y, angleL, leafLen, rng, leafStats);
        svg += buildLeaf(pt.x, pt.y, angleR, leafLen * randRange(rng, 0.92, 1.06), rng, leafStats);

        // Secondary branch (max 2 levels)
        if (depth < 1 && i === Math.floor(nodeCount / 2)) {
            const subSign = rng() > 0.5 ? 1 : -1;
            const subAngle = tan + subSign * deg2rad(randRange(rng, 30, 55));
            const subLen = leafBaseLen * 2.1;
            const sp1 = {
                x: pt.x + Math.cos(subAngle) * subLen * 0.35,
                y: pt.y + Math.sin(subAngle) * subLen * 0.35,
            };
            const sp2 = {
                x: pt.x + Math.cos(subAngle + subSign * 0.15) * subLen * 0.7,
                y: pt.y + Math.sin(subAngle + subSign * 0.15) * subLen * 0.7,
            };
            const sp3 = {
                x: pt.x + Math.cos(subAngle) * subLen,
                y: pt.y + Math.sin(subAngle) * subLen,
            };
            svg += buildBranch(
                pt,
                sp1,
                sp2,
                sp3,
                {
                    baseWidth: baseWidth * 0.62,
                    nodeCount: 3,
                    leafBaseLen: leafBaseLen * 0.75,
                    hasCluster: true,
                    clusterRadius: clusterRadius * 0.82,
                    depth: depth + 1,
                },
                rng,
                leafStats,
                placedCenters
            );
        }
    }

    // Flower cluster at tip (3-7 blooms: open, half-open, buds) with min spacing
    if (hasCluster) {
        const tip = p3;
        const tipTan = cubicTangent(p0, p1, p2, p3, 0.98);
        const count = Math.floor(randRange(rng, 3, 6.99));
        for (let k = 0; k < count; k++) {
            const offsetAng = tipTan + randRange(rng, -1.25, 1.25);
            const dist = k === 0 ? 0 : randRange(rng, clusterRadius * 0.8, clusterRadius * 1.65);
            const fx = tip.x + Math.cos(offsetAng) * dist;
            const fy = tip.y + Math.sin(offsetAng) * dist;

            const tooClose = placedCenters.some(
                (c) => Math.hypot(c.x - fx, c.y - fy) < clusterRadius * 0.72
            );
            if (tooClose) {
                continue;
            }
            placedCenters.push({ x: fx, y: fy });

            if (k === 0 || (k === 1 && rng() > 0.35)) {
                svg += buildOpenFlower(fx, fy, clusterRadius * randRange(rng, 0.88, 1.1), rng);
            } else if (k % 2 === 0) {
                svg += buildHalfFlower(fx, fy, offsetAng, clusterRadius * 0.9, rng);
            } else {
                svg += buildBud(fx, fy, offsetAng, clusterRadius * randRange(rng, 0.75, 0.95), rng);
            }
        }
    }

    return svg;
}

function wrapSvg(viewBox, body) {
    const [w, h] = viewBox;
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${w} ${h}" width="${w}" height="${h}">
  <style>
    .iv-silhouette { fill: #d8d4cc; stroke: #c4bfb6; stroke-width: 0.7; stroke-linejoin: round; }
    .iv-stem { fill: #d0ccc4; stroke: none; }
    .iv-midrib { fill: none; stroke: #b4afa6; stroke-width: 1.1; stroke-linecap: round; }
    .iv-vein { fill: none; stroke: #beb9b0; stroke-width: 0.75; stroke-linecap: round; }
    .iv-center { fill: #bab4aa; stroke: #a8a298; stroke-width: 0.6; }
    .iv-calyx { fill: #c6c1b8; stroke: none; }
    .iv-ring-stroke { fill: none; stroke: #d4d0c8; stroke-linecap: round; }
  </style>
  ${body}
</svg>`;
}

function generateCorner(asset, rng, leafStats) {
    const [w, h] = asset.viewBox;
    const corner = asset.params.corner;
    const ox = corner.includes('l') ? 24 : w - 24;
    const oy = corner.includes('t') ? 24 : h - 24;
    const dirX = corner.includes('l') ? 1 : -1;
    const dirY = corner.includes('t') ? 1 : -1;

    const placedCenters = [];
    let body = '';

    // 3 asymmetric radiating branches concentrated in the corner, fading before center safe zone
    const configs = [
        { lenX: 380, lenY: 165, nodes: 5, leafLen: 52, rad: 30 },
        { lenX: 250, lenY: 295, nodes: 5, leafLen: 48, rad: 28 },
        { lenX: 135, lenY: 375, nodes: 4, leafLen: 44, rad: 26 },
    ];

    configs.forEach((cfg, idx) => {
        const p0 = { x: ox + dirX * randRange(rng, 0, 14), y: oy + dirY * randRange(rng, 0, 14) };
        const p3 = {
            x: ox + dirX * cfg.lenX * randRange(rng, 0.88, 1.05),
            y: oy + dirY * cfg.lenY * randRange(rng, 0.88, 1.05),
        };
        const p1 = {
            x: p0.x + (p3.x - p0.x) * 0.33 + dirX * randRange(rng, -24, 24),
            y: p0.y + (p3.y - p0.y) * 0.33 + dirY * randRange(rng, -24, 24),
        };
        const p2 = {
            x: p0.x + (p3.x - p0.x) * 0.68 + dirX * randRange(rng, -20, 20),
            y: p0.y + (p3.y - p0.y) * 0.68 + dirY * randRange(rng, -20, 20),
        };
        body += buildBranch(
            p0,
            p1,
            p2,
            p3,
            {
                baseWidth: 5.2 - idx * 0.5,
                nodeCount: cfg.nodes,
                leafBaseLen: cfg.leafLen * (asset.params.leafScale || 1),
                hasCluster: true,
                clusterRadius: cfg.rad,
                depth: 0,
            },
            rng,
            leafStats,
            placedCenters
        );
    });

    return wrapSvg(asset.viewBox, body);
}

function generateEdge(asset, rng, leafStats) {
    const [w, h] = asset.viewBox;
    const isLeft = asset.params.side === 'left';
    const xBase = isLeft ? 28 : w - 28;
    const dirX = isLeft ? 1 : -1;
    const placedCenters = [];
    let body = '';

    // Two gentle vertical-sweeping sprigs hugging the outer margin (safe zone kept clear)
    const p0 = { x: xBase, y: 70 };
    const p3 = { x: xBase + dirX * randRange(rng, 85, 130), y: h - 90 };
    const p1 = { x: xBase + dirX * randRange(rng, 45, 95), y: h * 0.34 };
    const p2 = { x: xBase + dirX * randRange(rng, 25, 75), y: h * 0.68 };

    body += buildBranch(
        p0,
        p1,
        p2,
        p3,
        {
            baseWidth: 4.8,
            nodeCount: asset.params.nodes || 7,
            leafBaseLen: 46,
            hasCluster: true,
            clusterRadius: 27,
            depth: 0,
        },
        rng,
        leafStats,
        placedCenters
    );

    return wrapSvg(asset.viewBox, body);
}

function generateWreathOval(asset, rng, leafStats) {
    const [w, h] = asset.viewBox;
    const cx = w / 2;
    const cy = h / 2;
    const rx = asset.params.rx || 248;
    const ry = asset.params.ry || 310;
    const placedCenters = [];
    let body = `<ellipse class="iv-ring-stroke" stroke-width="2.2" cx="${cx}" cy="${cy}" rx="${rx}" ry="${ry}"/>`;
    body += `<ellipse class="iv-ring-stroke" stroke-width="1.2" cx="${cx}" cy="${cy}" rx="${rx - 10}" ry="${ry - 10}"/>`;

    // 4 asymmetric jasmine clusters along the oval perimeter
    const angles = [-2.4, -0.7, 0.75, 2.35];
    angles.forEach((a, idx) => {
        const px = cx + Math.cos(a) * rx;
        const py = cy + Math.sin(a) * ry;
        const tx = -Math.sin(a);
        const ty = Math.cos(a);
        const len = 115;
        const p0 = { x: px - tx * len * 0.5, y: py - ty * len * 0.5 };
        const p3 = { x: px + tx * len * 0.5, y: py + ty * len * 0.5 };
        const p1 = { x: px - tx * 20, y: py - ty * 20 };
        const p2 = { x: px + tx * 20, y: py + ty * 20 };
        body += buildBranch(
            p0,
            p1,
            p2,
            p3,
            {
                baseWidth: 3.8,
                nodeCount: 3,
                leafBaseLen: 36,
                hasCluster: idx % 2 === 0,
                clusterRadius: 24,
                depth: 1,
            },
            rng,
            leafStats,
            placedCenters
        );
    });

    return wrapSvg(asset.viewBox, body);
}

function generateEmblem(asset, rng, leafStats) {
    const [w, h] = asset.viewBox;
    const cx = w / 2;
    const cy = h * 0.54;
    const span = (asset.params.span || 320) * 0.48;
    const placedCenters = [];
    let body = '';

    // Two crossed jasmine twigs
    body += buildBranch(
        { x: cx - span, y: cy + 56 },
        { x: cx - span * 0.35, y: cy + 28 },
        { x: cx + span * 0.25, y: cy - 18 },
        { x: cx + span * 0.85, y: cy - 62 },
        { baseWidth: 4.2, nodeCount: 4, leafBaseLen: 38, hasCluster: true, clusterRadius: 22, depth: 1 },
        rng,
        leafStats,
        placedCenters
    );

    body += buildBranch(
        { x: cx + span, y: cy + 56 },
        { x: cx + span * 0.35, y: cy + 28 },
        { x: cx - span * 0.25, y: cy - 18 },
        { x: cx - span * 0.85, y: cy - 62 },
        { baseWidth: 4.2, nodeCount: 4, leafBaseLen: 36, hasCluster: true, clusterRadius: 21, depth: 1 },
        rng,
        leafStats,
        placedCenters
    );

    // Central focal cluster at intersection
    body += buildOpenFlower(cx, cy + 6, 26, rng, 7);
    body += buildHalfFlower(cx - 28, cy + 16, -2.5, 19, rng);
    body += buildBud(cx + 28, cy + 14, -0.5, 18, rng);

    return wrapSvg(asset.viewBox, body);
}

function generateDividerSprig(asset, rng, leafStats) {
    const [w, h] = asset.viewBox;
    const cy = h / 2;
    const placedCenters = [];
    let body = '';
    body += buildBranch(
        { x: 70, y: cy + 8 },
        { x: w * 0.32, y: cy - 10 },
        { x: w * 0.68, y: cy + 10 },
        { x: w - 70, y: cy - 4 },
        { baseWidth: 3.4, nodeCount: 5, leafBaseLen: 28, hasCluster: false, depth: 1 },
        rng,
        leafStats,
        placedCenters
    );
    body += buildOpenFlower(w / 2, cy, 20, rng, 6);
    body += buildBud(w / 2 - 28, cy - 4, -2.9, 15, rng);
    body += buildBud(w / 2 + 28, cy + 4, 0.25, 15, rng);
    return wrapSvg(asset.viewBox, body);
}

function generateArchPanel(asset, rng, leafStats) {
    const [w, h] = asset.viewBox;
    const m = 34;
    const archR = (w - m * 2) / 2;
    const innerM = m + 16;
    const innerR = (w - innerM * 2) / 2;
    const outerD = `M ${m} ${h - m} L ${m} ${m + archR} A ${archR} ${archR} 0 0 1 ${w - m} ${m + archR} L ${w - m} ${h - m} Z`;
    const innerD = `M ${innerM} ${h - innerM} L ${innerM} ${innerM + innerR} A ${innerR} ${innerR} 0 0 1 ${w - innerM} ${innerM + innerR} L ${w - innerM} ${h - innerM} Z`;

    let body = `<path class="iv-ring-stroke" stroke-width="2.5" d="${outerD}"/>`;
    body += `<path class="iv-ring-stroke" stroke-width="1.4" d="${innerD}"/>`;
    body += buildOpenFlower(w / 2, m + 10, 22, rng, 7);
    body += buildLeaf(w / 2 - 18, m + 12, -2.8, 30, rng, leafStats);
    body += buildLeaf(w / 2 + 18, m + 12, -0.34, 30, rng, leafStats);
    return wrapSvg(asset.viewBox, body);
}

function generateMonogramRing(asset, rng, leafStats) {
    const [w, h] = asset.viewBox;
    const cx = w / 2;
    const cy = h / 2;
    const rx = asset.params.rx || 136;
    const ry = asset.params.ry || 150;
    let body = `<ellipse class="iv-ring-stroke" stroke-width="3" cx="${cx}" cy="${cy}" rx="${rx}" ry="${ry}"/>`;
    body += `<ellipse class="iv-ring-stroke" stroke-width="1.3" cx="${cx}" cy="${cy}" rx="${rx - 10}" ry="${ry - 10}"/>`;
    body += buildOpenFlower(cx, cy - ry, 18, rng, 6);
    body += buildLeaf(cx - 14, cy - ry + 2, -2.9, 26, rng, leafStats);
    body += buildLeaf(cx + 14, cy - ry + 2, -0.24, 26, rng, leafStats);
    return wrapSvg(asset.viewBox, body);
}

function generateScallopMask(asset) {
    const [w, h] = asset.viewBox;
    const r = 12;
    let d = `M ${r} ${r}`;
    const steps = 6;
    const stepX = (w - r * 2) / steps;
    for (let i = 0; i < steps; i++) {
        const x2 = r + (i + 1) * stepX;
        d += ` A ${stepX / 2} ${stepX / 2} 0 0 1 ${fmt(x2)} ${r}`;
    }
    for (let i = 0; i < steps; i++) {
        const y2 = r + (i + 1) * stepX;
        d += ` A ${stepX / 2} ${stepX / 2} 0 0 1 ${w - r} ${fmt(y2)}`;
    }
    for (let i = 0; i < steps; i++) {
        const x2 = w - r - (i + 1) * stepX;
        d += ` A ${stepX / 2} ${stepX / 2} 0 0 1 ${fmt(x2)} ${h - r}`;
    }
    for (let i = 0; i < steps; i++) {
        const y2 = h - r - (i + 1) * stepX;
        d += ` A ${stepX / 2} ${stepX / 2} 0 0 1 ${r} ${fmt(y2)}`;
    }
    d += ' Z';
    return wrapSvg(asset.viewBox, `<path class="iv-ring-stroke" stroke-width="2.4" d="${d}"/>`);
}

export function generateAll() {
    mkdirSync(OUT_SVG_DIR, { recursive: true });
    const manifest = JSON.parse(readFileSync(MANIFEST_PATH, 'utf8'));
    const report = [];

    for (const asset of manifest.reliefAssets) {
        const rng = mulberry32(asset.seed);
        const leafStats = [];
        let svg = '';

        if (asset.category === 'corner') {
            svg = generateCorner(asset, rng, leafStats);
        } else if (asset.category === 'edge') {
            svg = generateEdge(asset, rng, leafStats);
        } else if (asset.category === 'wreath') {
            svg = generateWreathOval(asset, rng, leafStats);
        } else if (asset.category === 'emblem') {
            svg = generateEmblem(asset, rng, leafStats);
        } else if (asset.id === 'flower-open') {
            svg = wrapSvg(asset.viewBox, buildOpenFlower(120, 120, 74, rng, asset.params.petals));
        } else if (asset.id === 'flower-half') {
            svg = wrapSvg(asset.viewBox, buildHalfFlower(120, 165, -Math.PI / 2, 86, rng));
        } else if (asset.id === 'bud-cluster') {
            const b =
                buildBud(120, 165, -Math.PI / 2, 74, rng) +
                buildBud(95, 165, -Math.PI / 2 - 0.35, 58, rng) +
                buildBud(145, 165, -Math.PI / 2 + 0.35, 56, rng);
            svg = wrapSvg(asset.viewBox, b);
        } else if (asset.category === 'divider') {
            svg = generateDividerSprig(asset, rng, leafStats);
        } else if (asset.category === 'panel') {
            svg = generateArchPanel(asset, rng, leafStats);
        } else if (asset.category === 'ring') {
            svg = generateMonogramRing(asset, rng, leafStats);
        } else if (asset.category === 'mask') {
            svg = generateScallopMask(asset);
        }

        const outPath = join(OUT_SVG_DIR, `${asset.id}.svg`);
        writeFileSync(outPath, svg, 'utf8');

        // Leaf size coefficient of variation (CV = std / mean)
        let cv = null;
        if (leafStats.length > 1) {
            const mean = leafStats.reduce((a, b) => a + b, 0) / leafStats.length;
            const variance =
                leafStats.reduce((a, b) => a + (b - mean) ** 2, 0) / leafStats.length;
            cv = Number((Math.sqrt(variance) / mean).toFixed(3));
        }

        report.push({
            id: asset.id,
            seed: asset.seed,
            leafCount: leafStats.length,
            leafSizeCV: cv,
            svgBytes: Buffer.byteLength(svg, 'utf8'),
        });
    }

    return report;
}

if (import.meta.url === `file://${process.argv[1]}`) {
    const report = generateAll();
    console.log(JSON.stringify(report, null, 2));
}
