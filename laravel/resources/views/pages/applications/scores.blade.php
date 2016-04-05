@extends('app')

@section('content')
@can('view-submitted-application')
    <h1>score: <a href="/applications/{{ $application->id }}/review">{{ $application->name }}</a></h1>
    <hr>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Status</th>
                <th>Judge Status</th>
                <th>Score</th>
                <th>Created</th>
                <th>Last Modified</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $application->status }}</td>
                <td>{{ $application->judge_status }}</td>
                <td>{{ $application->objective_score }} / {{ $application->subjective_score }} / {{ $application->total_score }}</td>
                <td>{{ $application->created_at->format('Y-m-d H:i:s e') }}</td>
                <td>{{ $application->updated_at->format('Y-m-d H:i:s e') }}</td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table class="table table-hover"><thead>
        <thead>
            <tr>
            <th>Judge</th>
                @foreach($criteria as $criterion)
                    <th>C {{ $criterion->id }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($judges as $judge)
            <tr>
                <td><a href="/users/{{ $judge->id }}">{{ $judge->name }}</a></td>
                @foreach($criteria as $criterion)
                    <td>{{ $judgeScores[$judge->id][$criterion->id] }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
@endcan
@endsection