<x-layout title="Episódios da temporada {!! $seasons->number !!}" :successMessage="$successMessage" :failMessage="$failMessage">
    <form action="{{ route('episodes.update', $seasons->id)}}" method="POST">
        @csrf
        <ul class="list-group">
            @foreach ($episodes as $episode)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Episódio {{ $episode->number }}

                            <input 
                                type="checkbox" name="episodes[]" 
                                value="{{ $episode->id }}"
                                @if ($episode->watched == 1) checked @endif
                            >
                        </li>
            @endforeach
        </ul>

        <button class="btn btn-primary mt-2 mb-2">Salvar</button>
    </form>
</x-layout>