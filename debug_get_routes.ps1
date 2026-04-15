$data = Get-Content -Raw 'route_list.json' | ConvertFrom-Json

$data |
    Where-Object { $_.method -like 'GET*' -and $_.uri -and $_.uri -like '*classes*' } |
    Select-Object -First 50 method,uri |
    ForEach-Object { "$($_.method) $($_.uri)" }

