{{--
  Key/value details panel.
  $rows: [['label' => string, 'value' => string, 'emphasis' => bool]]
--}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 24px;background-color:#F4F6F1;border:1px solid #DDD6CC;border-radius:20px;">
    <tr>
        <td style="padding:20px 22px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                @foreach ($rows as $i => $row)
                    <tr>
                        <td style="padding:{{ $i === 0 ? '0' : '10px' }} 0 {{ $loop->last ? '0' : '10px' }};{{ $loop->last ? '' : 'border-bottom:1px solid #DDD6CC;' }}">
                            <p style="margin:0 0 2px;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:#A89886;">
                                {{ $row['label'] }}
                            </p>
                            <p style="margin:0;font-family:'Manrope',sans-serif;font-size:{{ ! empty($row['emphasis']) ? '18px' : '15px' }};font-weight:{{ ! empty($row['emphasis']) ? '800' : '600' }};color:{{ ! empty($row['emphasis']) ? '#3B4B31' : '#1C1815' }};">
                                {{ $row['value'] }}
                            </p>
                        </td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>
