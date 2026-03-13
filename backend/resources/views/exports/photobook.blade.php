<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Trip App Photobook</title>
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
        .photo-entry {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
        }
        .photo-image {
            text-align: center;
            margin-bottom: 10px;
        }
        .photo-image img {
            max-width: 100%;
            max-height: 400px;
        }
        .photo-placeholder {
            width: 100%;
            height: 200px;
            background-color: #edf2f7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a0aec0;
            font-size: 14px;
            border-radius: 4px;
            text-align: center;
            padding: 20px;
        }
        .photo-caption {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 4px;
        }
        .photo-meta {
            font-size: 11px;
            color: #718096;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .empty-message {
            text-align: center;
            color: #718096;
            padding: 40px;
        }
    </style>
</head>
<body>
    <h1>Trip App Photobook</h1>

    @forelse($photos as $photo)
        <div class="photo-entry">
            <div class="photo-placeholder">
                {{ $photo->originalFilename }}
            </div>
            @if($photo->caption)
                <div class="photo-caption">{{ $photo->caption }}</div>
            @endif
            <div class="photo-meta">
                @if($photo->takenAt)
                    Taken: {{ $photo->takenAt }}
                @endif
                &middot; {{ $photo->originalFilename }}
            </div>
        </div>
    @empty
        <p class="empty-message">No photos yet.</p>
    @endforelse

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
