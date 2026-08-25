{{--
    Partial: Release Schedule Picker
    Dipakai di form tambah produk & modal edit. Include dengan variabel:
    - $id      : suffix unik (misal 'create' atau $editId) biar gak bentrok antar instance
    - $date    : nilai awal tanggal (Y-m-d), default hari ini
    - $hour    : nilai awal jam (0-23), default jam sekarang
    - $minute  : nilai awal menit (kelipatan 5), default menit sekarang dibulatkan
--}}
@php
    $todayStr = now()->format('Y-m-d');
    $maxStr = now()->addDays(7)->format('Y-m-d');
    $selectedDate = $date ?? $todayStr;
    $selectedHour = $hour ?? (int) now()->format('H');
    $selectedMinute = $minute ?? (int) (round(now()->format('i') / 5) * 5) % 60;
@endphp

<div class="flex flex-col gap-3">
    <div>
        <label class="block text-sm font-semibold mb-1.5 text-white">Tanggal Rilis</label>
        <input type="date" id="pickDate-{{ $id }}" min="{{ $todayStr }}" max="{{ $maxStr }}"
            value="{{ $selectedDate }}"
            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C]">
        <p class="text-white/40 text-xs mt-1.5">Maksimal 7 hari dari sekarang.</p>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1.5 text-white">Jam Rilis</label>
        <div class="relative bg-black border border-white/10 rounded-lg">
            {{-- Garis penanda item terpilih di tengah --}}
            <div
                class="pointer-events-none absolute inset-x-4 top-1/2 -translate-y-1/2 h-10 border-y border-[#B71C1C]/60 bg-[#B71C1C]/5 rounded">
            </div>

            <div class="flex justify-center gap-1">
                <div id="wheelHour-{{ $id }}" class="wheel-col h-30 w-16 overflow-y-scroll no-scrollbar"
                    style="scroll-snap-type: y mandatory;">
                    <div class="wheel-pad"></div>
                    @foreach (range(0, 23) as $h)
                        <div class="wheel-item h-10 flex items-center justify-center text-white/40 text-lg transition-colors"
                            style="scroll-snap-align: center;" data-value="{{ $h }}">
                            {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                        </div>
                    @endforeach
                    <div class="wheel-pad"></div>
                </div>

                <span class="text-white/30 text-lg self-center">:</span>

                <div id="wheelMinute-{{ $id }}"
                    class="wheel-col h-30 w-16 overflow-y-scroll no-scrollbar"
                    style="scroll-snap-type: y mandatory;">
                    <div class="wheel-pad"></div>
                    @foreach (range(0, 55, 5) as $m)
                        <div class="wheel-item h-10 flex items-center justify-center text-white/40 text-lg transition-colors"
                            style="scroll-snap-align: center;" data-value="{{ $m }}">
                            {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                        </div>
                    @endforeach
                    <div class="wheel-pad"></div>
                </div>
            </div>
        </div>
        <p class="text-white/40 text-xs mt-1.5">Geser buat pilih jam & menit (kelipatan 5).</p>
    </div>

    <input type="hidden" id="inputPublishedAt-{{ $id }}" name="published_at">
</div>

@once
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .wheel-pad {
            height: 40px;
        }

        .wheel-item.is-selected {
            color: #fff;
            font-weight: 700;
        }
    </style>

    <script>
        // ---- Wheel scroll picker (generic, dipake buat jam & menit) ----
        function mavnusInitWheel(elId, initialValue) {
            const el = document.getElementById(elId);
            if (!el || el.dataset.wheelInit) return;
            el.dataset.wheelInit = '1';

            const itemHeight = 40;
            const items = Array.from(el.querySelectorAll('.wheel-item'));

            function highlightNearest() {
                const idx = Math.max(0, Math.min(items.length - 1, Math.round(el.scrollTop / itemHeight)));
                items.forEach(i => i.classList.remove('is-selected'));
                items[idx]?.classList.add('is-selected');
                return items[idx] ? Number(items[idx].dataset.value) : null;
            }

            let scrollTimeout;
            el.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    const idx = Math.max(0, Math.min(items.length - 1, Math.round(el.scrollTop /
                        itemHeight)));
                    el.scrollTo({
                        top: idx * itemHeight,
                        behavior: 'smooth'
                    });
                    highlightNearest();
                    el.dispatchEvent(new CustomEvent('wheelchange'));
                }, 100);
            });

            // Posisikan awal ke initialValue tanpa animasi
            const startIdx = items.findIndex(i => Number(i.dataset.value) === Number(initialValue));
            el.scrollTop = (startIdx === -1 ? 0 : startIdx) * itemHeight;
            highlightNearest();
        }

        // Dipanggil pas radio "Jadwalkan" pertama kali dipilih (lazy init,
        // karena scrollTop gak akurat kalau container-nya masih display:none)
        function mavnusInitReleasePicker(id, initialHour, initialMinute) {
            mavnusInitWheel('wheelHour-' + id, initialHour);
            mavnusInitWheel('wheelMinute-' + id, initialMinute);

            function syncHidden() {
                const dateVal = document.getElementById('pickDate-' + id)?.value;
                const hSel = document.querySelector('#wheelHour-' + id + ' .is-selected');
                const mSel = document.querySelector('#wheelMinute-' + id + ' .is-selected');
                const h = String(hSel?.dataset.value ?? '00').padStart(2, '0');
                const m = String(mSel?.dataset.value ?? '00').padStart(2, '0');
                const hidden = document.getElementById('inputPublishedAt-' + id);
                if (hidden && dateVal) hidden.value = `${dateVal}T${h}:${m}`;
            }

            document.getElementById('wheelHour-' + id)?.addEventListener('wheelchange', syncHidden);
            document.getElementById('wheelMinute-' + id)?.addEventListener('wheelchange', syncHidden);
            document.getElementById('pickDate-' + id)?.addEventListener('change', syncHidden);

            syncHidden();
        }
    </script>
@endonce
