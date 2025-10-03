@forelse($histories as $history)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td class="text-center">{{ $history->p3k->item }}</td>
        <td>{{ $history->npk }}</td>
        <td>
            @php
                $badgeColors = [
                    'add' => 'success',
                    'remove' => 'danger',
                    'restock' => 'primary',
                    'take' => 'warning',
                ];
            @endphp
            <span class="badge bg-{{ $badgeColors[$history->action] ?? 'secondary' }}">
                {{ ucfirst($history->action) }}
            </span>
        </td>
        <td>{{ $history->quantity }} pcs</td>
        <td>
            @if ($history->accident)
                <span>Victim:</span> {{ $history->accident->victim_name }}<br>
                <span>Victim NPK:</span> {{ $history->accident->victim_npk }}<br>
                <span>Accident:</span>
                @if ($history->accident->accident_other)
                    {{ $history->accident->accident_other }}
                @else
                    {{ $history->accident->masterAccident->name ?? '-' }}
                @endif
                <br>
                <span>Department:</span>
                {{ $history->accident->department->name ?? '-' }}<br>
            @else
                <em>No data</em>
            @endif
        </td>
        <td>{{ $history->updated_at->format('d-m-Y H:i') }}</td>
        <td>
            <form action="{{ route('p3k.transaction-history.destroy', $history->id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Are you sure to delete this history?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-muted text-center">No transaction history.</td>
    </tr>
@endforelse
