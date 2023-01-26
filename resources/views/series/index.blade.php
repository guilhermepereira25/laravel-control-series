<x-layout title="Series" :successMessage="$successMessage">
    @auth
        <a href="{{ route('series.create') }}" class="btn btn-dark mb-3">Adicionar</a>
    @endauth

    <ul class="list-group">
        @foreach ($series as $serie)
            <li class="list-group-item d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    @if ($serie->cover == null)
                        <!-- amanhã ao inves de colocar socado se for null pegar esse path da imagem e setar pro registro -->
                        <img src="{{ asset('storage/cover_series/jeremy-hynes-YfP_VibQbhg-unsplash.jpg') }}" width="100" class="img-thumbnail me-3" alt="Imagem da série">
                    @else
                        <img src="{{ asset('storage/' . $serie->cover) }}" width="200" class="img-thumbnail me-3" alt="Imagem da série">
                    @endif
                    @auth <a href="{{ route('seasons.index', $serie->id) }}"> @endauth
                        {{ $serie->name }}
                    @auth </a> @endauth
                </div>

                @auth
                    <span class="d-flex align-items-center">
                        <span>
                            <a href="{{ route('series.edit', $serie->id) }}" class="btn btn-outline-primary btn-sm">A</a>
                        </span>

                        <form action="{{ route('series.destroy', $serie->id) }}" method="POST" class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">E</button>
                        </form>
                    </span>
                @endauth
            </li>
        @endforeach
    </ul>
</x-layout>
