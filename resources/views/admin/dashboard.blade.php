<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Trang chủ</title>
</head>
<body>
    <div style="display: flex; gap: 10px;">
        <div style="width: 24rem; border: 1px solid #ccc; border-radius: 0.25rem; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div style="padding: 1.25rem;">
                <h2 style="margin-bottom: 0.75rem; background-color: #ccc; text-align: center;">Chi nhánh</h2>
                <h1 id="branchCount" style="margin-bottom: 0; text-align: center;"></h1>
            </div>
        </div>
        <div style="width: 24rem; border: 1px solid #ccc; border-radius: 0.25rem; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div style="padding: 1.25rem;">
                <h2 style="margin-bottom: 0.75rem; background-color: #ccc; text-align: center;">Người dùng</h2>
                <h1 id="userCount" style="margin-bottom: 0; text-align: center;"></h1>
            </div>
        </div>
    </div>
</body>
<script>
    var userCountData = @json($userCount);
    var userCountElement = document.getElementById('userCount');
    userCountElement.textContent = userCountData;

    var branchCountData = @json($branchCount);
    var branchCountElement = document.getElementById('branchCount');
    branchCountElement.textContent = branchCountData;
</script>
</html>
