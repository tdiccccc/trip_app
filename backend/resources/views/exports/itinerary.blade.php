<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>伊勢旅行しおり</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #1a365d;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
        }
        .date-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .date-header {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            background-color: #edf2f7;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-left: 4px solid #4299e1;
        }
        .item {
            padding: 8px 12px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .item-time {
            font-weight: bold;
            color: #4299e1;
            font-size: 11px;
        }
        .item-title {
            font-size: 14px;
            font-weight: bold;
            margin: 4px 0;
        }
        .item-spot {
            font-size: 11px;
            color: #718096;
        }
        .item-memo {
            font-size: 11px;
            color: #4a5568;
            margin-top: 4px;
        }
        .item-transport {
            font-size: 10px;
            color: #a0aec0;
            margin-top: 2px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Trip App Itinerary</h1>

    @forelse($items as $date => $dayItems)
        <div class="date-section">
            <div class="date-header">{{ $date }}</div>
            @foreach($dayItems as $item)
                <div class="item">
                    @if($item['start_time'] || $item['end_time'])
                        <div class="item-time">
                            {{ $item['start_time'] ?? '' }}
                            @if($item['start_time'] && $item['end_time']) - @endif
                            {{ $item['end_time'] ?? '' }}
                        </div>
                    @endif
                    <div class="item-title">{{ $item['title'] }}</div>
                    @if($item['spot_id'] && isset($spots[$item['spot_id']]))
                        <div class="item-spot">{{ $spots[$item['spot_id']] }}</div>
                    @endif
                    @if($item['memo'])
                        <div class="item-memo">{{ $item['memo'] }}</div>
                    @endif
                    @if($item['transport'])
                        <div class="item-transport">Transport: {{ $item['transport'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <p style="text-align: center; color: #718096;">No itinerary items yet.</p>
    @endforelse

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
