<x-layouts.app>
    <h1 class="ml-4">Halo Selamat Datang di Halaman Dokter</h1>
</x-layouts.app>
<form method="POST" action="/logout">
    @csrf
    <button>Logout</button>
</form>