@component('mail::message')
# Down Services Detected

A new scan (ID: {{ $scanHistory->id }}) has completed, and some services are marked as **down**.

@component('mail::table')
| Service Name | Port | Protocol | Host ID |
|--------------|------|----------|---------|
@foreach($downServices as $service)
| {{ $service->name }} | {{ $service->port }} | {{ $service->protocol }} | {{ $service->host_id }} |
@endforeach
@endcomponent

Please investigate the issue promptly.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
