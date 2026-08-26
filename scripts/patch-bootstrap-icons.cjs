// Bootstrap Icons default-nya pakai font-display: block (nunggu s.d. 3 detik
// sebelum fallback tampil), yang bikin skor Lighthouse Performance turun.
// Script ini otomatis gantiin ke font-display: swap tiap `npm install`
// (lewat "postinstall" di package.json), jadi gak ketimpa pas install ulang.
const fs = require("fs");
const path = require("path");

const target = path.join(
    __dirname,
    "..",
    "node_modules",
    "bootstrap-icons",
    "font",
    "bootstrap-icons.css",
);

if (!fs.existsSync(target)) {
    console.log(
        "[patch-bootstrap-icons] File gak ketemu, skip (mungkin belum npm install).",
    );
    process.exit(0);
}

const css = fs.readFileSync(target, "utf8");
const patched = css.replace(/font-display:\s*block;/g, "font-display: swap;");

if (patched === css) {
    console.log(
        "[patch-bootstrap-icons] Udah ke-patch atau gak ada yang perlu diubah.",
    );
} else {
    fs.writeFileSync(target, patched, "utf8");
    console.log(
        "[patch-bootstrap-icons] font-display: block -> swap berhasil di-patch.",
    );
}
