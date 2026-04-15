$data = Get-Content -Raw "route_list.json" | ConvertFrom-Json
$uris = @()
foreach($item in $data){
  if($item.method -like "GET*" -and $item.uri){
    $u = [string]$item.uri
    if($u.StartsWith('api/') ){ continue }
    if($u.StartsWith('storage/') ){ continue }
    if($u -eq 'up'){ continue }
    $uris += $u
  }
}
$uris = $uris | Sort-Object -Unique
$uris | Set-Content "webapp_pages.txt" -Encoding UTF8
Write-Output ("WROTE:" + $uris.Count)
