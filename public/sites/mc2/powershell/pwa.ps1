$sourceRoot = "C:\ampp\htdocs\ebooneTower2023\public\sites\mc2\content\M2"
$destinationRoot = "C:\ampp\htdocs\ebooneTower2023\public\pwa"

$filesToCopy = @(
    "add-to-homepage-ios.webp",
    "add-to-homepage-android.webp"
)

Get-ChildItem -Path $sourceRoot -Directory | ForEach-Object {

    $languageFolder = $_.Name
    $sourceImageFolder = Join-Path $_.FullName "images\standard"
    $destinationImageFolder = Join-Path $destinationRoot "$languageFolder\images\standard"

    foreach ($fileName in $filesToCopy) {

        $sourceFile = Join-Path $sourceImageFolder $fileName

        if (Test-Path $sourceFile) {

            if (-not (Test-Path $destinationImageFolder)) {
                New-Item -ItemType Directory -Path $destinationImageFolder -Force | Out-Null
            }

            Copy-Item `
                -Path $sourceFile `
                -Destination $destinationImageFolder `
                -Force

            Write-Host "Copied: $languageFolder\$fileName"
        }
        else {
            Write-Host "Missing: $languageFolder\$fileName"
        }
    }
}