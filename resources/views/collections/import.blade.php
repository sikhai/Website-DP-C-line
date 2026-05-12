<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Import Collections - Display Name</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f8f9fa; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .alert { padding: 10px; background-color: #d4edda; color: #155724; margin-bottom: 15px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { width: 100%; }
        button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Import Display Name cho Collections</h2>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <form action="{{ route('collections.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Chọn file Excel (.xlsx hoặc .xls):</label>
                <p><small>Cột yêu cầu: <strong>slug</strong>, <strong>display_name</strong></small></p>
                <input type="file" name="file" required accept=".xlsx,.xls">
            </div>
            <button type="submit">Xem trước dữ liệu</button>
        </form>

        @if(session('rows'))
            <h3>Dữ liệu đọc được:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Dòng</th>
                        <th>Slug</th>
                        <th>Display Name</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('rows') as $row)
                        <tr>
                            <td>{{ $row['row'] }}</td>
                            <td>{{ $row['slug'] }}</td>
                            <td>{{ $row['display_name'] }}</td>
                            <td>{{ $row['status'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
