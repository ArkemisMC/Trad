<?php
$defId = setting('trad.default_lang_id');
?>
@csrf
<div class="card-body">
    <div class="mb-3">
        <label class="form-label" for="dbInput">{{ trans('trad::admin.setting.per_page') }}</label>
        <input type="number" class="form-control @error('per_page') is-invalid @enderror" id="dbInput"
               name="per_page"
               value="{{ setting('trad.per_page') ?? 15 }}">

        @error('per_page')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="defaultLangId">{{ trans('trad::admin.setting.default_lang_id') }}</label>
        <select class="form-select " id="defaultLangId" name="default_lang_id">
            @foreach($langs as $lang)
                <option value="{{ $lang->id }}" @if($lang->id == $defId) selected @endif>{{ $lang->lang_name }}</option>
            @endforeach
        </select>

        @error('default_lang_id')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>