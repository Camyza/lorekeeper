<tr class="inflow">
    <td>
        <i class="btn py-1 m-0 px-2 btn-success fas fa-arrow-up mr-2"></i>
    </td>
    <td>
        {!! $stat->stat->displayName !!}
    </td>
    <td>
        {{ $stat->previous_level }} &rarr; {{ $stat->new_level }}
    </td>
    <td>{!! pretty_date($stat->created_at) !!}</div>
</tr>
