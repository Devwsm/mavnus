<form action="{{ route('clothes.destroy', $product) }}" method="POST"
    onsubmit="return confirm('Hapus produk {{ $product->name }}? Tindakan ini tidak bisa dibatalkan.')">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-[#B71C1C] transition">
        <i class="bi bi-trash"></i>
    </button>
</form>
