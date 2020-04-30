@if(empty($repair))
    <div class="alert alert-danger">{{trans('login.no_repair')}}</div>
@else
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge">{{$repair->user->first_name}} {{$repair->user->last_name}}</span>
            {{trans('login.customer')}}
        </li>
        <li class="list-group-item">
            <span class="badge">{{$repair->id}} </span>
            {{trans('login.repair_number')}}
        </li>
        <li class="list-group-item">
            @if($repair->status=='pending')
                <span class="badge label-warning">{{ trans('repair.pending') }}</span>
            @endif
            @if($repair->status=='fixed')
                <span class="badge label-success">{{ trans('repair.fixed') }}</span>
            @endif
            @if($repair->status=='in_progress')
                <span class="badge label-info">{{ trans('repair.in_progress') }}</span>
            @endif
            @if($repair->status=='cancelled')
                <span class="badge label-danger">{{ trans('repair.cancelled') }}</span>
            @endif
            {{trans('login.status')}}
        </li>
    </ul>
@endif