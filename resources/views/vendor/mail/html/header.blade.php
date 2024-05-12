@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
{{-- src="{{ asset('email_images/logo.png') }}" --}}
{{-- <img src="{{ url('/').'/'.request()->email_settings['merchant_settings']['logo']}}" class="logo" alt="Logo" style=""> --}}
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
