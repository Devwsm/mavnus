{{--
    Radio "Publish Sekarang" / "Jadwalkan" + wheel picker tanggal-jam
    (release-schedule-picker yang udah ada dipakai apa adanya di dalam sini).

    Props:
    - suffix        : id scoping (form create = '', modal edit = '-editModal-{id}')
    - scheduled     : true kalau produk ini lagi terjadwal (mode edit)
    - date/hour/minute       : nilai awal buat picker
    - defaultHour/defaultMinute : jam & menit default kalau baru pertama kali buka "Jadwalkan"
--}}
@props([
    'suffix' => '',
    'scheduled' => false,
    'date' => null,
    'hour' => null,
    'minute' => null,
    'defaultHour' => null,
    'defaultMinute' => null,
])
@php
    $pickerId = ltrim($suffix, '-') ?: 'create';
    $defHour = $defaultHour ?? (int) now()->format('H');
    $defMinute = $defaultMinute ?? (int) (round(now()->format('i') / 5) * 5) % 60;
@endphp

<div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
    <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Jadwal Rilis</h2>

    <div class="flex gap-4">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="release_mode" value="now" id="releaseModeNow{{ $suffix }}"
                onchange="mavnusToggleScheduledField('{{ $suffix }}')" class="accent-[#B71C1C]"
                @checked(!$scheduled)>
            <span class="text-sm text-white">Publish Sekarang</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="release_mode" value="scheduled" id="releaseModeScheduled{{ $suffix }}"
                onchange="mavnusToggleScheduledField('{{ $suffix }}')" class="accent-[#B71C1C]"
                @checked($scheduled)>
            <span class="text-sm text-white">Jadwalkan</span>
        </label>
    </div>

    <div id="scheduledAtWrapper{{ $suffix }}" style="display: {{ $scheduled ? 'block' : 'none' }};"
        data-default-hour="{{ $defHour }}" data-default-minute="{{ $defMinute }}">
        @include('components.dashboard.release-schedule-picker', [
            'id' => $pickerId,
            'date' => $date,
            'hour' => $hour,
            'minute' => $minute,
        ])
    </div>
</div>

@once
    <script>
        function mavnusToggleScheduledField(suffix) {
            const id = suffix ? suffix.replace(/^-/, '') : 'create';
            const scheduledRadio = document.getElementById('releaseModeScheduled' + suffix);
            const wrapper = document.getElementById('scheduledAtWrapper' + suffix);
            if (!scheduledRadio || !wrapper) return;

            const isScheduled = scheduledRadio.checked;
            wrapper.style.display = isScheduled ? 'block' : 'none';

            if (isScheduled) {
                const defaultHour = wrapper.dataset.defaultHour;
                const defaultMinute = wrapper.dataset.defaultMinute;
                mavnusInitReleasePicker(id, defaultHour, defaultMinute);
            }
        }
    </script>
@endonce
