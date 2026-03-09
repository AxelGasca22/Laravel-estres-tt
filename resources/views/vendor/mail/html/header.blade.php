@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="./../../../../assets/Logo_Final_FullColor.jpg" alt="Logo vidazen" style="max-height: 50px; width: auto; display: block; margin: 0 auto;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
