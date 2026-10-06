/**
 * Bikin font ikon Bootstrap Icons versi ringan (subset) + icons.css
 * dari ikon yang BENAR-BENAR dipakai di project.
 *
 * Jalankan: npm run icons   (otomatis jalan lewat predev / prebuild)
 *
 * Kalau subset gagal, font lengkap (±134KB) disalin sebagai cadangan
 * supaya ikon TIDAK PERNAH hilang.
 */
const fs = require("fs");
const path = require("path");

const root = path.resolve(__dirname, "..");
const biDir = path.join(root, "node_modules", "bootstrap-icons", "font");
const outFont = path.join(root, "resources", "fonts", "bootstrap-icons-subset.woff2");
const outCss = path.join(root, "resources", "css", "icons.css");

// Class yang dibentuk dinamis lewat PHP/JS (tidak kelihatan oleh scanner) taruh di sini.
const EXTRA = [];

const SCAN_DIRS = ["resources/views", "resources/js", "app"];

function walk(dir, files = []) {
    if (!fs.existsSync(dir)) return files;
    for (const name of fs.readdirSync(dir)) {
        const p = path.join(dir, name);
        const st = fs.statSync(p);
        if (st.isDirectory()) walk(p, files);
        else if (/\.(php|js|html)$/.test(name)) files.push(p);
    }
    return files;
}

function collectUsed(valid) {
    const used = new Set(EXTRA);
    for (const d of SCAN_DIRS) {
        for (const f of walk(path.join(root, d))) {
            const txt = fs.readFileSync(f, "utf8");
            for (const m of txt.matchAll(/\bbi-([a-z0-9]+(?:-[a-z0-9]+)*)/g)) {
                if (valid[m[1]] !== undefined) used.add(m[1]);
            }
        }
    }
    return [...used].sort();
}

async function main() {
    const map = JSON.parse(fs.readFileSync(path.join(biDir, "bootstrap-icons.json"), "utf8"));
    const names = collectUsed(map);
    if (!names.length) throw new Error("Tidak ada ikon bi-* ditemukan, dibatalkan.");

    const src = fs.readFileSync(path.join(biDir, "fonts", "bootstrap-icons.woff2"));
    let out;
    try {
        const subsetFont = (await import("subset-font")).default;
        const text = names.map((n) => String.fromCodePoint(map[n])).join("");
        out = await subsetFont(src, text, { targetFormat: "woff2" });
        if (!out || out.length < 200) throw new Error("hasil subset kosong");
        console.log(`[icons] subset OK: ${names.length} ikon, ${out.length} bytes`);
    } catch (e) {
        console.warn(`[icons] subset gagal (${e.message}) -> pakai font lengkap`);
        out = src;
    }

    fs.mkdirSync(path.dirname(outFont), { recursive: true });
    fs.writeFileSync(outFont, out);

    let css = `/* DIBUAT OTOMATIS oleh scripts/build-icons.cjs (npm run icons) — jangan diedit manual. */
@font-face {
    font-family: "bootstrap-icons";
    src: url("../fonts/bootstrap-icons-subset.woff2") format("woff2");
    font-display: swap;
}

.bi::before,
[class^="bi-"]::before,
[class*=" bi-"]::before {
    display: inline-block;
    font-family: "bootstrap-icons" !important;
    font-style: normal;
    font-weight: normal !important;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    vertical-align: -0.125em;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
`;
    for (const n of names) {
        css += `\n.bi-${n}::before {\n    content: "\\${map[n].toString(16)}";\n}`;
    }
    fs.writeFileSync(outCss, css + "\n");
}

main().catch((e) => {
    console.error("[icons] GAGAL:", e.message);
    process.exit(1);
});
