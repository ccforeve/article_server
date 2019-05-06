<div class="btn-group" data-toggle="buttons">
    @foreach($options as $option => $label)
        <label class="btn btn-default btn-sm {{ \Request::get('message', 'all') == $option ? 'active' : '' }}">
            <input type="radio" class="user-message" value="{{ $option }}">{{$label}}
        </label>
    @endforeach
</div>
