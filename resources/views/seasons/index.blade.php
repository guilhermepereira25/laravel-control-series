<x-layout title="Temporadas de {!! $series->name !!}">
    <ul class="list-group">
        @foreach ($seasons as $season)
                @if($season->number <= 0)
                    <div class="alert alert-info">
                        <p>Não existe uma temporada para esta série com nome: {{$series->name}} </p>
                    </div>
                @else
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span> 
                            <a href="{{ route('episodes.index', $season->id) }}">Temporada</a> {{ $season->number }}
                        </span>

                        <span class="badge bg-secondary">
                           {{ $season->numberOfWatchedEpisodes() }} / {{ $season->episodes->count() }}
                        </span>
                    </li>
                @endif
        @endforeach
    </ul>
</x-layout>