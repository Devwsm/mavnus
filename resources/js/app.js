//
import Swal from "sweetalert2";
window.Swal = Swal; // biar bisa dipanggil dari script inline di Blade

// Input yang cuma boleh angka (nomor HP). Pakai atribut `data-digits-only`
// di <input>-nya. Event delegation, jadi otomatis berlaku juga buat
// input yang dibuat belakangan. Event `input` ikut kena waktu paste.
document.addEventListener("input", (e) => {
    const el = e.target;
    if (
        !(el instanceof HTMLInputElement) ||
        !el.hasAttribute("data-digits-only")
    )
        return;

    const clean = el.value.replace(/\D/g, "");
    if (clean === el.value) return;

    // Jaga posisi kursor biar gak loncat ke ujung pas ngetik di tengah
    const caret = el.selectionStart ?? el.value.length;
    const newCaret = el.value.slice(0, caret).replace(/\D/g, "").length;
    el.value = clean;
    try {
        el.setSelectionRange(newCaret, newCaret);
    } catch (_) {
        /* tipe input gak support, abaikan */
    }
});
