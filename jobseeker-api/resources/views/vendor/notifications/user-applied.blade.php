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
<h3 class="card-title">{{ $job->job_title }}</h3>
<p class="card-subtitle">{{$job->country}} - {{$job->location}}</p>
<p class="card-subtitle">{{is_array($job->job_type) ? implode(", ", $job->job_type) : $job->job_type}}</p>
</div>

<h4 class="title-information-user">About the user applied</h4>
<ul class="information-user">
<?php $user_detail = $user_applied->resume->user_detail ?>
<li>Nama : {{ $user_detail->first_name . $user_detail->last_name }}</li>
<li>Alamat : {{ $user_detail->street_address ?? '' }}</li>
<li>Email : {{ $user_applied->email }}</li>
</ul>

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

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

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
