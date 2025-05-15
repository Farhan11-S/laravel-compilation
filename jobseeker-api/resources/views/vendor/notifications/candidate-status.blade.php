<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}<br>
@endforeach

<div class="card-content">
<div>
<h3 class="card-title">{{ $job->job_title }}</h3>
<p class="card-subtitle">{{$job->country}} - {{$job->location}}</p>
<p class="card-subtitle">{{is_array($job->job_type) ? $job->job_type[0] : $job->job_type}}</p>
</div>
</div>

@if ($candidate->status == 'accepted')
<p>Congratulations, you have been {{$candidate->status}} in our company.</p>
@elseif ($candidate->status == 'rejected')
<p>Sorry, you have been {{$candidate->status}}.</p>
<p>Keep the spirit, someday there must be a way out.</p>
@endif

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>
{{ config('app.name') }}
@endif

<x-slot:subcopy>
<p>Thank you for using our application!</p>
</x-slot:subcopy>

</x-mail::message>
