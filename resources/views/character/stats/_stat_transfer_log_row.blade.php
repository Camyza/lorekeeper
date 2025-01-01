<tr class="{{ $stat->recipient_id == $character->id ? 'inflow' : 'outflow' }}">
    <td><i class="btn py-1 m-0 px-2 btn-{{ $stat->quantity > 0 ? 'success' : 'danger' }} fas {{ $stat->quantity > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
        {!! $stat->sender ? $stat->sender->displayName : '' !!}
    </td>
    <td>{!! $stat->recipient ? $stat->recipient->displayName : '' !!}</td>
    <td>{{ $stat->quantity }}</td>
    <td>{!! $stat->log !!}</td>
    <td>{!! pretty_date($stat->created_at) !!}</td>
</tr>
