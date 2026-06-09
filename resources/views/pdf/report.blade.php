<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 5px 0;
        }
        .meta {
            font-size: 10px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1 class="title">{{ $title }}</h1>
        <div class="meta">
            Date Generated: {{ now()->format('Y-m-d H:i A') }} &bull; 
            Filter Range: {{ request()->query('start_date', 'All') }} to {{ request()->query('end_date', 'All') }}
        </div>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                @foreach($data['headers'] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($data['rows'] as $row)
                <tr>
                    @foreach($row as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($data['headers']) ?: 1 }}" style="text-align: center; color: #94a3b8; padding: 20px;">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        {{ strtoupper($setting?->company_name ?? 'EDU INSTITUTE') }} MANAGEMENT SYSTEM &bull; CONFIDENTIAL ADMINISTRATIVE REPORT &bull; Page 1
    </div>
</body>
</html>
