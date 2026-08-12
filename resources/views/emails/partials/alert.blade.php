{{-- $tone: success|warning|danger|info   $title   $body --}}
@php
    $tones = [
        'success' => ['bg' => '#F4F6F1', 'border' => '#CDD8C2', 'title' => '#2F3C28', 'body' => '#485C3A'],
        'warning' => ['bg' => '#FAF6F3', 'border' => '#E0CBB8', 'title' => '#6A4030', 'body' => '#824F38'],
        'danger'  => ['bg' => '#FEF2F2', 'border' => '#FECACA', 'title' => '#991B1B', 'body' => '#B91C1C'],
        'info'    => ['bg' => '#EEF9FE', 'border' => '#ADE3F9', 'title' => '#174D65', 'body' => '#1A8FBD'],
    ];
    $t = $tones[$tone ?? 'info'] ?? $tones['info'];
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;background-color:{{ $t['bg'] }};border:1px solid {{ $t['border'] }};border-radius:16px;">
    <tr>
        <td style="padding:16px 18px;">
            @isset($title)
                <p style="margin:0 0 4px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:700;color:{{ $t['title'] }};">
                    {{ $title }}
                </p>
            @endisset
            <p style="margin:0;font-family:'Inter',sans-serif;font-size:13px;line-height:20px;color:{{ $t['body'] }};">
                {!! $body !!}
            </p>
        </td>
    </tr>
</table>
