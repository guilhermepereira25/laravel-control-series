<form action="{{ $action }}" method="POST" @if (!$update) enctype="multipart/form-data" @endif>
    @csrf

    @if ($update)
        @method('PUT')
    @endif

    <div class="row mb-3">
        <div class="col-8">
            <label for="name" class="form-label">Nome</label>
            <input type="text" id="name" name="name" class="form-control" autofocus @isset($name) value="{{ $name }}" @endisset>
        </div>

        <div class="col-2">
            <label for="seasonsQuantity" class="form-label">N de temporadas</label>
            <input type="number" id="seasonsQuantity" name="seasonsQuantity" class="form-control" @isset($seasons) value="{{ $seasons }}" @endisset>
        </div>

        <div class="col-2">
            <label for="episodesQuantity" class="form-label">Eps / Temporada</label>
            <input type="number" id="episodesQuantity" name="episodesQuantity" class="form-control" @isset($episodes) value="{{ $episodes }}" @endisset>
        </div>
    </div>

    @if (!$update)
        <div class="row mb-3">
            <div class="col-12">
                <label for="cover" class="form-label">Capa</label>

                <input type="file" id="cover" name="cover" class="form-control">
            </div>
        </div>
    @endif

    <button type="submit" class="btn btn-primary">Salvar</button>
</form>
