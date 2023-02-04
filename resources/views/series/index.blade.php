<x-layout title="Series" :successMessage="$successMessage">
    @auth
        <a href="{{ route('series.create') }}" class="btn btn-primary mb-3">Adicionar</a>
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

                        <span class="ms-2">
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#myModal">E</button>
                        </span>

                        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModal" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Exclusão</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p>Confirme a exclusão da série {{ $serie->name }}</p>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Fechar</button>
                                        <form action="{{ route('series.destroy', $serie->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Confirmar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </span>
                @endauth
            </li>
        @endforeach
    </ul>
</x-layout>
