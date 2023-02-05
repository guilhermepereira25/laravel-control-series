<x-layout title="Series" :successMessage="$successMessage">
    <div class="container text-start">
        <div class="row align-items-center">
            <div class="col">
                @auth
                    <a href="{{ route('series.create') }}" class="btn btn-primary mb-3">Adicionar</a>
                @endauth
            </div>

            <div class="col-6">
                <p class="text-center fs-4">Sistema de Séries</p>
            </div>

            <div class="col">
                <nav aria-label="pagination series">
                    <ul class="justify-content-end">
                        {{ $series->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    </div>

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
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#myModal{{$serie->id}}">E</button>
                        </span>

                        <div class="modal fade" id="myModal{{$serie->id}}" tabindex="-1" aria-labelledby="myModal" aria-hidden="true">
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
